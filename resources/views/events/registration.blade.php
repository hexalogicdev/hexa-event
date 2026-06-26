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
                            Public registration
                        </span>
                        <h1 class="display-6 fw-bold mb-3">{{ $event->name }}</h1>
                        <p class="lead mb-4">
                            Register once, receive a unique booking reference and QR code, then use that QR to check in
                            on event day.
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

                            @if ($errors->any())
                                <div class="alert alert-danger border-0 rounded-4">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <div class="ems-form-grid">
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
                                        <div class="ems-badge ems-badge--info mb-2">Secure booking</div>
                                        <p class="mb-0 text-muted">
                                            A unique reference and QR code will be generated after submission.
                                        </p>
                                    </div>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="ti ti-qrcode me-2"></i>
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
