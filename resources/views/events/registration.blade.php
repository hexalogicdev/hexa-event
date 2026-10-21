@extends('layouts.app')

@section('title', 'Register for ' . $event->name)

@section('content')
    <section class="container py-4 py-lg-5">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-5">
                <div class="ems-hero h-100">
                    <div class="p-4 p-lg-5 position-relative">
                        <span class="ems-kicker mb-3">
                            <i class="ti ti-ticket"></i>
                            Event booking
                        </span>
                        <h1 class="display-6 fw-bold mb-3">{{ $event->name }}</h1>
                        <p class="lead mb-4">
                            Review the event information, choose your admission type, and reserve your place.
                        </p>
                        <div class="ems-support-list">
                            <li>
                                <i class="ti ti-map-pin"></i>
                                <div>
                                    <div class="fw-semibold">Location</div>
                                    <div class="text-white-50">{{ $event->location ?: 'To be announced' }}</div>
                                </div>
                            </li>
                            <li>
                                <i class="ti ti-calendar-time"></i>
                                <div>
                                    <div class="fw-semibold">Event date</div>
                                    <div class="text-white-50">{{ $event->event_start_date?->format('d M Y') }}</div>
                                </div>
                            </li>
                            <li>
                                <i class="ti ti-clock-hour-4"></i>
                                <div>
                                    <div class="fw-semibold">Booking window</div>
                                    <div class="text-white-50">
                                        {{ $event->booking_start_date?->format('d M Y') }}
                                        -
                                        {{ $event->booking_end_date?->format('d M Y') }}
                                    </div>
                                </div>
                            </li>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="ems-surface h-100">
                    <div class="ems-card-header">
                        <h2 class="ems-section-title h4 mb-1">Complete your registration</h2>
                        <p class="ems-section-subtitle mb-0">All fields are required unless marked optional.</p>
                    </div>
                    <div class="ems-card-body">
                        <form action="{{ route('attendee.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->id }}">
                            <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', (string) \Illuminate\Support\Str::uuid()) }}">

                            @if ($errors->any())
                                <div class="alert alert-danger border-0 rounded-4">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            @if ($event->attendees_count >= $event->max_attendees)
                                <div class="alert alert-warning border-0 rounded-4">This event is fully booked.</div>
                            @elseif (now()->lt($event->booking_start_date) || now()->gt($event->booking_end_date?->endOfDay()))
                                <div class="alert alert-warning border-0 rounded-4">Registration is not currently open.</div>
                            @endif

                            <div class="ems-form-grid">
                                @if ($event->ticketing_type === 'ticketed')
                                    <div class="ems-form-span-12">
                                        <label class="form-label">Select a ticket</label>
                                        <div class="d-grid gap-3">
                                            @foreach ($event->ticketTypes as $ticket)
                                                @php $ticketSoldOut = $ticket->remaining === 0; @endphp
                                                <label class="form-check border rounded-4 p-3 mb-0 {{ $ticketSoldOut ? 'opacity-50' : '' }}">
                                                    <input class="form-check-input me-2" type="radio" name="ticket_type_id"
                                                        value="{{ $ticket->id }}" @checked((string) old('ticket_type_id') === (string) $ticket->id)
                                                        {{ $ticketSoldOut ? 'disabled' : 'required' }}>
                                                    <span class="d-flex justify-content-between gap-3">
                                                        <span>
                                                            <strong class="d-block">{{ $ticket->name }}</strong>
                                                            <span class="text-muted small">{{ $ticket->description ?: 'Event admission' }}</span>
                                                        </span>
                                                        <span class="text-end">
                                                            <strong class="d-block">{{ number_format($ticket->price, 2) }}</strong>
                                                            <span class="text-muted small">{{ $ticketSoldOut ? 'Sold out' : ($ticket->remaining === null ? 'Available' : $ticket->remaining . ' left') }}</span>
                                                        </span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @error('ticket_type_id')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                                    </div>
                                @else
                                    <div class="ems-form-span-12">
                                        <div class="alert alert-success border-0 rounded-4 mb-0"><strong>Free admission</strong> — no ticket payment is required.</div>
                                    </div>
                                @endif
                                <div class="ems-form-span-12">
                                    <label for="name" class="form-label">Full name</label>
                                    <input type="text" name="name" class="form-control" id="name"
                                        value="{{ old('name') }}" placeholder="Enter your full name" required>
                                </div>
                                <div class="ems-form-span-6">
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" name="email" class="form-control" id="email"
                                        value="{{ old('email') }}" placeholder="name@example.com" required>
                                </div>
                                <div class="ems-form-span-6">
                                    <label for="phone" class="form-label">Phone number</label>
                                    <input type="text" name="phone" class="form-control" id="phone"
                                        value="{{ old('phone') }}" placeholder="+44 20 0000 0000">
                                </div>

                                @foreach ($event->registration_fields ?? [] as $field)
                                    @php
                                        $fieldKey = $field['key'] ?? \Illuminate\Support\Str::slug($field['label'] ?? 'field-' . $loop->index);
                                        $fieldType = $field['type'] ?? 'text';
                                        $fieldOptions = collect($field['options'] ?? []);
                                        $isRequired = !empty($field['required']);
                                    @endphp
                                    <div class="ems-form-span-12">
                                        <label for="{{ $fieldKey }}" class="form-label">
                                            {{ $field['label'] ?? 'Custom field' }}
                                            @if ($isRequired)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        @switch($fieldType)
                                            @case('textarea')
                                                <textarea class="form-control" id="{{ $fieldKey }}" name="{{ $fieldKey }}" rows="4"
                                                    {{ $isRequired ? 'required' : '' }}>{{ old($fieldKey) }}</textarea>
                                                @break

                                            @case('dropdown')
                                                <select class="form-select" id="{{ $fieldKey }}" name="{{ $fieldKey }}"
                                                    {{ $isRequired ? 'required' : '' }}>
                                                    <option value="">Select an option</option>
                                                    @foreach ($fieldOptions as $option)
                                                        <option value="{{ $option }}" @selected(old($fieldKey) === $option)>{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                                @break

                                            @case('radio')
                                                <div class="d-grid gap-2">
                                                    @foreach ($fieldOptions as $option)
                                                        <label class="form-check border rounded-4 p-3 mb-0">
                                                            <input class="form-check-input me-2" type="radio" name="{{ $fieldKey }}"
                                                                value="{{ $option }}" @checked(old($fieldKey) === $option)
                                                                {{ $isRequired ? 'required' : '' }}>
                                                            <span class="form-check-label">{{ $option }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                @break

                                            @case('checkbox')
                                                <div class="d-grid gap-2">
                                                    @foreach ($fieldOptions as $option)
                                                        <label class="form-check border rounded-4 p-3 mb-0">
                                                            <input class="form-check-input me-2" type="checkbox" name="{{ $fieldKey }}[]"
                                                                value="{{ $option }}">
                                                            <span class="form-check-label">{{ $option }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                @break

                                            @case('file')
                                                <input type="file" class="form-control" id="{{ $fieldKey }}" name="{{ $fieldKey }}"
                                                    {{ $isRequired ? 'required' : '' }}>
                                                @break

                                            @case('email')
                                            @case('phone')
                                            @case('number')
                                            @case('date')
                                                <input type="{{ $fieldType }}" class="form-control" id="{{ $fieldKey }}" name="{{ $fieldKey }}"
                                                    value="{{ old($fieldKey) }}" {{ $isRequired ? 'required' : '' }}>
                                                @break

                                            @default
                                                <input type="text" class="form-control" id="{{ $fieldKey }}" name="{{ $fieldKey }}"
                                                    value="{{ old($fieldKey) }}" {{ $isRequired ? 'required' : '' }}>
                                        @endswitch
                                    </div>
                                @endforeach
                            </div>

                            <div class="ems-card mt-4">
                                <div class="ems-card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                    <div>
                                        <div class="ems-badge ems-badge--info mb-2">Booking confirmation</div>
                                        <p class="mb-0 text-muted">
                                            Your booking reference and entry pass will be generated after submission.
                                        </p>
                                    </div>
                                    <button type="submit" class="btn btn-primary px-4"
                                        @disabled($event->attendees_count >= $event->max_attendees || now()->lt($event->booking_start_date) || now()->gt($event->booking_end_date?->endOfDay()))>
                                        <i class="ti ti-ticket me-2"></i>
                                        Confirm booking
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
