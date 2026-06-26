@extends('layouts.layoutMaster')
@section('title', 'Dashboard')

@section('content')
    @php
        $isAdmin = auth()->user()?->role?->name === 'admin';
        $scopeLabel = $isAdmin ? 'Owner workspace' : 'Organizer workspace';
        $primaryEvent = $recentEvents->first();
        $attendanceRate = $attendees > 0 ? round(($checkedInCount / $attendees) * 100) : 0;
    @endphp

    <div class="ems-dashboard-shell">
        <div class="ems-dashboard-frame">
            <div class="ems-dashboard-topbar">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    {{-- <img src="{{ asset('assets/img/hexa-logo.png') }}" alt="HexaLogicDev" class="ems-dashboard-brand"> --}}
                    <div>
                        <div class="ems-dashboard-kicker">{{ $scopeLabel }}</div>
                        <h1 class="ems-dashboard-title mb-0">Event management dashboard</h1>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('events.index') }}" class="btn btn-outline-light btn-sm">View events</a>
                    <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">Create event</a>
                </div>
            </div>

            <div class="row g-4 align-items-start">
                <div class="col-12">
                    <div class="ems-dashboard-hero">
                        <div class="d-flex flex-column flex-xl-row justify-content-between gap-4">
                            <div class="pe-xl-5">
                                <span class="ems-badge ems-badge--info mb-3">International workspace</span>
                                <h5 class="ems-dashboard-hero__text">A calm, premium operations view for your events.</h5>
                                <p class="ems-dashboard-hero__text mb-0">
                                    Monitor event publishing, bookings, and QR check-ins from a clean dashboard that feels
                                    built for global event teams.
                                </p>
                            </div>

                            <div class="ems-dashboard-mini">
                                <div class="ems-dashboard-mini__label">Primary event</div>
                                <div class="ems-dashboard-mini__value">{{ $primaryEvent?->name ?? 'No published event yet' }}</div>
                                <div class="ems-dashboard-mini__meta">{{ $primaryEvent?->location ?: 'Create your first event to start tracking activity.' }}</div>
                            </div>
                        </div>

                        <div class="ems-dashboard-metrics">
                            <div class="ems-dashboard-metric">
                                <div class="label">Total events</div>
                                <div class="value">{{ $eventsCount }}</div>
                            </div>
                            <div class="ems-dashboard-metric">
                                <div class="label">Published</div>
                                <div class="value">{{ $publishedEvents }}</div>
                            </div>
                            <div class="ems-dashboard-metric">
                                <div class="label">Draft</div>
                                <div class="value">{{ $draftEvents }}</div>
                            </div>
                            <div class="ems-dashboard-metric">
                                <div class="label">Upcoming</div>
                                <div class="value">{{ $upcomingEvents->count() }}</div>
                            </div>
                            <div class="ems-dashboard-metric">
                                <div class="label">Registrations</div>
                                <div class="value">{{ $attendees }}</div>
                            </div>
                            <div class="ems-dashboard-metric">
                                <div class="label">Checked in</div>
                                <div class="value">{{ $checkedInCount }}</div>
                            </div>
                            <div class="ems-dashboard-metric">
                                <div class="label">Pending</div>
                                <div class="value">{{ $pendingCount }}</div>
                            </div>
                            <div class="ems-dashboard-metric">
                                <div class="label">Revenue</div>
                                <div class="value">{{ number_format($revenue ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-7">
                    <div class="ems-dashboard-card h-100">
                        <div class="ems-dashboard-card__head">
                            <div>
                                <h2>Recent events</h2>
                                <p>Bookings, venue details, and audience counts.</p>
                            </div>
                            <a href="{{ route('events.create') }}" class="btn btn-outline-light btn-sm">New event</a>
                        </div>
                        <div class="ems-dashboard-card__body">
                            <div class="ems-event-list">
                                @forelse ($recentEvents as $event)
                                    <article class="ems-event-row">
                                        <div class="ems-event-row__icon">
                                            <i class="ti ti-calendar-event"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between gap-3 flex-wrap">
                                                <div>
                                                    <div class="ems-event-row__title">{{ $event->name }}</div>
                                                    <div class="ems-event-row__meta">{{ $event->location ?: 'Location not set' }}</div>
                                                </div>
                                                <span class="ems-pill-dark">
                                                    {{ $event->event_start_date?->format('d M Y') ?? 'TBA' }}
                                                </span>
                                            </div>
                                            <div class="ems-event-row__footer">
                                                <span>
                                                    Booking window
                                                    {{ $event->booking_start_date?->format('d M') ?? 'TBA' }}
                                                    -
                                                    {{ $event->booking_end_date?->format('d M') ?? 'TBA' }}
                                                </span>
                                                <span>{{ $event->attendees_count ?? 0 }} attendees</span>
                                            </div>
                                        </div>
                                    </article>
                                @empty
                                    <div class="ems-empty-state">
                                        <div class="ems-empty-state__title">No events yet</div>
                                        <p class="mb-0">Create an event to populate this dashboard.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-5">
                    <div class="d-grid gap-4 h-100">
                        <div class="ems-dashboard-card ems-dashboard-card--soft">
                            <div class="ems-dashboard-card__head">
                                <div>
                                    <h2>Booking overview</h2>
                                    <p>Quick operational snapshot.</p>
                                </div>
                            </div>
                            <div class="ems-dashboard-card__body d-grid gap-3">
                                <div class="ems-insight-block">
                                    <div class="label">Workspace mode</div>
                                    <div class="value">{{ $isAdmin ? 'Full platform access' : 'My event portfolio' }}</div>
                                    <div class="meta">{{ $isAdmin ? 'All events and attendees are visible.' : 'Only your own events and attendees are visible.' }}</div>
                                </div>

                                <div class="ems-insight-block">
                                    <div class="label">Attendance rate</div>
                                    <div class="value">{{ $attendanceRate }}%</div>
                                    <div class="meta">Calculated from checked-in attendees in the current scope.</div>
                                </div>

                                <div class="ems-insight-block">
                                    <div class="label">Quick actions</div>
                                    <div class="d-grid gap-2 mt-3">
                                        <a href="{{ route('events.create') }}" class="btn btn-primary">Create new event</a>
                                        <a href="{{ route('events.index') }}" class="btn btn-outline-light">Open event list</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ems-dashboard-card ems-dashboard-card--soft">
                            <div class="ems-dashboard-card__head">
                                <div>
                                    <h2>Upcoming events</h2>
                                    <p>Next items on your schedule.</p>
                                </div>
                            </div>
                            <div class="ems-dashboard-card__body d-grid gap-3">
                                @forelse ($upcomingEvents->take(3) as $event)
                                    <div class="ems-insight-block">
                                        <div class="label">{{ $event->event_start_date?->format('d M Y') }}</div>
                                        <div class="value">{{ $event->name }}</div>
                                        <div class="meta">{{ $event->location ?: 'Location not set' }}</div>
                                    </div>
                                @empty
                                    <div class="ems-empty-state">
                                        <div class="ems-empty-state__title">No upcoming events</div>
                                        <p class="mb-0">Publish an event to see it here.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 align-items-start">
                <div class="col-xl-8">
                    <div class="ems-dashboard-card h-100">
                        <div class="ems-dashboard-card__head">
                            <div>
                                <h2>Recent registrations</h2>
                                <p>Latest registrations and status badges.</p>
                            </div>
                        </div>
                        <div class="ems-dashboard-card__body">
                            <div class="ems-attendee-list">
                                @forelse ($recentAttendees as $attendee)
                                    <article class="ems-attendee-row">
                                        <div class="ems-avatar">
                                            {{ strtoupper(substr($attendee->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between gap-3 flex-wrap">
                                                <div>
                                                    <div class="ems-event-row__title">{{ $attendee->name }}</div>
                                                    <div class="ems-event-row__meta">{{ $attendee->email }}</div>
                                                </div>
                                                <span class="ems-status {{ $attendee->checked_in ? 'is-success' : 'is-waiting' }}">
                                                    {{ $attendee->checked_in ? 'Checked in' : 'Pending' }}
                                                </span>
                                            </div>
                                            <div class="ems-attendee-row__meta">
                                                <span>{{ $attendee->event?->name ?? 'Event' }}</span>
                                                <span>{{ $attendee->checked_in_at?->format('h:i A') ?? 'Awaiting check-in' }}</span>
                                            </div>
                                        </div>
                                    </article>
                                @empty
                                    <div class="ems-empty-state">
                                        <div class="ems-empty-state__title">No attendee data yet</div>
                                        <p class="mb-0">Bookings will appear here as guests register.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="ems-dashboard-card ems-dashboard-card--soft h-100">
                        <div class="ems-dashboard-card__head">
                            <div>
                                <h2>Workspace snapshot</h2>
                                <p>Shortcuts for daily operations.</p>
                            </div>
                        </div>
                        <div class="ems-dashboard-card__body d-grid gap-3">
                            <div class="ems-insight-block">
                                <div class="label">Scope</div>
                                <div class="value">{{ $isAdmin ? 'All events' : 'Organizer events' }}</div>
                                <div class="meta">The dashboard respects your current role.</div>
                            </div>

                            <div class="ems-insight-block">
                                <div class="label">Latest activity</div>
                                <div class="value">{{ $recentAttendees->first()?->name ?? 'No activity yet' }}</div>
                                <div class="meta">{{ $recentAttendees->first()?->event?->name ?? 'Recent registrations will appear here.' }}</div>
                            </div>

                            <div class="ems-insight-block">
                                <div class="label">Performance</div>
                                <div class="value">{{ $eventsCount > 0 ? 'Live' : 'Starting out' }}</div>
                                <div class="meta">Keep adding events to populate the premium dashboard view.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
