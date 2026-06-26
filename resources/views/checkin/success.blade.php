@extends('layouts.app')

@section('title', 'Check-In Successful')

@section('content')
    <section class="container-fluid py-3 py-lg-4">
        <div class="ems-checkin-shell ems-checkin-shell--wide">
            <div class="ems-checkin-frame ems-fade-up">
                <div class="ems-checkin-toolbar">
                    <div class="d-flex flex-column gap-2">
                        <span class="ems-badge ems-badge--success">
                            <i class="ti ti-circle-check"></i>
                            Attendance recorded
                        </span>
                        <div>
                            <h1 class="ems-checkin-title mb-1">The attendee is now marked as checked in.</h1>
                            <p class="ems-checkin-subtitle mb-0">
                                The timestamp has been stored and the record is ready for attendance reporting.
                            </p>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('attendee.qr.scan') }}" class="btn btn-outline-light">
                            <i class="ti ti-scan me-2"></i>
                            Scan another
                        </a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="ti ti-layout-dashboard me-2"></i>
                                Dashboard
                            </a>
                        @else
                            <a href="{{ url('/') }}" class="btn btn-primary">
                                <i class="ti ti-home-2 me-2"></i>
                                Home
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="row g-4 align-items-stretch">
                    <div class="col-xl-6">
                        <div class="ems-checkin-hero ems-checkin-hero--success">
                            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4">
                                <span class="ems-kicker mb-0 bg-white bg-opacity-10 text-white">
                                    <i class="ti ti-shield-check"></i>
                                    Verified attendee
                                </span>
                                <span class="ems-badge ems-badge--success">Completed</span>
                            </div>

                            <h2 class="ems-checkin-hero__title">{{ $attendee->name }}</h2>
                            <p class="ems-checkin-hero__text">
                                {{ $attendee->event->name }} and the attendee record are now synced with the live attendance list.
                            </p>

                            <div class="ems-checkin-summary">
                                <div class="ems-checkin-summary__item">
                                    <span>Check-in time</span>
                                    <strong>{{ $attendee->checked_in_at?->format('h:i A') }}</strong>
                                </div>
                                <div class="ems-checkin-summary__item">
                                    <span>Check-in date</span>
                                    <strong>{{ $attendee->checked_in_at?->format('d M Y') }}</strong>
                                </div>
                                <div class="ems-checkin-summary__item">
                                    <span>Token</span>
                                    <strong>{{ $attendee->qr_id }}</strong>
                                </div>
                                <div class="ems-checkin-summary__item">
                                    <span>Event</span>
                                    <strong>{{ $attendee->event->name }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="ems-checkin-card-panel">
                            <div class="ems-badge ems-badge--success mb-3">Verified</div>
                            <h2 class="ems-section-title h4 mb-2">{{ $attendee->name }}</h2>
                            <p class="ems-section-subtitle mb-4">{{ $attendee->event->name }}</p>

                            <div class="ems-grid ems-grid--2 mb-4">
                                <div class="ems-stat">
                                    <div class="ems-stat__label">Check-in time</div>
                                    <div class="ems-stat__value">{{ $attendee->checked_in_at->format('h:i A') }}</div>
                                    <div class="ems-stat__meta">{{ $attendee->checked_in_at->format('d M Y') }}</div>
                                </div>
                                <div class="ems-stat">
                                    <div class="ems-stat__label">Booking token</div>
                                    <div class="ems-stat__value">{{ $attendee->qr_id }}</div>
                                    <div class="ems-stat__meta">Stored securely in the system</div>
                                </div>
                            </div>

                            <div class="alert alert-success border-0 rounded-4 mb-4">
                                The attendee is now included in the live attendance report.
                            </div>

                            <div class="d-grid gap-2 d-md-flex">
                                <a href="{{ route('attendee.qr.scan') }}" class="btn btn-outline-light flex-grow-1">
                                    <i class="ti ti-scan me-2"></i>
                                    Scan next attendee
                                </a>
                                @auth
                                    <a href="{{ route('events.attendees', $attendee->event_id) }}" class="btn btn-primary flex-grow-1">
                                        <i class="ti ti-users me-2"></i>
                                        Open attendee list
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
