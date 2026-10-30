@extends('layouts.layoutMaster')
@section('title', 'Attendees')

@section('content')
    <x-ui.event-nav :event="$event" />
    @php
        $total = $attendees->count();
        $checkedIn = $attendees->where('checked_in', true)->count();
        $pending = $attendees->where('checked_in', false)->count();
    @endphp

    <div class="ems-page-section">
        <div class="ems-dashboard-hero ems-fade-up">
            <div class="d-flex flex-column flex-xl-row justify-content-between gap-4 align-items-xl-end">
                <div>
                    <span class="ems-badge ems-badge--info mb-3">Attendees / Registrations</span>
                    <h5 class="ems-dashboard-hero__text mb-3">{{ $event->name }}</h5>
                    <p class="ems-dashboard-hero__text mb-0">
                        Search attendees, review their answers, manually mark check-ins, and export a clean attendance
                        list for reporting.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('events.report', $event->id) }}" class="btn btn-outline-light">Attendance report</a>
                    <a href="{{ route('events.attendees.export', $event->id) }}" class="btn btn-primary">Export list</a>
                </div>
            </div>

            <div class="ems-dashboard-metrics">
                <div class="ems-dashboard-metric"><div class="label">Total attendees</div><div class="value">{{ $total }}</div></div>
                <div class="ems-dashboard-metric"><div class="label">Checked in</div><div class="value">{{ $checkedIn }}</div></div>
                <div class="ems-dashboard-metric"><div class="label">Pending</div><div class="value">{{ $pending }}</div></div>
                <div class="ems-dashboard-metric"><div class="label">Event capacity</div><div class="value">{{ $event->max_attendees }}</div></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-12">
            <div class="ems-surface">
                <div class="ems-card-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <div>
                        <h2 class="ems-section-title h4 mb-1">Registration management</h2>
                        <p class="ems-section-subtitle mb-0">Filter attendees and mark check-in from one clean list.</p>
                    </div>
                    <form class="d-flex flex-wrap gap-2" method="GET">
                        <input type="search" class="form-control" name="q" value="{{ request('q') }}" placeholder="Search name, email, QR">
                        <select name="status" class="form-select">
                            <option value="">All statuses</option>
                            <option value="checked_in" @selected(request('status') === 'checked_in')>Checked in</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                        </select>
                        <button class="btn btn-primary" type="submit">Filter</button>
                    </form>
                </div>
                <div class="ems-card-body">
                    <div class="table-responsive">
                        <table class="table ems-table align-middle">
                            <thead>
                                <tr>
                                    <th>Attendee</th>
                                    <th>Booking ref</th>
                                    <th>Status</th>
                                    <th>Answers</th>
                                    <th>Check-in</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendees as $attendee)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $attendee->name }}</div>
                                            <div class="text-muted small">{{ $attendee->email }} · {{ $attendee->phone ?: 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $attendee->qr_id }}</div>
                                            <div class="text-muted small">QR reference</div>
                                        </td>
                                        <td>
                                            <span class="ems-badge {{ $attendee->checked_in ? 'ems-badge--success' : 'ems-badge--muted' }}">
                                                {{ $attendee->checked_in ? 'Checked in' : 'Pending' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if (!empty($attendee->custom_answers))
                                                <details>
                                                    <summary class="text-white">View answers</summary>
                                                    <div class="mt-2 d-grid gap-1">
                                                        @foreach ($attendee->custom_answers as $key => $value)
                                                            <div class="text-muted small">
                                                                <strong>{{ $key }}:</strong>
                                                                {{ is_array($value) ? implode(', ', $value) : $value }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </details>
                                            @else
                                                <span class="text-muted">No custom answers</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-white">{{ $attendee->checked_in_at?->format('d M Y') ?? 'Not checked in' }}</div>
                                            <div class="text-muted small">{{ $attendee->checked_in_at?->format('h:i A') ?? '—' }}</div>
                                        </td>
                                        <td class="text-end">
                                            @if ($attendee->checked_in)
                                                <span class="ems-badge ems-badge--success">Already Checked In</span>
                                            @else
                                                <form action="{{ route('attendees.manual-check-in', $attendee->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-light btn-sm"
                                                        onclick="return confirm('Mark this attendee as checked in?');">
                                                        Mark checked in
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="ems-empty-state text-center">
                                                <div class="ems-empty-state__title">No attendees yet</div>
                                                <p class="mb-0">Bookings will appear here as people register.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
