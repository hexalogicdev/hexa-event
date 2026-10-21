@extends('layouts.layoutMaster')
@section('title', 'Edit Event')

@section('content')
    <div class="ems-page-section">
        <div class="ems-dashboard-hero ems-fade-up">
            <div class="d-flex flex-column flex-xl-row justify-content-between gap-4 align-items-xl-end">
                <div>
                    <span class="ems-badge ems-badge--info mb-3">Edit event details</span>
                    <h5 class="ems-dashboard-hero__text mb-3">{{ $event->name }}</h5>
                    <p class="ems-dashboard-hero__text mb-0">
                        Update the event title, description, banner, timings, capacity, status, and check-in password.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline-light">
                        <i class="ti ti-layout-dashboard me-2"></i>Overview
                    </a>
                    <a href="{{ route('events.index') }}" class="btn btn-outline-light">
                        <i class="ti ti-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="ems-surface">
                <div class="ems-card-header">
                    <h2 class="ems-section-title h4 mb-1">Event details</h2>
                    <p class="ems-section-subtitle mb-0">All key event details can be edited here after creation.</p>
                </div>
                <div class="ems-card-body">
                    <form action="{{ route('events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 rounded-4">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <div class="ems-form-grid">
                            @if (auth()->user()->isSuperadmin())
                                <div class="ems-form-span-12">
                                    <label for="organizer_id" class="form-label">Event organizer</label>
                                    <select class="form-select" id="organizer_id" name="organizer_id" required>
                                        @foreach ($organizers as $organizer)
                                            <option value="{{ $organizer->id }}" @selected((string) old('organizer_id', $event->user_id) === (string) $organizer->id)>{{ $organizer->name }} — {{ $organizer->email }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="ems-form-span-6">
                                <label for="name" class="form-label">Event title</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $event->name) }}" required>
                            </div>
                            <div class="ems-form-span-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location"
                                    value="{{ old('location', $event->location) }}" required>
                            </div>
                            <div class="ems-form-span-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5">{{ old('description', $event->description) }}</textarea>
                            </div>
                            <div class="ems-form-span-6">
                                <label for="event_start_date" class="form-label">Event start</label>
                                <input type="date" class="form-control" id="event_start_date" name="event_start_date"
                                    value="{{ old('event_start_date', optional($event->event_start_date)->format('Y-m-d')) }}" required>
                            </div>
                            <div class="ems-form-span-6">
                                <label for="event_end_date" class="form-label">Event end</label>
                                <input type="date" class="form-control" id="event_end_date" name="event_end_date"
                                    value="{{ old('event_end_date', optional($event->event_end_date)->format('Y-m-d')) }}" required>
                            </div>
                            <div class="ems-form-span-6">
                                <label for="booking_start_date" class="form-label">Registration start</label>
                                <input type="date" class="form-control" id="booking_start_date" name="booking_start_date"
                                    value="{{ old('booking_start_date', optional($event->booking_start_date)->format('Y-m-d')) }}" required>
                            </div>
                            <div class="ems-form-span-6">
                                <label for="booking_end_date" class="form-label">Registration end</label>
                                <input type="date" class="form-control" id="booking_end_date" name="booking_end_date"
                                    value="{{ old('booking_end_date', optional($event->booking_end_date)->format('Y-m-d')) }}" required>
                            </div>
                            <div class="ems-form-span-4">
                                <label for="max_attendees" class="form-label">Capacity</label>
                                <input type="number" class="form-control" id="max_attendees" name="max_attendees"
                                    value="{{ old('max_attendees', $event->max_attendees) }}" required min="1">
                            </div>
                            <div class="ems-form-span-8">
                                <label for="banner_image" class="form-label">Banner image</label>
                                <input type="file" class="form-control" id="banner_image" name="banner_image" accept="image/*">
                                @if ($event->banner_image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $event->banner_image) }}" alt="Banner preview"
                                            class="rounded-4 w-100" style="height: 120px; object-fit: cover;">
                                    </div>
                                @endif
                            </div>
                            @include('events.partials.ticket-types')
                            <div class="ems-form-span-4">
                                <label for="status" class="form-label">Event status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft" @selected(old('status', $event->status) === 'draft')>Draft</option>
                                    <option value="published" @selected(old('status', $event->status) === 'published')>Published</option>
                                </select>
                            </div>
                            <div class="ems-form-span-4">
                                <label for="visibility" class="form-label">Visibility</label>
                                <select class="form-select" id="visibility" name="visibility">
                                    <option value="public" @selected(old('visibility', $event->visibility) === 'public')>Public</option>
                                    <option value="private" @selected(old('visibility', $event->visibility) === 'private')>Private</option>
                                </select>
                            </div>
                            <div class="ems-form-span-4">
                                <label for="is_active" class="form-label">Published</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1" @selected(old('is_active', $event->is_active))>Yes</option>
                                    <option value="0" @selected(old('is_active') === '0')>No</option>
                                </select>
                            </div>
                            <div class="ems-form-span-6">
                                <label for="checkin_password" class="form-label">Check-in password</label>
                                <input type="text" class="form-control" id="checkin_password" name="checkin_password"
                                    value="{{ old('checkin_password', $event->checkin_password) }}" minlength="3" maxlength="50" required>
                            </div>
                            <div class="ems-form-span-6 d-flex align-items-end">
                                <div class="w-100">
                                    <div class="ems-badge ems-badge--info mb-2">Field management separate</div>
                                    <p class="ems-form-hint mb-0">Use Manage Fields to control custom attendee questions.</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button class="btn btn-primary px-4" type="submit">Save changes</button>
                            <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline-primary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="d-grid gap-4">
                <div class="ems-dashboard-card">
                    <div class="ems-dashboard-card__head">
                        <div>
                            <h2>Management tabs</h2>
                            <p>Keep each area separate.</p>
                        </div>
                    </div>
                    <div class="ems-dashboard-card__body d-grid gap-2">
                        <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline-light">Event overview</a>
                        <a href="{{ route('events.fields', $event->id) }}" class="btn btn-outline-light">Registration fields</a>
                        <a href="{{ route('events.attendees', $event->id) }}" class="btn btn-outline-light">Attendees</a>
                        <a href="{{ route('events.report', $event->id) }}" class="btn btn-outline-light">Reports</a>
                    </div>
                </div>

                <div class="ems-dashboard-card">
                    <div class="ems-dashboard-card__head">
                        <div>
                            <h2>Current status</h2>
                            <p>Event visibility snapshot</p>
                        </div>
                    </div>
                    <div class="ems-dashboard-card__body d-grid gap-3">
                        <div class="ems-insight-block">
                            <div class="label">Status</div>
                            <div class="value">{{ ucfirst($event->status ?? 'draft') }}</div>
                        </div>
                        <div class="ems-insight-block">
                            <div class="label">Visibility</div>
                            <div class="value">{{ ucfirst($event->visibility ?? 'public') }}</div>
                        </div>
                        <div class="ems-insight-block">
                            <div class="label">Registration link</div>
                            <div class="value">{{ route('events.registration', $event->event_code) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
