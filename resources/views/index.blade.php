@extends('layouts.app')

@section('title', 'Event Management Platform')

@section('content')
    <section class="container py-4 py-lg-5">
        <div class="ems-hero ems-fade-up">
            <div class="row align-items-center g-4 position-relative">
                <div class="col-lg-7">
                    <div class="p-4 p-lg-5">
                        {{-- <img src="{{ asset('assets/img/hexa-logo.png') }}" alt="HexaLogicDev" class="ems-hero-logo mb-4"> --}}
                        {{-- <span class="ems-kicker mb-3">
                            <i class="ti ti-globe"></i>
                            International event operations
                        </span> --}}
                        <h5 class=" fw-bold mb-3">
                            Plan public events, issue QR bookings, and check in attendees with confidence.
                        </h5>
                        <p class="lead mb-4">
                            One platform for organizers, entrance staff, and attendees. Built for responsive,
                            multilingual use across regions, time zones, and devices.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('login') }}" class="btn btn-light px-4">
                                <i class="ti ti-login me-2"></i>
                                Log in
                            </a>

                            <a href="{{ route('register') }}" class="btn btn-outline-light px-4">
                                <i class="ti ti-user-plus me-2"></i>
                                Create account
                            </a>
                        </div>
                        <div class="row g-3 mt-4">
                            <div class="col-sm-4">
                                <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                                    <div class="small text-white-50">Public bookings</div>
                                    <div class="h4 mb-0 fw-bold">Share links</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                                    <div class="small text-white-50">Entrance control</div>
                                    <div class="h4 mb-0 fw-bold">QR verify</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                                    <div class="small text-white-50">Reporting</div>
                                    <div class="h4 mb-0 fw-bold">Export cleanly</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="p-4 p-lg-5">
                        <div class="ems-card ems-card--raised bg-white text-dark">
                            <div class="ems-card-body">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div>
                                        <div class="text-uppercase text-muted small fw-semibold">Live overview</div>
                                        <h3 class="mb-0 text-white">Everything in one flow</h3>
                                    </div>
                                    <span class="ems-badge ems-badge--info">Global-ready</span>
                                </div>
                                <div class="ems-grid ems-grid--2 mb-4">
                                    <div class="ems-stat">
                                        <div class="ems-stat__label">Organizers</div>
                                        <div class="ems-stat__value">4</div>
                                        <div class="ems-stat__meta">Role-based access</div>
                                    </div>
                                    <div class="ems-stat">
                                        <div class="ems-stat__label">Check-in</div>
                                        <div class="ems-stat__value">QR</div>
                                        <div class="ems-stat__meta">Secure token flow</div>
                                    </div>
                                </div>
                                <div class="ems-step">
                                    <div class="ems-step__index">1</div>
                                    <div>
                                        <div class="fw-semibold text-white">Create an event</div>
                                        <div class="text-muted small">Add schedules, limits, and custom fields.</div>
                                    </div>
                                </div>
                                <div class="ems-step">
                                    <div class="ems-step__index">2</div>
                                    <div>
                                        <div class="fw-semibold text-white">Share the link</div>
                                        <div class="text-muted small">Open public registration on any device.</div>
                                    </div>
                                </div>
                                <div class="ems-step">
                                    <div class="ems-step__index">3</div>
                                    <div>
                                        <div class="fw-semibold text-white">Verify at the door</div>
                                        <div class="text-muted small">Use the QR token and check-in password.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-4 py-lg-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="ems-card h-100">
                    <div class="ems-card-body">
                        <span class="ems-chip mb-3">
                            <i class="ti ti-layout-dashboard"></i>
                            Organizer dashboard
                        </span>
                        <h3 class="ems-section-title h4">Control the full event lifecycle</h3>
                        <p class="ems-section-subtitle mb-0">
                            Keep every event, attendee, and check-in visible in a single responsive workspace.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ems-card h-100">
                    <div class="ems-card-body">
                        <span class="ems-chip mb-3">
                            <i class="ti ti-qrcode"></i>
                            Verification
                        </span>
                        <h3 class="ems-section-title h4">Simple mobile check-in</h3>
                        <p class="ems-section-subtitle mb-0">
                            Scan the booking QR, enter the event password, and confirm attendance in seconds.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ems-card h-100">
                    <div class="ems-card-body">
                        <span class="ems-chip mb-3">
                            <i class="ti ti-language"></i>
                            International-ready
                        </span>
                        <h3 class="ems-section-title h4">Designed for global teams</h3>
                        <p class="ems-section-subtitle mb-0">
                            Clear hierarchy, responsive layouts, and locale-friendly navigation make it easy to adopt
                            anywhere.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-4 py-lg-5">
        <div class="ems-surface">
            <div class="ems-card-body">
                <div class="row align-items-center g-4">
                    <div class="col-lg-5">
                        <h2 class="ems-section-title display-6">Built for the flow your team actually uses</h2>
                        <p class="ems-section-subtitle mb-4">
                            Organizers publish events, attendees register from a clean public link, and entrance staff
                            verify entries on a fast mobile-first screen.
                        </p>
                        <a href="{{ route('login') }}" class="btn btn-primary px-4">Open dashboard</a>
                    </div>
                    <div class="col-lg-7">
                        <div class="ems-grid ems-grid--3">
                            <div class="ems-stat">
                                <div class="ems-stat__label">Public access</div>
                                <div class="ems-stat__value text-black">1 link</div>
                                <div class="ems-stat__meta">Share once, register anywhere.</div>
                            </div>
                            <div class="ems-stat">
                                <div class="ems-stat__label">Safety</div>
                                <div class="ems-stat__value text-black">Secure</div>
                                <div class="ems-stat__meta">QR token plus password validation.</div>
                            </div>
                            <div class="ems-stat">
                                <div class="ems-stat__label">Reports</div>
                                <div class="ems-stat__value text-black">CSV</div>
                                <div class="ems-stat__meta">Track attendance, exports, and answers.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
