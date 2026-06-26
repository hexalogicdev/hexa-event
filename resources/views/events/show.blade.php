@extends('layouts.layoutMaster')
@section('title', $event->name)

@section('content')
    @php
        $registrationUrl = route('events.registration', $event->event_code);
        $checkedInRate = $event->attendees_count ? round(($checkedInCount / max($event->attendees_count, 1)) * 100) : 0;
    @endphp

    <div class="ems-page-section">
        <div class="ems-dashboard-hero ems-fade-up">
            <div class="d-flex flex-column flex-xl-row justify-content-between gap-4 align-items-xl-end">
                <div>
                    <span class="ems-badge ems-badge--info mb-3">Event workspace</span>
                    <h5 class="ems-dashboard-hero__text mb-3">{{ $event->name }}</h5>
                    <p class="ems-dashboard-hero__text mb-0">
                        Use the dedicated tabs and actions below to manage event details, registration fields, attendees,
                        check-ins, and reports without mixing everything into one page.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('events.edit', $event->id) }}" class="btn btn-primary">
                        Edit event details
                    </a>
                    <a href="{{ route('events.fields', $event->id) }}" class="btn btn-outline-light">
                        Manage fields
                    </a>
                    <a href="{{ route('events.attendees', $event->id) }}" class="btn btn-outline-light">
                        Attendees
                    </a>
                    <a href="{{ route('events.report', $event->id) }}" class="btn btn-outline-light">
                        Reports
                    </a>
                </div>
            </div>

            <div class="ems-dashboard-metrics">
                <div class="ems-dashboard-metric"><div class="label">Total registrations</div><div class="value">{{ $event->attendees_count }}</div></div>
                <div class="ems-dashboard-metric"><div class="label">Checked in</div><div class="value">{{ $checkedInCount }}</div></div>
                <div class="ems-dashboard-metric"><div class="label">Pending</div><div class="value">{{ $pendingCount }}</div></div>
                <div class="ems-dashboard-metric"><div class="label">Upcoming events</div><div class="value">{{ $upcomingCount }}</div></div>
                <div class="ems-dashboard-metric"><div class="label">Visibility</div><div class="value">{{ ucfirst($event->visibility ?? 'public') }}</div></div>
                <div class="ems-dashboard-metric"><div class="label">Status</div><div class="value">{{ ucfirst($event->status ?? 'draft') }}</div></div>
                <div class="ems-dashboard-metric"><div class="label">Check-in rate</div><div class="value">{{ $checkedInRate }}%</div></div>
                <div class="ems-dashboard-metric"><div class="label">Public link</div><div class="value">Copy</div></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="ems-surface">
                <div class="ems-card-header d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <h2 class="ems-section-title h4 mb-1">Event details</h2>
                        <p class="ems-section-subtitle mb-0">{{ $event->description ?: 'No description added yet.' }}</p>
                    </div>
                    <span class="ems-pill-dark">{{ $event->is_active ? 'Published' : 'Hidden' }}</span>
                </div>
                <div class="ems-card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="ems-insight-block h-100">
                                <div class="label">Schedule</div>
                                <div class="value">{{ $event->event_start_date?->format('d M Y') }} - {{ $event->event_end_date?->format('d M Y') }}</div>
                                <div class="meta">Registration: {{ $event->booking_start_date?->format('d M Y') }} to {{ $event->booking_end_date?->format('d M Y') }}</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="ems-insight-block h-100">
                                <div class="label">Location and capacity</div>
                                <div class="value">{{ $event->location ?: 'Location not set' }}</div>
                                <div class="meta">Capacity: {{ $event->max_attendees }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-light ems-copy-link" data-copy-link="{{ $registrationUrl }}">Copy public registration link</button>
                                <a href="{{ route('events.attendees.export', $event->id) }}" class="btn btn-outline-light">Download report</a>
                                <a href="{{ route('events.registration', $event->event_code) }}" target="_blank" class="btn btn-outline-light">View event</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="d-grid gap-4">
                <div class="ems-dashboard-card">
                    <div class="ems-dashboard-card__head">
                        <div>
                            <h2>Recent registrations</h2>
                            <p>Latest attendee activity.</p>
                        </div>
                    </div>
                    <div class="ems-dashboard-card__body d-grid gap-3">
                        @forelse ($attendees as $attendee)
                            <div class="ems-attendee-row">
                                <div class="ems-avatar">{{ strtoupper(substr($attendee->name, 0, 1)) }}</div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between gap-2">
                                        <div>
                                            <div class="ems-event-row__title">{{ $attendee->name }}</div>
                                            <div class="ems-event-row__meta">{{ $attendee->email }}</div>
                                        </div>
                                        <span class="ems-status {{ $attendee->checked_in ? 'is-success' : 'is-waiting' }}">
                                            {{ $attendee->checked_in ? 'Checked in' : 'Pending' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="ems-empty-state">
                                <div class="ems-empty-state__title">No registrations yet</div>
                                <p class="mb-0">Share the public link to start collecting attendees.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="ems-dashboard-card">
                    <div class="ems-dashboard-card__head">
                        <div>
                            <h2>Organizer actions</h2>
                            <p>Use the tabs for clear separation.</p>
                        </div>
                    </div>
                    <div class="ems-dashboard-card__body d-grid gap-2">
                        <a href="{{ route('events.edit', $event->id) }}" class="btn btn-primary">Edit event details</a>
                        <a href="{{ route('events.fields', $event->id) }}" class="btn btn-outline-light">Manage registration fields</a>
                        <a href="{{ route('events.attendees', $event->id) }}" class="btn btn-outline-light">Manage attendees</a>
                        <a href="{{ route('events.report', $event->id) }}" class="btn btn-outline-light">Attendance report</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.querySelectorAll('[data-copy-link]').forEach((button) => {
            button.addEventListener('click', async () => {
                const link = button.getAttribute('data-copy-link');
                try {
                    await navigator.clipboard.writeText(link);
                    const original = button.innerHTML;
                    button.innerHTML = 'Copied';
                    setTimeout(() => {
                        button.innerHTML = original;
                    }, 1500);
                } catch (error) {
                    window.prompt('Copy this public registration link:', link);
                }
            });
        });
    </script>
@endsection
