@extends('layouts.layoutMaster')
@section('title', 'Attendance Report')

@section('content')
    <x-ui.event-nav :event="$event" />
    <div class="ems-page-section">
        <div class="ems-dashboard-hero ems-fade-up">
            <div class="d-flex flex-column flex-xl-row justify-content-between gap-4 align-items-xl-end">
                <div>
                    <span class="ems-badge ems-badge--info mb-3">Reports</span>
                    <h5 class="ems-dashboard-hero__text mb-3">{{ $event->name }}</h5>
                    <p class="ems-dashboard-hero__text mb-0">
                        Attendance overview with checked-in and pending breakdown for quick decision-making.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('events.attendees.export', $event->id) }}" class="btn btn-primary">Download CSV</a>
                    <a href="{{ route('events.attendees', $event->id) }}" class="btn btn-outline-light">Back to attendees</a>
                </div>
            </div>

            <div class="ems-dashboard-metrics">
                <div class="ems-dashboard-metric"><div class="label">Registrations</div><div class="value">{{ $attendees->count() }}</div></div>
                <div class="ems-dashboard-metric"><div class="label">Checked in</div><div class="value">{{ $checkedInCount }}</div></div>
                <div class="ems-dashboard-metric"><div class="label">Pending</div><div class="value">{{ $pendingCount }}</div></div>
                <div class="ems-dashboard-metric"><div class="label">Check-in rate</div><div class="value">{{ $attendees->count() ? round(($checkedInCount / $attendees->count()) * 100) : 0 }}%</div></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="ems-dashboard-card">
                <div class="ems-dashboard-card__head">
                    <div>
                        <h2>Attendance summary</h2>
                        <p>Simple report cards for the event.</p>
                    </div>
                </div>
                <div class="ems-dashboard-card__body d-grid gap-3">
                    <div class="ems-insight-block">
                        <div class="label">Event window</div>
                        <div class="value">{{ $event->event_start_date?->format('d M Y') }} - {{ $event->event_end_date?->format('d M Y') }}</div>
                    </div>
                    <div class="ems-insight-block">
                        <div class="label">Registration window</div>
                        <div class="value">{{ $event->booking_start_date?->format('d M Y') }} - {{ $event->booking_end_date?->format('d M Y') }}</div>
                    </div>
                    <div class="ems-insight-block">
                        <div class="label">Event code</div>
                        <div class="value">{{ $event->event_code }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="ems-dashboard-card">
                <div class="ems-dashboard-card__head">
                    <div>
                        <h2>Latest attendee states</h2>
                        <p>Quick recent register/check-in view.</p>
                    </div>
                </div>
                <div class="ems-dashboard-card__body">
                    <div class="ems-attendee-list">
                        @forelse ($attendees->take(6) as $attendee)
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
                                <p class="mb-0">The report will populate once attendees begin registering.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
