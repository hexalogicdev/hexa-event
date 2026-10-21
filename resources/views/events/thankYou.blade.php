@extends('layouts.app')

@section('title', 'Booking Confirmed')

@section('content')
    @php
        $bookingUrl = route('attendee.qr', $attendee->qr_id);
        $qrPng = base64_encode(QrCode::format('png')->size(260)->generate($bookingUrl));
    @endphp

    <section class="container py-4 py-lg-5">
        <div class="ems-hero ems-fade-up">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="p-4 p-lg-5">
                        <span class="ems-kicker mb-3">
                            <i class="ti ti-circle-check"></i>
                            Registration complete
                        </span>
                        <h1 class="display-6 fw-bold mb-3">Thanks, {{ $attendee->name }}. Your booking is confirmed.</h1>
                        <p class="lead mb-4">
                            Your digital entry pass is ready. Save it and present it with your booking reference at the venue.
                        </p>

                        <div class="ems-grid ems-grid--2">
                            <div class="bg-white bg-opacity-10 rounded-4 p-3">
                                <div class="small text-white-50">Booking reference</div>
                                <div class="h4 mb-0 fw-bold">{{ $attendee->qr_id }}</div>
                            </div>
                            <div class="bg-white bg-opacity-10 rounded-4 p-3">
                                <div class="small text-white-50">Event</div>
                                <div class="h4 mb-0 fw-bold">{{ $attendee->event->name ?? 'Event' }}</div>
                            </div>
                            <div class="bg-white bg-opacity-10 rounded-4 p-3">
                                <div class="small text-white-50">Order reference</div>
                                <div class="h5 mb-0 fw-bold">{{ $attendee->order?->reference ?? 'Legacy booking' }}</div>
                            </div>
                            <div class="bg-white bg-opacity-10 rounded-4 p-3">
                                <div class="small text-white-50">Admission</div>
                                <div class="h5 mb-0 fw-bold">{{ $attendee->orderItem?->ticket_name ?? 'General admission' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="p-4 p-lg-5">
                        <div class="ems-card bg-white text-dark text-center">
                            <div class="ems-card-body">
                                <div class="ems-badge ems-badge--info mb-3">Digital entry pass</div>
                                <div class="mb-3">
                                    {!! QrCode::size(240)->generate($bookingUrl) !!}
                                </div>
                                <a href="data:image/png;base64,{{ $qrPng }}" download="qr-{{ $attendee->qr_id }}.png"
                                    class="btn btn-primary w-100">
                                    <i class="ti ti-download me-2"></i>
                                    Download entry pass
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
