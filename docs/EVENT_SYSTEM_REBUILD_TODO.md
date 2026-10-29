# Event Management Platform Renovation Roadmap

Last updated: 2026-10-29

This is the authoritative roadmap and handoff record for the renovation. Read this file, inspect the Git worktree, and verify migration/test status before starting or resuming work.

Task states: `[ ]` not started, `[~]` in progress, `[x]` completed and verified, `[!]` blocked.

## 1. Project objective

Renovate the existing Laravel event-booking application into a secure, maintainable, multi-organization event-management platform while preserving working behavior and production data. The target includes professional public, authenticated, and security-operation experiences; centralized authorization and design foundations; robust registration/ticket/payment workflows; accurate analytics; and incremental, reversible delivery.

Phase 0 is documentation and verification only. No application behavior or schema is changed by Phase 0.

## 2. Current architecture summary

- Laravel 11 on PHP `^8.2`, server-rendered Blade, Laravel UI authentication, Bootstrap 5/Vuexy assets, Vite, and MySQL-style migrations.
- Web-only application: 52 web routes and no `routes/api.php` currently exist.
- Authentication uses Laravel sessions. Public account registration is disabled; password reset remains available.
- Active roles are `superadmin`, `organizer`, and `security_officer`. Access is enforced through route middleware plus repeated controller/ownership checks.
- An event belongs directly to one organizer user through `events.user_id`; there is no organization tenant.
- Public registration creates `orders`, `order_items`, `attendees`, and `tickets` inside a database transaction.
- Security officers belong to an organizer and may be assigned to events through `event_security_officer`.
- QR validation records attempts in `check_in_attempts` and uses a locked attendee row for successful check-in.
- UI composition is split across Vuexy master layouts, front/authenticated layouts, JSON menu configuration, compiled template CSS, custom CSS, and page-local styles.
- Existing focused workflow suite contains seven feature tests covering registration disablement, basic QR flow, officer isolation, layout rendering, suspension, assignment/audit behavior, and idempotent free registration.

## 3. Existing functionality inventory and classification

| Area | Existing behavior | Decision |
|---|---|---|
| Authentication | Login, logout, password reset; public registration disabled | Keep, extend with invitations and verified onboarding |
| Roles | Three role records, `user_type`, role middleware | Refactor into centralized permissions |
| Organizer onboarding | Superadmin directly creates organizer credentials | Replace plaintext/manual credential flow with expiring invitation while preserving existing accounts |
| Event ownership | Event belongs to organizer user | Extend through backward-compatible organization tenancy |
| Event CRUD | Create/edit/show/delete, draft/published strings, public/private visibility | Refactor lifecycle, policies, and safe archival |
| Event configuration | Dates, location, capacity, image, tickets, custom fields | Extend into wizard, modes, venue/timezone/policy configuration |
| Registration | Public form, availability checks, custom answers | Keep core behavior; refactor validation and modes |
| Capacity | Transaction and row locks, event/ticket counts | Keep approach; improve inventory/reservation model and concurrency tests |
| Orders/tickets | Order, item, attendee, ticket records and idempotency key | Keep and normalize lifecycle semantics |
| Paid tickets | Prices exist but orders are marked completed/not-required | Block production paid flow until provider and financial rules are approved |
| Attendees | Event list, search, status filter, CSV export, manual check-in | Extend; add pagination, privacy controls and lifecycle actions |
| Security staff | Create/suspend/delete, organizer ownership, event assignments | Keep and extend with organization membership, gates and permissions |
| QR check-in | Camera/manual entry, authorization, attempt audit, locked check-in | Keep core; refactor to a dedicated operational shell and strengthen concurrency |
| Dashboards | Basic KPIs/recent records | Replace collection-heavy calculations with defined aggregate queries |
| Reports | Basic attendee/check-in report and CSV | Extend with accurate financial/operational definitions and bounded exports |
| Notifications | Mail class exists, but no complete queued notification lifecycle | Replace with centralized queued notifications and delivery logs |
| Public website | Homepage, public registration, thank-you and legal page | Rebuild on centralized public layout |
| Authenticated UI | Vuexy shell with custom light-theme overrides | Refactor into centralized authenticated design system |
| Security UI | Scanner hides primary sidebar | Replace with dedicated mobile-first security layout |
| Legacy school domain | Course/student/class-session models and scheduler remain | Deprecate after dependency/data audit; do not delete during early phases |

## 4. Identified problems

### Architecture and domain

- A user is treated as the organizer/tenant; multiple organizations and multi-organization membership are unsupported.
- `role_id` and `user_type` duplicate role identity and may drift.
- Authorization is distributed across routes and controllers; no Policies or organization-aware query scopes exist.
- Statuses are free-form strings and valid event/order/ticket transitions are not centralized.
- Controllers combine authorization, validation, querying, storage and business workflows.
- Empty resource methods and legacy school-domain code remain in active application namespaces.

### Data and workflow

- Paid ticket prices do not represent confirmed payments; current registrations still complete orders with `payment_status=not_required`.
- Event deletion cascades may destroy operational records; deletion/archive semantics need redesign before tenant/event migrations.
- Ticket availability uses count queries under locks rather than a reservation ledger; payment expiration is not implemented.
- Registration supports one ticket/attendee per submission rather than quantity-based, multi-attendee checkout.
- Dates are inconsistent (`date` booking windows versus event datetimes) and organization/event timezone semantics are absent.
- Dynamic custom-field file validation, retention and authorization require hardening.
- CSV exports load all attendees and are unbounded.

### Performance

- Event and attendee listings use unpaginated `get()` calls.
- Dashboard totals rely partly on cloned attendee queries and need definition/query review.
- Some relationship checks can trigger repeated authorization queries.
- Reporting/export paths are synchronous and unbounded.
- Required indexes must be derived from real query plans before schema changes.

### Security and privacy

- No centralized tenant boundary exists.
- Organizer creation currently accepts a password rather than using a single-use invitation.
- Public registration lacks an explicit rate-limit/abuse strategy.
- QR/gate supervisor override rules do not exist.
- Audit coverage is limited mainly to check-in attempts.
- Data-retention, erasure, legal retention and export-audit policies are undefined.

### Frontend and design

- Layout responsibility is split among several master layouts and CSS layers.
- High-specificity legacy theme rules require overrides and make components inconsistent.
- Reusable event-platform components are limited; page-level markup/styles are repeated.
- Public Vuexy demo navigation/footer assets and template metadata remain.
- Scanner is large and page-specific rather than a focused operational component.

## 5. Target architecture

### Domain structure

```text
Organization
  -> Memberships -> Users
  -> Event quota -> immutable adjustments -> allowance requests
  -> Events
       -> Ticket types/inventories
       -> Registration forms
       -> Orders -> Order items
       -> Attendees -> Tickets
       -> Staff assignments
       -> Gates -> scheduled security assignments -> Check-in attempts
       -> Sessions/speakers/sponsors (later)
  -> Invitations
  -> Settings/branding
```

### Application layers

- Form Requests for validation.
- Policies and permission middleware for authorization.
- Organization context and scoped query builders for isolation.
- Enums/state transition services for lifecycle rules.
- Actions/services for organization onboarding, publishing, registration, payment, ticket issue and check-in.
- Transactions for inventory, payment state, ticket issuance and check-in.
- Queued notifications and exports.
- Blade components backed by centralized design tokens.

### Proposed source structure

```text
app/Actions/{Organizations,Events,Registrations,Orders,Payments,CheckIn}
app/Enums
app/Http/Requests/{Organizations,Events,Registrations,CheckIn}
app/Jobs
app/Models
app/Notifications
app/Policies
app/Queries
app/Services
resources/views/components/ui
resources/views/layouts/{public,dashboard,security}
resources/views/{public,dashboard,security,organizations,events,attendees,orders,reports}
resources/css/{tokens,components,layouts,pages}
tests/Feature/{Organizations,Authorization,Events,Registration,Payments,CheckIn,Reports}
```

## 6. Database changes and migration strategy

1. Inspect representative production schema, row counts, foreign keys, indexes, orphaned rows and status values before every schema phase.
2. Add new tables/nullable foreign keys first; do not rename or drop legacy columns in the same release.
3. Introduce `organizations`, `organization_user`, invitations, and organization roles/permissions behind compatibility mappings.
4. Create an organization for every existing organizer and backfill event/staff ownership in an idempotent command or migration.
5. Dual-read/dual-write where necessary until backfill and verification are complete.
6. Add constraints only after duplicate/orphan audits and data cleanup plans.
7. Preserve `events.user_id` during the compatibility window.
8. Treat payments/refunds/reservations as new additive tables. Never infer successful payments from prices or legacy amounts.
9. Replace destructive event deletion with archive/cancel rules before changing foreign-key behavior.
10. Document backup, dry-run, backfill metrics, verification queries and rollback/recovery for every production migration.

### High-risk migrations

- Organizer-user ownership to organization tenancy.
- Role/user-type reconciliation and permission backfill.
- Event status normalization without changing live registration visibility.
- Existing paid-looking attendee amounts/orders that lack verified payments.
- Event deletion/cascade semantics.
- Timezone conversion of existing dates.
- Ticket inventory/reservation backfill.

## 7. Complete module list

- Authentication and invitations
- Organizations, memberships and branding
- Roles, permissions and tenant context
- Platform administration
- Organizer/team management
- Event lifecycle and publishing
- Venues, online events, gates and checkpoints
- Event wizard and form builder
- Registration modes and approvals
- Ticket catalog, inventory and reservations
- Orders, payments, refunds and settlements
- Attendees and booking management
- Ticket issuance/delivery/transfer
- Security operations and QR check-in
- Notifications and delivery logs
- Public discovery, event details and SEO
- Dashboards, analytics and reports
- Sessions, speakers, sponsors and announcements
- Support tickets and platform settings
- Audit, privacy and data retention
- Performance, monitoring and release readiness
- Commercial event allowances, adjustments and requests
- Security-team scheduling, assignment acknowledgements and performance

## 8. Finalized implementation phases

The master sequence is retained with two refinements: tenant/authorization foundations precede broad UI conversion of protected modules, and payment work remains blocked until provider/business approval.

### Phase 0 — Audit and roadmap

- [x] `P0-AUD-001` Inventory framework, routes, controllers, models, migrations, layouts, assets and tests. Dependencies: none. Files changed: this roadmap only. Tests: migration status and baseline suite tracked below.
- [x] `P0-AUD-002` Classify existing modules as keep/refactor/extend/replace/deprecate. Dependencies: P0-AUD-001. Verification: recorded in section 3.
- [x] `P0-DOC-001` Create authoritative roadmap, migration strategy, acceptance criteria and handoff. Dependencies: P0-AUD-001.
- [x] `P0-VER-001` Establish baseline tests and repository state. Dependencies: P0-AUD-001. Verification: `EventPlatformWorkflowTest` passed 7 tests/46 assertions; migrations all reported `Ran`; `git diff --check` passed.
- [ ] `P0-DATA-001` Capture representative production-safe schema/data profile before Phase 2 migrations. Dependencies: production/staging access approval. Do not export personal data.

### Phase 1 — UI foundation

- [x] `P1-UI-001` Define initial restrained indigo/slate CSS design tokens. Files: `public/assets/css/event-platform.css`. Verification: CSS loaded by both authenticated and public foundations; Blade cache passed.
- [x] `P1-UI-002` Build reusable page-header, card, stat-card, status and empty-state Blade components. Files: `resources/views/components/ui/`. Verification: reference views compiled and focused suite passed.
- [x] `P1-UI-003` Centralize public layout foundation and convert the public home reference page. The event-discovery design, database-backed cards and search filters are implemented and automated checks pass; the user authorized continuation of the full design renovation.
- [x] `P1-UI-004` Centralize authenticated layout and navigation-access foundation. Files: `resources/views/layouts/dashboard.blade.php`, `app/Support/NavigationAccess.php`, menu and styles includes. Verification: organizer sidebar/dashboard assertion passed. Current roles are compatibility inputs until Phase 2 permissions exist.
- [x] `P1-UI-005` Centralize mobile-first security shell foundation and move scanner reference page onto it. Files: `resources/views/layouts/security.blade.php`, `resources/views/checkin/scan.blade.php`. Verification: security shell rendered without organizer sidebar.
- [x] `P1-UI-006` Convert dashboard, event list and organizer list as reference pages. Verification: Blade cache and focused workflows passed.
- [~] `P1-UI-007` Responsive, keyboard and contrast verification. Source includes responsive breakpoints/focus-visible states and semantic reference components; authenticated browser/device and formal contrast verification remain outstanding.

#### Public design consistency checklist

- [x] Homepage uses the approved indigo/blue/slate direction with real event data and functional search/navigation/CTAs.
- [ ] Event listing uses the same navigation, spacing, filters, cards, states and pagination language.
- [ ] Event details use the same hero, metadata, organizer, venue, ticket and CTA hierarchy.
- [ ] Organizer profile uses shared public cards, identity and event-list components.
- [ ] Ticket selection uses shared ticket cards, availability, pricing and action states.
- [ ] Registration forms use shared inputs, validation, progress and policy presentation.
- [ ] Checkout uses shared order summary, payment, loading, failure and confirmation states.
- [ ] Booking confirmation uses shared success, ticket and next-action components.
- [ ] Manage booking uses shared search, identity verification, ticket and cancellation states.
- [ ] Privacy/legal pages use the public shell, readable content width and consistent navigation/footer.
- [ ] No fabricated counts, testimonials, transactions, trust claims or unsupported functionality.

#### Authenticated design consistency checklist

- [ ] Superadmin dashboard uses shared headers, cards, filters, charts, tables and empty/loading states.
- [ ] Organization management uses shared list/detail/form/audit components.
- [ ] Event allowance management uses shared quota meters, request states, approval dialogs and history tables.
- [ ] Organizer dashboard uses the same dashboard grid and role-scoped navigation system.
- [ ] Event management uses shared wizard, status, forms, tables and confirmation dialogs.
- [ ] Security team management uses shared staff, assignment, gate and shift components.
- [ ] Security officer dashboard uses the dedicated mobile-first operational shell.
- [ ] Reports use shared date filters, metric definitions, charts, exports and empty states.

### Phase 2 — Organizations and permissions

- [x] `P2-ORG-001` Finalize organization ownership/membership decisions and existing-data profile. Decision: users may belong to multiple organizations with an organization-specific role; one owner is recorded per organization. Local profile: one organizer, one event and one linked security officer before backfill.
- [x] `P2-ORG-002` Add organizations, memberships and invitation schema additively. Migration: `2026_10_29_000010_create_organization_foundation.php`; legacy `events.user_id` remains for compatibility.
- [x] `P2-ORG-003` Implement idempotent legacy organizer/event backfill with dry-run reporting. Command: `php artisan organizations:backfill --dry-run`; post-migration dry-run reported zero pending changes.
- [x] `P2-AUTH-001` Evaluate Spatie Permission against current role model; record adopt/reject decision before installation. Decision: use native Laravel Policies and organization memberships for this additive phase; do not add a package migration/dependency during compatibility cutover.
- [x] `P2-AUTH-002` Implement organization context, Policies and tenant-scoped queries. Organization and Event Policies enforce membership roles; event list/show/update queries retain a legacy-owner fallback.
- [x] `P2-AUTH-003` Implement expiring single-use owner/staff invitations; never email plaintext passwords. Tokens are random, only SHA-256 hashes are stored, expiry is 72 hours, and existing-user passwords are preserved.
- [x] `P2-AUTH-004` Add organization suspension and archival rules. Only superadmins change lifecycle state; non-active organizations are excluded from active membership access. Published-event suspension effects remain an explicit business decision.
- [x] `P2-AUTH-005` Add isolation, invitation, role and suspension feature tests. Verification: `OrganizationFoundationTest` passed 7 tests/33 assertions.
- [x] `P2-ORG-004` Preserve every existing organizer-owned event by mapping its owner to the backfilled organization and verify ownership totals before cutover. Migration and dry-run completed with zero unmapped/pending event changes.
- [x] `P2-AUTH-006` Build permission-aware navigation from centralized abilities while retaining a compatibility mapping for current roles. Organizations navigation uses `organizations.viewAny`; existing role navigation remains unchanged.

### Phase 3A — Commercial event allowances

- [x] `P3-QUO-001` Add auditable organization quota storage without redundant or client-controlled counters. Allocation is the sum of immutable adjustments; consumption is the count of organization events.
- [x] `P3-QUO-002` Add immutable quota adjustments recording administrator, signed quantity, reason, date, notes and optional agreement reference. Superadmin-only adjustment UI and history are available on the organization detail page.
- [x] `P3-QUO-003` Enforce event allowance transactionally in every current event-creation path. Drafts consume allowance; edit, publish and later lifecycle changes do not alter consumption.
- [~] `P3-QUO-004` Add idempotent event-creation protection and concurrency tests so simultaneous requests cannot exceed allowance. UUID retry protection and organization-row locking are implemented and focused tests pass; a true simultaneous MySQL integration test remains required.
- [x] `P3-REQ-001` Add organizer allowance requests with pending, under-review, approved, rejected and cancelled states.
- [x] `P3-REQ-002` Implement single-processing approval/rejection, positive override quantity, internal notes, immutable adjustment and organizer notification.
- [x] `P3-REQ-003` Add organizer request/history pages and quota widget showing allocated, consumed, remaining and utilization.
- [x] `P3-ADM-001` Add superadmin quota overview, pending requests and adjustment history with organization isolation.
- [~] `P3-TEST-001` Test within-limit creation, exhaustion, concurrency, duplicate creation, unauthorized adjustments and duplicate approvals. All listed behavior except true competing MySQL transactions is covered locally.

### Phase 4A — Organization security team and scheduling

- [ ] `P4-SEC-001` Extend organization memberships for security staff; do not introduce a duplicate user/security identity domain.
- [ ] `P4-GATE-001` Add organization-scoped venues, gates and checkpoints required by assignments.
- [ ] `P4-ASG-001` Replace the simple event/officer pivot through a backward-compatible scheduled assignment model recording event, gate, shift, responsibility, status, confirmation and assigner.
- [ ] `P4-ASG-002` Backfill current event security assignments without losing access and retain compatibility during cutover.
- [ ] `P4-ASG-003` Add assigned, confirmed, declined, cancelled and completed transitions; never infer physical attendance from time alone.
- [ ] `P4-ASG-004` Detect conflicting active shifts in the event timezone and provide a clear validation result.
- [ ] `P4-ASG-005` Queue assignment and assignment-change notifications.
- [ ] `P4-OFF-001` Build mobile-first officer dashboard for today, upcoming and completed assignments plus assignment acknowledgement/details.
- [ ] `P4-SCAN-001` Enforce active membership, assignment, event, gate, operational window and ticket status server-side before scanning.
- [ ] `P4-SCAN-002` Attribute scans to officer, event, gate and assignment; preserve atomic duplicate prevention and audit history.
- [ ] `P4-SCAN-003` Add permissioned supervisor override with reason and audit history.
- [ ] `P4-TEST-001` Test officer permissions, cross-organization assignments, shift conflicts, assigned/unassigned access, gate authorization, operating windows and duplicate check-in.

### Phase 3B — Core event management (after organization/quota foundation)

- [ ] `P3-EVT-001` Define event lifecycle, visibility and registration-state separation.
- [ ] `P3-EVT-002` Add enums, transition service, policy and audit trail.
- [ ] `P3-EVT-003` Build event wizard with draft persistence and publish validation.
- [ ] `P3-EVT-004` Add venue/online-event and timezone configuration.
- [ ] `P3-EVT-005` Add capacity, sales-window and per-order rules.
- [ ] `P3-EVT-006` Replace unsafe deletion with archive/cancel semantics.
- [ ] `P3-EVT-007` Build event dashboard foundation using defined metrics.

### Phase 5 — Registration and ticketing

- [ ] `P4-REG-001` Model three registration modes explicitly.
- [ ] `P4-REG-002` Refactor custom forms, validation, approvals and file handling.
- [ ] `P4-TKT-001` Support free-ticket quantity, attendee assignment and inventory.
- [ ] `P4-TKT-002` Add booking lookup, resend and download workflows.
- [ ] `P4-ATT-001` Build paginated attendee management with filters and privacy controls.
- [ ] `P4-INV-001` Implement reservation/expiration rules and concurrency tests.
- [ ] `P4-NOT-001` Queue registration/ticket notifications with deduplication and logs.

### Phase 6 — Paid checkout

- [!] `P5-PAY-001` Select payment provider, countries/currencies, fee/tax/refund and settlement rules. Blocked on business approval.
- [ ] `P5-PAY-002` Add payment, webhook, refund, reservation and reconciliation schema after approval.
- [ ] `P5-PAY-003` Implement pending order -> confirmed payment -> exactly-once ticket issuance.
- [ ] `P5-PAY-004` Implement signed/idempotent webhooks and retry handling.
- [ ] `P5-PAY-005` Implement full/partial refunds and ticket invalidation policy.
- [ ] `P5-PAY-006` Add financial reconciliation and accuracy tests.

### Phase 7 — Security and check-in completion

- [ ] `P6-SEC-001` Add venues/gates/checkpoints and event assignments.
- [ ] `P6-SEC-002` Refactor scanner into dedicated security layout and reusable scanner component.
- [ ] `P6-SEC-003` Implement atomic, idempotent check-in result service.
- [ ] `P6-SEC-004` Add unauthorized-gate, cancelled/refunded and wrong-event handling.
- [ ] `P6-SEC-005` Add permissioned supervisor override with reason/audit trail.
- [ ] `P6-SEC-006` Define offline strategy before claiming offline support.
- [ ] `P6-SEC-007` Add simultaneous-scan and authorization tests plus latency measurement.

### Phase 8 — Public website

- [ ] `P7-WEB-001` Homepage, browse, categories and organizer/venue profiles.
- [ ] `P7-WEB-002` Professional event detail, schedule, FAQs, policy and CTA.
- [ ] `P7-WEB-003` Registration/ticket/checkout and confirmation presentation.
- [ ] `P7-WEB-004` Manage-booking, support and legal pages.
- [ ] `P7-WEB-005` SEO metadata, schema, canonical and social previews.
- [ ] `P7-WEB-006` Responsive, accessibility and performance verification.

### Phase 9 — Dashboards and reports

- [ ] `P8-MET-001` Create metric dictionary distinguishing registrations, attendees, tickets, orders, payments, revenue and check-ins.
- [ ] `P8-DASH-001` Superadmin dashboard with scoped aggregate queries.
- [ ] `P8-DASH-002` Organization dashboard with tenant isolation.
- [ ] `P8-DASH-003` Event analytics dashboard.
- [ ] `P8-DASH-004` Security operations dashboard.
- [ ] `P8-RPT-001` Date/filter controls, bounded exports and accuracy tests.
- [ ] `P8-PERF-001` Measure queries/latency; add caching only with invalidation rules.
- [ ] `P8-QUO-001` Add real-data quota KPIs and charts for allocation, consumption, remaining capacity, requests and near-limit organizations.
- [ ] `P8-SEC-001` Add organizer security staffing gaps and officer schedule/check-in performance metrics.

### Phase 10 — Additional operations

- [ ] `P9-OPS-001` Sessions and session attendance model.
- [ ] `P9-OPS-002` Speakers, sponsors and announcements.
- [ ] `P9-SUP-001` Support-ticket workflow and activity history.
- [ ] `P9-SET-001` Platform/organization settings and notification templates.
- [ ] `P9-AUD-001` Central audit viewer and privacy/retention administration.

### Phase 11 — Optimization and release readiness

- [ ] `P10-PERF-001` Representative endpoint/query benchmarks and index review.
- [ ] `P10-SEC-001` Authentication, authorization, tenant, upload, QR and payment security review.
- [ ] `P10-A11Y-001` Accessibility and responsive review.
- [ ] `P10-TEST-001` Full regression, concurrency and migration rehearsal.
- [ ] `P10-REL-001` Backup, deployment, rollback, queue, scheduler and monitoring checklist.
- [ ] `P10-DOC-001` Final documentation and roadmap reconciliation.

## 9. Dependencies between tasks

- Design tokens precede component/page renovation.
- Organization decisions and data profiling precede organization migrations.
- Tenant context and Policies precede broad protected-module expansion.
- Trustworthy organization ownership and authorization precede every quota operation.
- Transactional quota storage and adjustment history precede allowance requests and event enforcement.
- Stable organization staff/event relationships and event timezone semantics precede security scheduling.
- Gates and scheduled assignments precede scanner gate/window enforcement.
- Event lifecycle definitions precede wizard/publishing/dashboard work.
- Registration-mode definitions precede ticket inventory and checkout.
- Provider and financial-rule approval precede any production payment integration.
- Gates and assignments precede gate-restricted scanning/reporting.
- Metric definitions precede dashboards and financial reports.
- Representative data and backup/recovery plans precede production backfills.

## 10. Expected file paths by phase

- Phase 0: `docs/EVENT_SYSTEM_REBUILD_TODO.md`, existing tests/config read-only.
- Phase 1: `resources/css/`, `resources/js/`, `resources/views/layouts/`, `resources/views/components/`, `resources/menu/`, selected views.
- Phase 2: organization/membership/invitation models, migrations, Policies, Requests, middleware, onboarding views and tests.
- Phase 3: Event model/controller replacement layers, event Requests/Policies/Actions/Enums, wizard views, migrations and tests.
- Phase 4: registration/order/ticket/attendee Actions, Requests, views, notifications, jobs and tests.
- Phase 5: payment/refund/reservation models, services, webhook routes/controllers, jobs and tests.
- Phase 6: gate/check-in models, services, security layout/views/JS, migrations and tests.
- Phase 7: public controllers/queries/layout/views/components/SEO tests.
- Phase 8: query objects, dashboard/report controllers/views/charts/exports/tests.
- Phase 9: operational models/modules/views/tests.
- Phase 10: configuration, indexes, runbooks, deployment documentation and regression tests.

## 11. Acceptance criteria

Global criteria for any completed task:

- Existing authorized behavior remains intact unless an approved change says otherwise.
- Authorization is enforced server-side and tenant isolation is tested.
- Schema changes are additive/backward-compatible or include an approved recovery plan.
- Relevant automated tests pass; failures/limitations are recorded.
- UI changes use centralized components/tokens and are responsive/accessibility-reviewed.
- Financial metrics use confirmed payment facts only.
- Performance claims include before/after evidence.
- This roadmap records files, migrations, tests, results and outstanding issues.

Phase gates:

- Phase 1: three shells and reference pages render consistently across target breakpoints.
- Phase 2: existing accounts/events are backfilled without cross-tenant access or data loss.
- Phase 3: invalid event transitions/publishing are rejected and audited.
- Phase 4: all three registration modes enforce capacity and duplicate protection.
- Phase 5: tickets issue exactly once only after verified payment; retries/refunds reconcile.
- Phase 6: only one simultaneous scan succeeds; every attempt is auditable.
- Phase 7: private/unpublished events remain inaccessible and public flows are responsive.
- Phase 8: metric definitions and report totals reconcile with source records.
- Phase 10: migration rehearsal, regression, security, accessibility and rollback checks pass.

## 12. Testing requirements

- Preserve and run `tests/Feature/EventPlatformWorkflowTest.php` after relevant changes.
- Add feature tests for tenant isolation, permissions, invitations, event transitions, three registration modes, concurrency, webhook retries/refunds, attendee privacy, gate restrictions and report accuracy.
- Add unit tests for enums/state transitions and pure pricing/inventory calculations.
- Use database transaction/concurrency tests where correctness depends on locks.
- Review browser behavior for responsive layouts, keyboard navigation, camera permissions and scanner lifecycle.
- Never describe source-level checks as deployed/browser/payment-provider proof.
- Do not run `npm run build` unless the user explicitly authorizes it; ask the user to run it when required.

## 13. Risks and unresolved decisions

- Existing production record counts/status values and orphan/duplicate data have not been profiled in this Phase 0 local review.
- Organization ownership: whether one user may own multiple organizations and whether ownership transfer is required.
- Membership: whether a user may hold different roles across multiple organizations (recommended: yes).
- Superadmin access: support/impersonation rules and audit requirements.
- Organization approval and suspension effects on published events and attendee access.
- Whether organization owners may create quota requests directly or require a specific management permission (recommended: explicit `request-event-allowance`).
- Whether shift conflicts are hard-blocked or may be overridden by an authorized manager with a reason.
- Scanner operational-window grace periods before and after a shift.
- Whether assignment acknowledgement is mandatory for every organization or configurable.
- Brand name, logo, primary color and typography.
- Payment provider, merchant-of-record model, supported countries/currencies, VAT/tax, fees, refunds, chargebacks and payouts.
- Existing non-zero attendee amounts without verified payment records.
- Event cancellation/refund and rescheduling communication policies.
- Ticket transfer, attendee reassignment, waitlist and approval rules.
- Data retention/erasure versus financial/legal retention.
- Offline scanner requirements and conflict policy.
- Whether legacy school-domain tables contain production data and can eventually be removed.

## 14. Implementation decisions

- Preserve working code and data; renovate incrementally.
- Keep the application server-rendered with Blade/Bootstrap/Vuexy unless a later measured requirement justifies another frontend architecture.
- Use three centralized shells: public, authenticated dashboard and security operations.
- Introduce organizations additively and retain legacy ownership during migration.
- Organizer onboarding is administrator-controlled B2B onboarding; public organizer signup remains disabled.
- Every organization has an auditable event allowance. Each successfully created event, including a draft, consumes one allowance; later lifecycle actions do not restore it automatically.
- Quota approval is not proof of payment and stores only optional external agreement references/administrative notes until a commercial billing workflow is approved.
- Security officers remain users/memberships and receive scheduled event/gate assignments rather than a duplicate security identity model.
- Centralize authorization through Policies/permissions; sidebar visibility is not security.
- Keep existing transaction/idempotency/check-in audit foundations and strengthen them.
- Treat current paid-looking registrations as unverified legacy financial data, not confirmed revenue.
- Do not install Spatie Permission until its fit and migration impact are documented and approved.
- Do not run a frontend production build without explicit user authorization.

## 15. Completed work history

### 2026-10-29 — Phase 0 audit and roadmap

- Inspected framework/dependency manifests, routes, middleware, controllers, models, migrations, relationships, Blade layouts, menu configuration, CSS strategy and focused tests.
- Confirmed all 13 existing migrations report `Ran` locally.
- Confirmed the worktree was clean before creating this file.
- Ran `php artisan test --filter=EventPlatformWorkflowTest`: 7 tests passed with 46 assertions in 80.59 seconds.
- Ran `git diff --check`: passed with no whitespace errors.
- Created this authoritative roadmap. No application behavior or database schema was changed.

### 2026-10-29 — Roadmap authorization update

- Incorporated administrator-controlled organization onboarding, auditable event allowances, additional-allowance requests, organization security scheduling, officer dashboards and assignment-aware scanner enforcement.
- Revised dependency order so tenant ownership/authorization precedes quota enforcement and stable event/staff/gate relationships precede scheduling/scanner restrictions.
- No schema or behavior change was made as part of this roadmap update.

### 2026-10-29 — Phase 1 UI foundation

- Added centralized event-platform design tokens and reusable UI components.
- Added public, authenticated dashboard and security-operation shell foundations.
- Added `NavigationAccess` as the centralized compatibility boundary for current role-driven navigation; Phase 2 will connect it to permissions.
- Renovated the dashboard, event listing and organizer listing reference pages; moved public home and QR scanner to their dedicated shells.
- Updated focused tests to assert the correct public/dashboard/security shells.
- Verification: PHP lint passed; Blade templates cached successfully; `EventPlatformWorkflowTest` passed 7 tests/50 assertions in 5.70 seconds; `git diff --check` passed.
- No schema, event, registration, payment, quota or security-assignment behavior changed.
- Browser/device responsive and accessibility verification remains pending under `P1-UI-007`; no frontend production build was run.

### 2026-10-29 — Public homepage approval candidate

- Rebuilt the public navigation, event hero, integrated search, popular-event cards, trust benefits, category browser, promotional CTA and footer to follow the supplied visual reference.
- Kept public organizer registration disabled: guests receive Login and authenticated users receive Dashboard rather than a public signup action.
- Added server-side event search by text, location and date while preserving published/public/active/upcoming constraints.
- Reused real event banners with a branded fallback and the existing auditorium asset for the hero.
- Verification: controller and Blade PHP lint passed; Blade cache passed; focused suite passed 7 tests/52 assertions in 7.86 seconds; `git diff --check` passed.
- Visual/browser approval is pending. Do not continue the remaining Phase 1 or Phase 2 work until the user approves this public-page direction.

### 2026-10-29 — Conditional Phase 1 approval and Phase 2 authorization

- Public design direction is conditionally approved; database-backed content and functional controls remain mandatory.
- Public organizer signup remains prohibited. Attendee registration does not create organizer access or an attendee account.
- Added public and authenticated cross-page design-consistency checklists.
- Phase 1 browser/device, keyboard, contrast, three-shell inspection and production frontend build remain open until actually verified.
- Safe additive Phase 2 organization, membership, invitation, Policy, tenant-isolation and compatibility work is authorized.
- Phase 3 quota enforcement remains prohibited until organization ownership/backfill and authorization are verified.

### 2026-10-29 — Phase 2 organization and permission foundation

- Added additive organizations, memberships, hashed invitations and nullable event organization ownership while retaining legacy organizer ownership.
- Backfilled the local organizer, event and security officer relationships. The isolated Phase 2 migration rollback/reapply succeeded; all 14 migrations report `Ran`.
- Added the idempotent `organizations:backfill` command. Its post-migration dry-run inspected one organizer and reported zero organizations, memberships or events requiring changes.
- Added native Organization/Event Policies, tenant-scoped organization and event access, permission-aware organization navigation, superadmin lifecycle controls and owner/administrator staff invitations.
- Invitations expire after 72 hours, are single-use, store only token hashes, and do not replace credentials for existing users.
- Verification: `OrganizationFoundationTest` passed 7 tests/33 assertions; `EventPlatformWorkflowTest` passed 7 tests/52 assertions; Blade cache, PHP lint and `git diff --check` passed. Seven organization/invitation routes were confirmed.
- Remaining limitations: browser/device/accessibility verification and the frontend production build are still pending; no build was run. Representative production/staging profiling and migration rehearsal remain required before deployment. Organization suspension behavior for already-public events needs business approval.

### 2026-10-29 — Phase 3A event allowance foundation

- Added an immutable signed allowance-adjustment ledger with administrator, reason, effective date, notes and optional agreement reference.
- Derived allocation from ledger entries and consumption from persisted organization events; no mutable quota counter was introduced.
- Backfilled an opening adjustment equal to each organization's existing event count, preserving existing events without granting unapproved additional capacity.
- Enforced remaining allowance inside the event-creation transaction while locking the organization row. Draft events consume allowance and later event changes do not restore it.
- Added UUID event-creation retry protection and a unique database constraint so a repeated submission cannot create or consume twice.
- Added superadmin-only adjustment controls and immutable history on organization detail pages.
- Verification: `EventAllowanceTest` passed 3 tests/17 assertions; `EventPlatformWorkflowTest` passed 7 tests/52 assertions; `OrganizationFoundationTest` passed 8 tests/37 assertions.
- Remaining limitation: real simultaneous-request behavior still needs a MySQL concurrency integration test; SQLite/local request tests verify exhaustion and retry idempotency but not competing transactions.

### 2026-10-29 — Centralized responsive UI renovation

- Rebuilt the invitation-only login page as a responsive professional split layout with clear account context, accessible form controls, password visibility and submission feedback.
- Reworked sidebar branding and menu alignment with consistent icon sizing, truncation behavior, scroll handling and responsive off-canvas behavior.
- Made `public/assets/css/event-platform.css` the final authoritative design layer for authenticated cards, forms, tables, buttons, spacing, typography and responsive behavior.
- Neutralized legacy black KPI/admin card variants across authenticated pages through centralized white-surface tokens and consistent indigo/slate styling.
- Applied responsive table scrolling, compact mobile card padding and form/control sizing across existing admin and authenticated Blade pages without changing routes or backend behavior.
- Verification: all Blade views cached successfully; `EventPlatformWorkflowTest` passed 7 tests/52 assertions; `OrganizationFoundationTest` passed 8 tests/37 assertions; `EventAllowanceTest` passed 3 tests/17 assertions; `git diff --check` passed.
- No frontend production build was run. Formal browser/device screenshots, keyboard walkthrough and measured contrast verification remain tracked under `P1-UI-007` and must not be inferred from source checks.

### 2026-10-29 — Phase 3A allowance request lifecycle

- Added organization-scoped allowance requests with pending, under-review, approved, rejected and cancelled states.
- Added locked single-processing review logic. Approval creates one immutable positive allowance adjustment in the same transaction; duplicate processing is rejected.
- Added positive approved-quantity override, internal decision notes, reviewer attribution and queued organizer notification.
- Added organizer request/cancellation/history UI and a superadmin request queue with cross-organization quota totals.
- Verification: `EventAllowanceRequestTest` passed 3 tests/17 assertions; allowance, organization and platform regression suites passed 18 tests/106 assertions; all 16 migrations report `Ran`; five allowance routes were confirmed; Blade cache and `git diff --check` passed.
- Remaining limitation: true simultaneous event-creation testing still requires a MySQL concurrency harness; SQLite verifies retry and lifecycle behavior but not competing row locks.

## 16. Remaining tasks

Phase 1 implementation foundation is complete except browser/device accessibility verification. Phase 2 is implemented and locally verified; production/staging data profiling and migration rehearsal remain required before deployment. Phase 3A allowance storage, enforcement and request lifecycle are implemented; true MySQL concurrency verification and all later domain phases remain open.

## 17. Recommended next task

Proceed to Phase 3B event lifecycle definitions, enums, transition service and audit trail before expanding the event wizard. Complete true MySQL concurrency verification before treating `P3-QUO-004` as finished.

Do not begin Phase 3, payment integration or production deployment until the applicable approvals and production-safe profiling/rehearsal are complete.

## 18. HANDOFF / NEXT SESSION

- Last completed task: Phase 3A allowance ledger, enforcement, request lifecycle, admin review queue and organizer notifications.
- Currently active task: Phase 3B event lifecycle foundation. True MySQL concurrency verification remains open from Phase 3A.
- Exact Phase 2 areas modified: organization models/migration/controllers/requests/services/notification/Policies/views/routes, User/Event relationships and scoping, permission-aware navigation, organization tests, the idempotent backfill command, and this roadmap.
- Decisions already made: incremental migration; three centralized layouts; server-side authorization; preserve Vuexy until dependencies are mapped; no unverified revenue; no frontend build without approval.
- Tests already executed: `OrganizationFoundationTest` passed 7 tests/33 assertions; `EventPlatformWorkflowTest` passed 7 tests/52 assertions; Blade cache, relevant PHP lint and `git diff --check` passed; all 14 migrations reported `Ran`.
- Known failures: an aggregate `tinker --execute` verification probe had a PowerShell escaping parse error; it did not mutate data. The supported backfill dry-run succeeded and reported zero pending mapping changes. Browser/device accessibility verification has not been performed and no frontend production build was run.
- Next required action: implement Phase 3B event lifecycle, transition rules and audit trail. Do not claim allowance concurrency completion until it is exercised against MySQL.
