@extends('layouts.dashboard')
@section('title', 'Events')

@section('dashboard-content')
    @php
        $totalRegistrations = $events->sum('attendee_total');
        $checkedInTotal = $events->sum('checked_in_total');
    @endphp

    <x-ui.page-header eyebrow="Event portfolio" title="Events"
        description="Manage publishing, tickets, bookings, guests, and reporting.">
        <x-slot:actions><a href="{{ route('events.create') }}" class="btn btn-primary"><i
                    class="ti ti-plus me-2"></i>Create event</a></x-slot:actions>
    </x-ui.page-header>

    <div class="row g-3 mb-4">
        @foreach ([['All events', $events->count(), 'calendar-event'], ['Published', $events->where('status', 'published')->count(), 'world-check'], ['Registrations', $totalRegistrations, 'ticket'], ['Checked in', $checkedInTotal, 'circle-check']] as [$label, $value, $icon])
            <div class="col-sm-6 col-xl-3"><x-ui.stat-card :label="$label" :value="number_format($value)" :icon="$icon"
                    class="h-100" /></div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div
            class="card-header bg-white border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 py-3">
            <div>
                <h5 class="mb-1">Event portfolio</h5><small class="text-muted">{{ $events->count() }} records in your
                    current access scope.</small>
            </div>
            <div class="input-group" style="max-width: 320px"><span class="input-group-text bg-white"><i
                        class="ti ti-search"></i></span><input type="search" class="form-control"
                    placeholder="Search events" data-table-search></div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0" data-events-table>
                <thead class="table-light">
                    <tr>
                        <th>Event</th>
                        @if (auth()->user()->isSuperadmin())
                            <th>Organizer</th>
                        @endif
                        <th>
                            Date</th>
                        <th>Status</th>
                        <th>Tickets</th>
                        <th>Attendance</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="ems-event-thumb">
                                        @if ($event->banner_image)
                                        <img src="{{ asset('storage/' . $event->banner_image) }}" alt="">@else<i
                                                class="ti ti-calendar-event"></i>
                                        @endif
                                    </div>
                                    <div><a href="{{ route('events.show', $event) }}"
                                            class="fw-semibold text-heading">{{ $event->name }}</a>
                                        <div class="text-muted small"><i
                                                class="ti ti-map-pin me-1"></i>{{ $event->location ?: 'Location pending' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            @if (auth()->user()->isSuperadmin())
                                <td>{{ $event->admin?->name ?? 'Unassigned' }}</td>
                            @endif
                            <td>
                                <div>{{ $event->event_start_date?->format('d M Y') }}</div><small
                                    class="text-muted">{{ $event->event_start_date?->format('H:i') }}</small>
                            </td>
                            <td><span
                                    class="badge {{ $event->status === 'published' ? 'bg-label-success' : 'bg-label-secondary' }}">{{ ucfirst($event->status) }}</span>
                                <div class="small text-muted mt-1">{{ ucfirst($event->visibility) }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $event->ticketing_type === 'ticketed' ? 'Ticketed' : 'Free' }}
                                </div><small class="text-muted">Capacity {{ $event->max_attendees }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $event->checked_in_total }} / {{ $event->attendee_total }}
                                </div><small class="text-muted">checked in</small>
                            </td>
                            <td class="text-end">
                                <div class="dropdown"><button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                        data-bs-toggle="dropdown">Manage</button>
                                    <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item"
                                            href="{{ route('events.show', $event) }}">Overview</a><a class="dropdown-item"
                                            href="{{ route('events.edit', $event) }}">Edit event</a><a
                                            class="dropdown-item" href="{{ route('events.fields', $event) }}">Registration
                                            fields</a><a class="dropdown-item"
                                            href="{{ route('events.attendees', $event) }}">Attendees</a><a
                                            class="dropdown-item"
                                            href="{{ route('events.report', $event) }}">Reports</a><button
                                            class="dropdown-item" type="button"
                                            data-copy-link="{{ route('events.registration', $event->event_code) }}">Copy
                                            booking link</button></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted mb-3">No events have been created.</div><a
                                    href="{{ route('events.create') }}" class="btn btn-primary">Create first event</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.querySelector('[data-table-search]')?.addEventListener('input', event => {
            const term = event.target.value.toLowerCase();
            document.querySelectorAll('[data-events-table] tbody tr').forEach(row => row.hidden = !row.textContent
                .toLowerCase().includes(term));
        });
        document.querySelectorAll('[data-copy-link]').forEach(button => button.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(button.dataset.copyLink);
                button.textContent = 'Link copied';
            } catch {
                window.prompt('Copy booking link:', button.dataset.copyLink);
            }
        }));
    </script>
@endsection
