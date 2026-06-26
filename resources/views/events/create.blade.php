@extends('layouts.layoutMaster')
@section('title', 'Create Event')

@section('content')
    <div class="ems-page-section">
        <div class="ems-dashboard-hero ems-fade-up">
            <div class="d-flex flex-column flex-xl-row justify-content-between gap-4 align-items-xl-end">
                <div>
                    <span class="ems-badge ems-badge--info mb-3">Event builder</span>
                    <h5 class="ems-dashboard-hero__text mb-3">Create a polished event with clear publishing controls.</h5>
                    <p class="ems-dashboard-hero__text mb-0">
                        Set dates, capacity, visibility, and check-in security. Custom attendee fields are managed on a
                        separate page after the event is saved.
                    </p>
                </div>
                <a href="{{ route('events.index') }}" class="btn btn-outline-light">
                    <i class="ti ti-arrow-left me-2"></i>Back to events
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="ems-surface">
                <div class="ems-card-header">
                    <h2 class="ems-section-title h4 mb-1">Event details</h2>
                    <p class="ems-section-subtitle mb-0">Fill in the essential details and publish when ready.</p>
                </div>
                <div class="ems-card-body">
                    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 rounded-4">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <div class="ems-form-grid">
                            <div class="ems-form-span-6">
                                <label for="name" class="form-label">Event title</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="International Conference 2026" value="{{ old('name') }}" required>
                            </div>
                            <div class="ems-form-span-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location"
                                    placeholder="London, United Kingdom" value="{{ old('location') }}" required>
                            </div>
                            <div class="ems-form-span-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5"
                                    placeholder="Describe the event, audience, and agenda">{{ old('description') }}</textarea>
                            </div>
                            <div class="ems-form-span-6">
                                <label for="event_start_date" class="form-label">Event start</label>
                                <input type="date" class="form-control" id="event_start_date" name="event_start_date"
                                    value="{{ old('event_start_date') }}" required>
                            </div>
                            <div class="ems-form-span-6">
                                <label for="event_end_date" class="form-label">Event end</label>
                                <input type="date" class="form-control" id="event_end_date" name="event_end_date"
                                    value="{{ old('event_end_date') }}" required>
                            </div>
                            <div class="ems-form-span-6">
                                <label for="booking_start_date" class="form-label">Registration start</label>
                                <input type="date" class="form-control" id="booking_start_date" name="booking_start_date"
                                    value="{{ old('booking_start_date') }}" required>
                            </div>
                            <div class="ems-form-span-6">
                                <label for="booking_end_date" class="form-label">Registration end</label>
                                <input type="date" class="form-control" id="booking_end_date" name="booking_end_date"
                                    value="{{ old('booking_end_date') }}" required>
                            </div>
                            <div class="ems-form-span-4">
                                <label for="max_attendees" class="form-label">Capacity</label>
                                <input type="number" class="form-control" id="max_attendees" name="max_attendees"
                                    placeholder="500" value="{{ old('max_attendees') }}" required min="1">
                            </div>
                            <div class="ems-form-span-4">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" class="form-control" id="price" name="price"
                                    placeholder="0.00" value="{{ old('price') }}" min="0" max="10000" step="0.01">
                            </div>
                            <div class="ems-form-span-4">
                                <label for="banner_image" class="form-label">Banner image</label>
                                <input type="file" class="form-control" id="banner_image" name="banner_image" accept="image/*">
                            </div>
                            <div class="ems-form-span-4">
                                <label for="status" class="form-label">Event status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft" @selected(old('status', 'draft') === 'draft')>Draft</option>
                                    <option value="published" @selected(old('status') === 'published')>Published</option>
                                </select>
                            </div>
                            <div class="ems-form-span-4">
                                <label for="visibility" class="form-label">Visibility</label>
                                <select class="form-select" id="visibility" name="visibility">
                                    <option value="public" @selected(old('visibility', 'public') === 'public')>Public</option>
                                    <option value="private" @selected(old('visibility') === 'private')>Private</option>
                                </select>
                            </div>
                            <div class="ems-form-span-4">
                                <label for="is_active" class="form-label">Published</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1" @selected(old('is_active', 1))>Yes</option>
                                    <option value="0" @selected(old('is_active') === '0')>No</option>
                                </select>
                            </div>
                            <div class="ems-form-span-6">
                                <label for="checkin_password" class="form-label">Check-in password</label>
                                <input type="text" class="form-control" id="checkin_password" name="checkin_password"
                                    value="{{ old('checkin_password') }}" minlength="3" maxlength="50"
                                    placeholder="Enter a secure password" required>
                            </div>
                            <div class="ems-form-span-6 d-flex align-items-end">
                                <div class="w-100">
                                    <div class="ems-badge ems-badge--info mb-2">Field management is separate</div>
                                    <p class="ems-form-hint mb-0">
                                        After saving this event, go to Manage Fields to build the public registration form.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="ti ti-device-floppy me-2"></i>
                                Save event
                            </button>
                            <a href="{{ route('events.index') }}" class="btn btn-outline-primary px-4">Cancel</a>
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
                            <h2>Publishing checklist</h2>
                            <p>Keep the setup clean and predictable.</p>
                        </div>
                    </div>
                    <div class="ems-dashboard-card__body">
                        <div class="ems-step">
                            <div class="ems-step__index">1</div>
                            <div>
                                <div class="fw-semibold">Fill event details</div>
                                <div class="text-muted small">Title, dates, location, capacity, and password.</div>
                            </div>
                        </div>
                        <div class="ems-step">
                            <div class="ems-step__index">2</div>
                            <div>
                                <div class="fw-semibold">Open Manage Fields</div>
                                <div class="text-muted small">Add custom attendee fields separately.</div>
                            </div>
                        </div>
                        <div class="ems-step">
                            <div class="ems-step__index">3</div>
                            <div>
                                <div class="fw-semibold">Publish and share</div>
                                <div class="text-muted small">Copy the public registration link from the events page.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ems-dashboard-card">
                    <div class="ems-dashboard-card__head">
                        <div>
                            <h2>Tip</h2>
                            <p>Professional event operations</p>
                        </div>
                    </div>
                    <div class="ems-dashboard-card__body">
                        <p class="ems-section-subtitle mb-0">
                            Keep the main event form focused. Registration questions, attendee data, and reporting are all
                            managed in separate sections so the workspace stays simple for organizers.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
