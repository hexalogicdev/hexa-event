# Event Management System Revamp

## 1. Current-state assessment

The repository is a Laravel 11 application using Blade, Bootstrap/Vuexy, Vite, MySQL-style migrations, session authentication, `simple-qrcode`, and `html5-qrcode`. Public registration is disabled and accounts are provisioned by a super administrator. The current event workflow is a useful prototype, but it is not yet a complete production ticketing platform.

### Existing capabilities

- Super administrator, organizer, and security-officer account types.
- Organizer-owned events with draft/published and public/private fields.
- Free or ticketed events with basic ticket categories, price, quantity, and description.
- Public event discovery and registration forms.
- JSON-based custom registration fields.
- QR/reference generation and authenticated scanning.
- Basic attendee lists, CSV export, check-in metrics, and dashboards.

### Gap analysis

| Area | Current implementation | Required production state | Priority |
|---|---|---|---|
| Authorization | Role checks are duplicated in controllers; `role_id` and `user_type` can disagree | Central middleware plus policies; one authoritative role model; resource ownership tests | Critical |
| Account lifecycle | Login rejects inactive accounts | Every authenticated request must reject suspended accounts; auditable activation and suspension | Critical |
| Public security | Predictable attendee thank-you IDs and a public log-download route existed | Signed attendee links; no public operational files; rate limits and privacy-safe errors | Critical |
| Event ownership | Events require an organizer | Support organizer-owned and platform-managed events with explicit ownership rules | High |
| Event model | Basic name, dates, location, status, capacity | Slug, category, timezone, online/physical/hybrid location, currency, lifecycle, sales windows, unlimited capacity | High |
| Ticket inventory | Ticket category quantity counted from attendee rows | Reserved/sold/refunded inventory ledger with transactional holds and expiry | Critical for paid sales |
| Orders | One attendee row acts as registration and ticket | Order, order item, attendee, admission ticket, payment, refund, and invoice are separate records | Critical for paid sales |
| Payments | No gateway integration; unused Stripe-shaped column only | Provider-neutral payment service, hosted/secure checkout, verified webhooks, idempotency, refunds | Blocked pending provider approval |
| QR security | Ten-character code; database column is nullable/non-unique | Unique cryptographic ticket token, versioned payload/signature, revocation and reissue | Critical |
| Check-in | Check-in state is stored on attendee; organizer-wide officer access | Event assignment, atomic ticket check-in, duplicate/wrong-event/revoked outcomes, immutable attempts | Critical |
| Registration form | JSON fields with basic required support | Field definitions, ordering, validation, conditional rules, option sets, versioned response snapshots | Medium |
| Reporting | Basic totals and attendee CSV | Sales, tax/fees, refunds, ticket mix, conversion, attendance, officer activity, scheduled exports | Medium |
| Auditability | No audit log | Actor/action/subject/before/after/IP/user-agent audit records for privileged changes | High |
| Notifications | Legacy Dome Carlow mail class; no event confirmation flow | Queued confirmation, ticket delivery, reminders, cancellation/refund messages | High |
| Testing | Four workflow tests against the configured database | Isolated test DB, policy matrix, checkout/webhook/idempotency, inventory race and QR replay tests | Critical |
| Legacy code | Student/course/session models and extensive template demos remain | Quarantine after a reference audit; remove only through a separately reviewed cleanup | Low |

## 2. Target architecture

### Domain model

- `users`, `roles`: platform identities and authoritative roles.
- `organizer_profiles`: business details, billing identity, default currency/timezone.
- `events`, `event_categories`: event lifecycle and presentation.
- `event_staff`: many-to-many event assignments for security officers and future staff roles.
- `ticket_types`: price, currency, sales window, min/max per order, capacity, active state.
- `orders`: purchaser, totals, currency, status, idempotency key, expiry.
- `order_items`: immutable ticket price/name snapshots and quantities.
- `attendees`: person/contact and form responses, optionally linked to an order item.
- `tickets`: one admission credential per admission, unique token hash, status and reissue lineage.
- `payments`, `payment_events`, `refunds`: provider-neutral financial records and webhook history.
- `check_in_attempts`: every accepted or rejected scan, event, officer, reason, device context, timestamp.
- `audit_logs`: immutable privileged-action history.

### Application boundaries

- `app/Policies`: ownership and role authorization for users, events, orders, attendees, and tickets.
- `app/Http/Requests`: validation per create/update/checkout/check-in operation.
- `app/Services/Events`: event lifecycle, ticket configuration, publishing readiness.
- `app/Services/Inventory`: reservations, expiry, commit, release, and capacity calculations.
- `app/Services/Checkout`: order creation and free/paid completion.
- `app/Services/Payments`: provider contract and approved adapter.
- `app/Services/Tickets`: secure token issue, reissue, revoke, and QR payload generation.
- `app/Services/CheckIn`: atomic scan decision and attempt recording.
- `app/Actions` or queued jobs: confirmations, reminders, exports, and hold expiry.

Controllers should coordinate requests only. Business invariants belong in services and database constraints. Every payment webhook and checkout mutation must be idempotent.

## 3. UI/UX information architecture

### Public

- Home: focused value proposition, featured/upcoming events, search and filters.
- Event catalogue: category/date/location/price filters, pagination, empty states.
- Event detail: banner, schedule, venue, organizer, ticket availability, accessible purchase CTA.
- Registration/checkout: ticket selection, attendee details, custom fields, order summary, payment handoff.
- Confirmation: signed access, order reference, individual tickets/QRs, receipt and calendar action.

### Super administrator

- Overview, organizers, all events, orders/payments, refunds, reports, audit log, platform settings.
- White workspace, persistent role-aware sidebar, compact metric cards, searchable tables, explicit status badges.

### Organizer

- Overview, events, ticket types, registration form, orders/attendees, staff assignments, check-in activity, reports.
- Event setup should be a clear staged editor: basics, schedule/location, tickets/capacity, form, staff, publish review.

### Security officer

- Scanner-first authenticated workspace only.
- Assigned-event selector, camera permission state, manual reference fallback, large accepted/rejected result, recent local scan history.
- Explicit outcomes: accepted, already checked in, wrong event, cancelled/refunded/revoked, invalid, and offline/connection failure.

## 4. Delivery roadmap

### Phase A — security and access foundation

Files: route definitions, authentication middleware, policies, user/role models, feature tests.

Acceptance: registration remains disabled; suspended sessions are terminated; security officers cannot access management screens; operational logs are not public; attendee confirmation pages require signed links.

### Phase B — normalized event and staffing domain

Migrations: organizer profiles, event fields/categories, event-staff assignments, ticket-type extensions, indexes and constraints.

Acceptance: super administrators can create platform or organizer events; organizers only manage owned resources; officers scan only explicitly assigned events.

### Phase C — orders, inventory, and free checkout

Migrations: orders, order items, tickets, inventory reservations. Services: inventory and checkout.

Acceptance: multiple quantities/categories per order, one secure ticket per admission, atomic capacity enforcement, expiring holds, repeat-safe order submission.

### Phase D — approved payment integration

Migrations: payments, payment events, refunds. Services: payment contract and provider adapter. Routes: authenticated provider webhook with signature verification.

Acceptance: no client-trusted totals; successful webhook completes an order once; failed/expired payment releases inventory; partial/full refunds revoke applicable tickets and update reports.

Dependency: the payment provider and operating currency/countries must be approved before this phase is implemented.

### Phase E — check-in and audit trail

Migrations: check-in attempts and audit logs. Services: ticket token and check-in decision engine.

Acceptance: cryptographically strong unique tokens; atomic duplicate protection; wrong-event and eligibility validation; every scan result recorded with officer/event/reason.

### Phase F — professional product UI and reporting

Files: role layouts/navigation, dashboard queries, public pages, event setup screens, data tables, scanner, reporting/export jobs.

Acceptance: responsive and accessible screens, consistent white management UI, server-side pagination/filtering, accurate financial and attendance reports, actionable empty/error states.

### Phase G — legacy cleanup and production readiness

Candidates: student/course/class-session domain and unused Vuexy demo pages/assets. These are not removed until route/reference checks and a reviewed deletion list prove they are unused.

Acceptance: isolated test database, queue/scheduler deployment notes, backup/restore plan, error monitoring, security headers, rate limits, data retention, privacy export/deletion process.

## 5. Test strategy

- Unit tests: money calculations, event lifecycle, inventory, QR token parsing, permission decisions.
- Feature tests: full role matrix; event CRUD ownership; free checkout; signed confirmations; officer assignments; all scanner outcomes.
- Integration tests: approved gateway signatures, webhook replay/out-of-order delivery, refunds, mail/queue jobs.
- Concurrency tests: final event/ticket capacity, two simultaneous scans, hold expiry versus payment success.
- Browser tests: responsive public discovery and checkout, camera/manual scanner fallback, management table filters.
- Security tests: IDOR, mass assignment, CSRF, rate limiting, file upload validation, formula-safe CSV export, disabled-account sessions.

No frontend production build is run by the implementation agent; the project owner runs it when requested.
