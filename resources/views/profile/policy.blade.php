@extends('layouts.app')

@section('title', 'Privacy Policy & Terms')

@section('content')
    <section class="container-fluid py-4 py-lg-5">
        <div class="ems-surface ems-fade-up">
            <div class="ems-card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <span class="ems-badge ems-badge--info mb-3">Legal information</span>
                    <h1 class="ems-section-title display-6 mb-2">Privacy Policy and Terms</h1>
                    <p class="ems-section-subtitle mb-0">
                        This page explains how the Event Management Software collects, uses, stores, and protects data
                        across public booking pages, organizer dashboards, and QR check-in flows.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ url('/') }}" class="btn btn-outline-light">Home</a>
                    <a href="{{ url('/') }}#events" class="btn btn-primary">Browse events</a>
                </div>
            </div>

            <div class="ems-card-body">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="ems-dashboard-card ems-dashboard-card--soft h-100">
                            <div class="ems-dashboard-card__head">
                                <div>
                                    <h2>Policy hierarchy</h2>
                                    <p>How this document is organized.</p>
                                </div>
                            </div>
                            <div class="ems-dashboard-card__body d-grid gap-3">
                                <div class="ems-insight-block">
                                    <div class="label">1. Privacy overview</div>
                                    <div class="meta">What data is collected and why.</div>
                                </div>
                                <div class="ems-insight-block">
                                    <div class="label">2. Data usage</div>
                                    <div class="meta">How information is processed and retained.</div>
                                </div>
                                <div class="ems-insight-block">
                                    <div class="label">3. Terms of use</div>
                                    <div class="meta">Platform rules, permissions, and responsibilities.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="d-grid gap-4">
                            <div class="ems-dashboard-card">
                                <div class="ems-dashboard-card__head">
                                    <div>
                                        <h2>Privacy policy</h2>
                                        <p>Data handling across the platform.</p>
                                    </div>
                                </div>
                                <div class="ems-dashboard-card__body d-grid gap-3">
                                    <div class="ems-empty-state">
                                        <div class="ems-empty-state__title">Information we collect</div>
                                        <p class="mb-0">
                                            We collect the information users provide during registration, login, event
                                            creation, attendee booking, QR check-in, profile updates, and support
                                            interactions. This may include name, email address, phone number, country,
                                            address, password hashes, event details, booking references, attendance
                                            records, and uploaded assets such as banners.
                                        </p>
                                    </div>
                                    <div class="ems-empty-state">
                                        <div class="ems-empty-state__title">How the data is used</div>
                                        <p class="mb-0">
                                            Data is used to authenticate users, assign organizer or admin access, create
                                            and manage events, display public registration forms, generate booking
                                            references, verify QR scans, record attendance, show reports, and send
                                            operational notices. We do not use attendee data for unrelated purposes.
                                        </p>
                                    </div>
                                    <div class="ems-empty-state">
                                        <div class="ems-empty-state__title">Access by role</div>
                                        <p class="mb-0">
                                            Organizers can access only the events and attendee records they manage.
                                            Admin users can access the full platform for oversight, support, security,
                                            and product administration.
                                        </p>
                                    </div>
                                    <div class="ems-empty-state">
                                        <div class="ems-empty-state__title">Camera and QR permissions</div>
                                        <p class="mb-0">
                                            Camera permission is requested only when a user opens the QR check-in screen
                                            or starts scanner mode. The camera is used solely to read QR codes and is not
                                            stored or transmitted as a video recording by the application.
                                        </p>
                                    </div>
                                    <div class="ems-empty-state">
                                        <div class="ems-empty-state__title">Storage and retention</div>
                                        <p class="mb-0">
                                            Event records, registrations, attendance timestamps, and audit trails are
                                            retained for operational and reporting purposes. Passwords are stored as
                                            secure hashes. Where possible, we keep data only as long as needed for the
                                            platform, legal obligations, security, and support.
                                        </p>
                                    </div>
                                    <div class="ems-empty-state">
                                        <div class="ems-empty-state__title">Security controls</div>
                                        <p class="mb-0">
                                            The application uses role-based access, authenticated sessions, validated
                                            forms, and guarded check-in actions. Users are responsible for keeping login
                                            credentials private and for protecting their devices during live check-in.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="ems-dashboard-card">
                                <div class="ems-dashboard-card__head">
                                    <div>
                                        <h2>Terms of use</h2>
                                        <p>Rules for using the platform responsibly.</p>
                                    </div>
                                </div>
                                <div class="ems-dashboard-card__body d-grid gap-3">
                                    <div class="ems-empty-state">
                                        <div class="ems-empty-state__title">Account responsibilities</div>
                                        <p class="mb-0">
                                            Users must provide accurate information, keep credentials secure, and use the
                                            platform only for legitimate event operations. Organizer accounts are for
                                            managing their own events, while admin accounts are reserved for platform
                                            oversight.
                                        </p>
                                    </div>
                                    <div class="ems-empty-state">
                                        <div class="ems-empty-state__title">Acceptable use</div>
                                        <p class="mb-0">
                                            The system may not be used for unlawful activity, unauthorized access,
                                            automated abuse, fraudulent attendance marking, or attempts to interfere with
                                            the check-in and booking workflow.
                                        </p>
                                    </div>
                                    <div class="ems-empty-state">
                                        <div class="ems-empty-state__title">Public registration pages</div>
                                        <p class="mb-0">
                                            Public event pages may collect attendee details requested by the organizer.
                                            Those fields are presented to attendees before submission and are used only
                                            for that event's registration and attendance process.
                                        </p>
                                    </div>
                                    <div class="ems-empty-state">
                                        <div class="ems-empty-state__title">International readiness</div>
                                        <p class="mb-0">
                                            The interface and data handling are designed to support international use,
                                            responsive layouts, and multilingual-ready structures. Any local compliance
                                            obligations remain the responsibility of the event owner or organization.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="ems-dashboard-card">
                                <div class="ems-dashboard-card__head">
                                    <div>
                                        <h2>Important notice</h2>
                                        <p>Review with your legal advisor before going live.</p>
                                    </div>
                                </div>
                                <div class="ems-dashboard-card__body">
                                    <div class="ems-empty-state">
                                        <div class="ems-empty-state__title">Draft policy notice</div>
                                        <p class="mb-0">
                                            This page is a product-level policy draft tailored to the current
                                            application behavior. It is not legal advice and should be reviewed and
                                            adapted to your final operational needs, jurisdiction, and compliance
                                            requirements before publication.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
