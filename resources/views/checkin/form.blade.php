@extends('layouts.layoutMaster')

@section('title', 'Event Check-In')

@section('content')
    <section class="container-fluid py-3 py-lg-4">
        <div class="ems-checkin-shell ems-checkin-shell--wide">
            <div class="ems-checkin-frame ems-fade-up">
                <div class="ems-checkin-toolbar">
                    <div class="d-flex flex-column gap-2">
                        <span class="ems-badge ems-badge--info">
                            <i class="ti ti-qrcode"></i>
                            QR attendee verification
                        </span>
                        <div>
                            <h1 class="ems-checkin-title mb-1">Confirm attendance with a clean, fast check-in flow.</h1>
                            <p class="ems-checkin-subtitle mb-0">
                                Review the booking status and mark the attendee present in one step.
                            </p>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('attendee.qr.scan') }}" class="btn btn-outline-light">
                            <i class="ti ti-scan me-2"></i>
                            Open scanner
                        </a>
                        @unless(auth()->user()->isSecurityOfficer())
                            <a href="{{ route('dashboard') }}" class="btn btn-primary"><i class="ti ti-layout-dashboard me-2"></i>Dashboard</a>
                        @endunless
                    </div>
                </div>

                <div class="row g-4 align-items-stretch">
                    <div class="col-xl-7">
                        <div class="ems-checkin-hero">
                            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4">
                                <span class="ems-kicker mb-0 bg-white bg-opacity-10 text-white">
                                    <i class="ti ti-device-mobile"></i>
                                    Mobile-first entrance
                                </span>
                                @if ($attendee->checked_in)
                                    <span class="ems-badge ems-badge--success">Already checked in</span>
                                @else
                                    <span class="ems-badge ems-badge--warning">Pending check-in</span>
                                @endif
                            </div>

                            <h2 class="ems-checkin-hero__title">Clear ticket verification for venue staff.</h2>
                            <p class="ems-checkin-hero__text">
                                Attendee identity, ticket status, and the check-in action remain readable on desktop and mobile.
                            </p>

                            <div class="ems-checkin-summary">
                                <div class="ems-checkin-summary__item">
                                    <span>Attendee</span>
                                    <strong>{{ $attendee->name }}</strong>
                                </div>
                                <div class="ems-checkin-summary__item">
                                    <span>Event</span>
                                    <strong>{{ $attendee->event->name }}</strong>
                                </div>
                                <div class="ems-checkin-summary__item">
                                    <span>Reference</span>
                                    <strong>{{ $attendee->qr_id }}</strong>
                                </div>
                                <div class="ems-checkin-summary__item">
                                    <span>Venue</span>
                                    <strong>{{ $attendee->event->location ?? 'Location not set' }}</strong>
                                </div>
                            </div>

                            <div class="ems-checkin-notes">
                                <div class="ems-support-list">
                                    <li>
                                        <i class="ti ti-shield-check"></i>
                                        <div>
                                            <div class="fw-semibold">Secure verification</div>
                                            <div class="text-white-50">Only authenticated event staff can mark this ticket present.</div>
                                        </div>
                                    </li>
                                    <li>
                                        <i class="ti ti-clock-check"></i>
                                        <div>
                                            <div class="fw-semibold">Timestamped attendance</div>
                                            <div class="text-white-50">Check-in time is stored instantly and reflected in the organizer report.</div>
                                        </div>
                                    </li>
                                    <li>
                                        <i class="ti ti-scan"></i>
                                        <div>
                                            <div class="fw-semibold">QR or manual entry</div>
                                            <div class="text-white-50">Use a QR camera, a handheld scanner, or the direct booking link.</div>
                                        </div>
                                    </li>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="ems-checkin-card-panel">
                            <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                                <div>
                                    <div class="ems-badge ems-badge--info mb-2">Attendee record</div>
                                    <h2 class="ems-section-title h4 mb-1">{{ $attendee->name }}</h2>
                                    <p class="ems-section-subtitle mb-0">{{ $attendee->event->name }}</p>
                                </div>
                                @if ($attendee->checked_in)
                                    <span class="ems-badge ems-badge--success">Verified</span>
                                @else
                                    <span class="ems-badge ems-badge--warning">Ready to check in</span>
                                @endif
                            </div>

                            <div class="ems-checkin-meta mb-4">
                                <div class="ems-checkin-meta__item">
                                    <span>Email</span>
                                    <strong>{{ $attendee->email }}</strong>
                                </div>
                                <div class="ems-checkin-meta__item">
                                    <span>QR token</span>
                                    <strong>{{ $attendee->qr_id }}</strong>
                                </div>
                                <div class="ems-checkin-meta__item">
                                    <span>Event</span>
                                    <strong>{{ $attendee->event->name }}</strong>
                                </div>
                            </div>

                            @if ($attendee->checked_in)
                                <div class="alert alert-success border-0 rounded-4 mb-0">
                                    <div class="fw-semibold mb-1">Already checked in</div>
                                    <div>Time: {{ $attendee->checked_in_at?->format('d M Y, h:i A') }}</div>
                                </div>
                            @else
                                @if ($errors->any())
                                    <div class="alert alert-danger border-0 rounded-4">
                                        {{ $errors->first() }}
                                    </div>
                                @endif

                                <form action="{{ route('attendee.qr.verify', $attendee->qr_id) }}" method="POST" class="d-grid gap-3">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="ti ti-circle-check me-2"></i>
                                        Confirm check-in
                                    </button>

                                    <a href="{{ route('attendee.qr.scan') }}" class="btn btn-outline-light btn-lg w-100">
                                        <i class="ti ti-scan me-2"></i>
                                        Use scanner mode
                                    </a>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
