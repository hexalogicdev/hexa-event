@extends('layouts.layoutMaster')
@section('title', 'Events')

@section('content')
    @php
        $totalEvents = $events->count();
        $publishedEvents = $events->where('status', 'published')->count();
        $draftEvents = $events->where('status', 'draft')->count();
        $upcomingEvents = $events->where('is_upcoming', true)->count();
        $totalRegistrations = $events->sum('attendee_total');
        $checkedInTotal = $events->sum('checked_in_total');
        $pendingTotal = max($totalRegistrations - $checkedInTotal, 0);
    @endphp

    <div class="ems-page-section">
        <div class="ems-dashboard-hero ems-fade-up">
            <div class="d-flex flex-column flex-xl-row justify-content-between gap-4 align-items-xl-end">
                <div>
                    <span class="ems-badge ems-badge--info mb-3">Organizer dashboard</span>
                    {{-- <h1 class="ems-dashboard-hero__title mb-3">Everything about your events in one control center.</h1> --}}
                    <p class="ems-dashboard-hero__text mb-0">
                        Monitor published and draft events, track registrations, manage attendee check-ins, and keep your
                        public booking flow clean and professional.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('events.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>New event
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light">
                        <i class="ti ti-layout-dashboard me-2"></i>Dashboard
                    </a>
                </div>
            </div>

            <div class="ems-dashboard-metrics">
                <div class="ems-dashboard-metric">
                    <div class="label">Total events</div>
                    <div class="value">{{ $totalEvents }}</div>
                </div>
                <div class="ems-dashboard-metric">
                    <div class="label">Active / published</div>
                    <div class="value">{{ $publishedEvents }}</div>
                </div>
                <div class="ems-dashboard-metric">
                    <div class="label">Draft events</div>
                    <div class="value">{{ $draftEvents }}</div>
                </div>
                <div class="ems-dashboard-metric">
                    <div class="label">Upcoming events</div>
                    <div class="value">{{ $upcomingEvents }}</div>
                </div>
                <div class="ems-dashboard-metric">
                    <div class="label">Registrations</div>
                    <div class="value">{{ $totalRegistrations }}</div>
                </div>
                <div class="ems-dashboard-metric">
                    <div class="label">Checked in</div>
                    <div class="value">{{ $checkedInTotal }}</div>
                </div>
                <div class="ems-dashboard-metric">
                    <div class="label">Pending</div>
                    <div class="value">{{ $pendingTotal }}</div>
                </div>
                <div class="ems-dashboard-metric">
                    <div class="label">Availability</div>
                    <div class="value">{{ $totalEvents ? round(($checkedInTotal / max($totalRegistrations, 1)) * 100) : 0 }}%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ems-surface">
                <div class="ems-card-header d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <h2 class="ems-section-title h4 mb-1">Upcoming and recent events</h2>
                        <p class="ems-section-subtitle mb-0">Use each event card to jump into details, fields, attendees, or reports.</p>
                    </div>
                    <span class="ems-badge ems-badge--muted">{{ $events->count() }} records</span>
                </div>
                <div class="ems-card-body">
                    <div class="ems-grid ems-grid--2">
                        @forelse ($events as $event)
                            <article class="ems-card ems-card--raised overflow-hidden">
                                <div class="p-0">
                                    <div class="position-relative">
                                        @if ($event->banner_image)
                                            <img src="{{ asset('storage/' . $event->banner_image) }}"
                                                alt="{{ $event->name }}" class="w-100" style="height: 180px; object-fit: cover;">
                                        @else
                                            <div class="ems-hero" style="border-radius: 0; min-height: 180px;"></div>
                                        @endif
                                        <div class="position-absolute top-0 start-0 m-3">
                                            <span class="ems-badge {{ $event->status === 'published' ? 'ems-badge--success' : 'ems-badge--muted' }}">
                                                {{ ucfirst($event->status ?? 'draft') }}
                                            </span>
                                        </div>
                                        <div class="position-absolute top-0 end-0 m-3">
                                            <span class="ems-pill-dark">
                                                {{ $event->visibility === 'private' ? 'Private' : 'Public' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                                            <div>
                                                <h3 class="h5 mb-1 text-white">{{ $event->name }}</h3>
                                                <p class="text-muted mb-0">{{ $event->location ?: 'Location not set' }}</p>
                                            </div>
                                            <span class="ems-badge ems-badge--info">{{ $event->is_upcoming ? 'Upcoming' : 'Live' }}</span>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <span class="ems-badge ems-badge--muted">{{ $event->event_start_date?->format('d M Y') }}</span>
                                            <span class="ems-badge ems-badge--muted">{{ $event->attendee_total }} registrations</span>
                                            <span class="ems-badge ems-badge--muted">{{ $event->checked_in_total }} checked in</span>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <a href="{{ route('events.show', $event->id) }}" class="btn btn-primary btn-sm">View event</a>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <a href="{{ route('events.edit', $event->id) }}" class="btn btn-outline-light btn-sm w-100">Edit</a>
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{ route('events.fields', $event->id) }}" class="btn btn-outline-light btn-sm w-100">Manage fields</a>
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{ route('events.attendees', $event->id) }}" class="btn btn-outline-light btn-sm w-100">Attendees</a>
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{ route('events.report', $event->id) }}" class="btn btn-outline-light btn-sm w-100">Report</a>
                                                </div>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <button type="button"
                                                        class="btn btn-outline-light btn-sm w-100 ems-copy-link"
                                                        data-copy-link="{{ route('events.registration', $event->event_code) }}">
                                                        Copy link
                                                    </button>
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{ route('events.attendees.export', $event->id) }}" class="btn btn-outline-light btn-sm w-100">Export</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="ems-empty-state">
                                <div class="ems-empty-state__title">No events yet</div>
                                <p class="mb-3">Create your first event to start tracking registrations and attendance.</p>
                                <a href="{{ route('events.create') }}" class="btn btn-primary">Create event</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="d-grid gap-4">
                <div class="ems-dashboard-card">
                    <div class="ems-dashboard-card__head">
                        <div>
                            <h2>Quick actions</h2>
                            <p>Common event tasks.</p>
                        </div>
                    </div>
                    <div class="ems-dashboard-card__body d-grid gap-2">
                        <a href="{{ route('events.create') }}" class="btn btn-primary">Create event</a>
                        <a href="{{ route('events.index') }}" class="btn btn-outline-light">Refresh list</a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-light">Open organizer dashboard</a>
                    </div>
                </div>

                <div class="ems-dashboard-card">
                    <div class="ems-dashboard-card__head">
                        <div>
                            <h2>Registration statistics</h2>
                            <p>Helpful at-a-glance summary.</p>
                        </div>
                    </div>
                    <div class="ems-dashboard-card__body d-grid gap-3">
                        <div class="ems-insight-block">
                            <div class="label">Total registrations</div>
                            <div class="value">{{ $totalRegistrations }}</div>
                            <div class="meta">Across all visible events.</div>
                        </div>
                        <div class="ems-insight-block">
                            <div class="label">Attendance summary</div>
                            <div class="value">{{ $checkedInTotal }} / {{ $totalRegistrations ?: 0 }}</div>
                            <div class="meta">Checked-in attendees vs total registrations.</div>
                        </div>
                        <div class="ems-insight-block">
                            <div class="label">Pending check-ins</div>
                            <div class="value">{{ $pendingTotal }}</div>
                            <div class="meta">Guests not yet checked in.</div>
                        </div>
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
