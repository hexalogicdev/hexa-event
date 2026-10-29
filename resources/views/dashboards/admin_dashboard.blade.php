@extends('layouts.dashboard')
@section('title', 'Dashboard')

@section('dashboard-content')
@php $isAdmin = auth()->user()->isSuperadmin(); @endphp
<div class="ems-admin-page">
  <x-ui.page-header :eyebrow="$isAdmin ? 'Platform overview' : 'Organizer overview'" :title="'Welcome back, '.auth()->user()->name" :description="$isAdmin ? 'Monitor organizers, events, registrations, and venue activity across the platform.' : 'Manage your events, registrations, ticket inventory, and check-in activity.'">
    <x-slot:actions><a href="{{ route('events.index') }}" class="btn btn-outline-secondary"><i class="ti ti-calendar-event me-2"></i>All events</a><a href="{{ route('events.create') }}" class="btn btn-primary"><i class="ti ti-plus me-2"></i>Create event</a></x-slot:actions>
  </x-ui.page-header>

  <div class="ems-kpi-grid">
    <div class="ems-kpi-card"><span class="ems-kpi-icon is-blue"><i class="ti ti-calendar-event"></i></span><div><small>Total events</small><strong>{{ number_format($eventsCount) }}</strong><span>{{ $publishedEvents }} published, {{ $draftEvents }} draft</span></div></div>
    <div class="ems-kpi-card"><span class="ems-kpi-icon is-violet"><i class="ti ti-users"></i></span><div><small>Registrations</small><strong>{{ number_format($attendees) }}</strong><span>Across visible events</span></div></div>
    <div class="ems-kpi-card"><span class="ems-kpi-icon is-green"><i class="ti ti-user-check"></i></span><div><small>Checked in</small><strong>{{ number_format($checkedInCount) }}</strong><span>{{ $attendanceRate }}% attendance rate</span></div></div>
    <div class="ems-kpi-card"><span class="ems-kpi-icon is-amber"><i class="ti ti-{{ $isAdmin ? 'building-community' : 'currency-pound' }}"></i></span><div><small>{{ $isAdmin ? 'Organizers' : 'Recorded revenue' }}</small><strong>{{ $isAdmin ? number_format($organizerCount) : 'GBP '.number_format($revenue, 2) }}</strong><span>{{ $isAdmin ? 'Managed accounts' : 'Completed registrations' }}</span></div></div>
  </div>

  <div class="row g-4">
    <div class="col-xl-8"><section class="ems-admin-card h-100">
      <div class="ems-admin-card__header"><div><h2>Recent events</h2><p>Upcoming activity and registration progress.</p></div><a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-secondary">View all</a></div>
      <div class="table-responsive"><table class="table ems-admin-table align-middle mb-0"><thead><tr><th>Event</th><th>Date</th><th>Status</th><th>Registrations</th><th class="text-end">Action</th></tr></thead><tbody>
      @forelse($recentEvents as $event)
        <tr><td><div class="ems-table-primary">{{ $event->name }}</div><div class="ems-table-secondary"><i class="ti ti-map-pin"></i>{{ $event->location ?: 'Location not set' }}</div></td><td><div class="ems-table-primary">{{ $event->event_start_date?->format('d M Y') }}</div><div class="ems-table-secondary">{{ $event->event_start_date?->format('H:i') }}</div></td><td><span class="ems-status-pill {{ $event->status === 'published' ? 'is-success' : 'is-neutral' }}">{{ ucfirst($event->status) }}</span></td><td><div class="ems-table-primary">{{ number_format($event->attendees_count ?? 0) }} / {{ number_format($event->max_attendees) }}</div><div class="ems-progress"><span style="width:{{ min(100, (($event->attendees_count ?? 0) / max(1, $event->max_attendees)) * 100) }}%"></span></div></td><td class="text-end"><a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-light">Manage</a></td></tr>
      @empty
        <tr><td colspan="5"><div class="ems-table-empty"><i class="ti ti-calendar-plus"></i><strong>No events yet</strong><span>Create your first event to begin accepting registrations.</span><a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">Create event</a></div></td></tr>
      @endforelse
      </tbody></table></div>
    </section></div>
    <div class="col-xl-4">
      <section class="ems-admin-card mb-4"><div class="ems-admin-card__header"><div><h2>Quick actions</h2><p>Common management tasks.</p></div></div><div class="ems-quick-actions">
        <a href="{{ route('events.create') }}"><span><i class="ti ti-calendar-plus"></i></span><div><strong>Create an event</strong><small>Configure tickets and registration</small></div><i class="ti ti-chevron-right"></i></a>
        @if($isAdmin)<a href="{{ route('User.index') }}"><span><i class="ti ti-building-community"></i></span><div><strong>Manage organizers</strong><small>Onboard and control access</small></div><i class="ti ti-chevron-right"></i></a>@endif
        <a href="{{ route('security-officers.index') }}"><span><i class="ti ti-shield-lock"></i></span><div><strong>Security team</strong><small>Manage scanner accounts</small></div><i class="ti ti-chevron-right"></i></a>
        <a href="{{ route('attendee.qr.scan') }}"><span><i class="ti ti-scan"></i></span><div><strong>Open scanner</strong><small>Process venue check-ins</small></div><i class="ti ti-chevron-right"></i></a>
      </div></section>
      <section class="ems-admin-card"><div class="ems-admin-card__header"><div><h2>Registration status</h2><p>Live attendance summary.</p></div></div><div class="ems-attendance-summary"><div><span>Checked in</span><strong>{{ number_format($checkedInCount) }}</strong></div><div><span>Awaiting arrival</span><strong>{{ number_format($pendingCount) }}</strong></div><div class="ems-attendance-bar"><span style="width:{{ $attendanceRate }}%"></span></div><small>{{ $attendanceRate }}% of registered attendees have checked in.</small></div></section>
    </div>
  </div>

  <section class="ems-admin-card mt-4"><div class="ems-admin-card__header"><div><h2>Latest registrations</h2><p>Most recently added attendees.</p></div></div><div class="table-responsive"><table class="table ems-admin-table align-middle mb-0"><thead><tr><th>Attendee</th><th>Event</th><th>Registration</th><th>Check-in</th></tr></thead><tbody>
  @forelse($recentAttendees as $attendee)
    <tr><td><div class="d-flex align-items-center gap-3"><span class="ems-table-avatar">{{ strtoupper(substr($attendee->name,0,1)) }}</span><div><div class="ems-table-primary">{{ $attendee->name }}</div><div class="ems-table-secondary">{{ $attendee->email }}</div></div></div></td><td><div class="ems-table-primary">{{ $attendee->event?->name }}</div></td><td>{{ $attendee->created_at?->format('d M Y, H:i') }}</td><td><span class="ems-status-pill {{ $attendee->checked_in ? 'is-success' : 'is-warning' }}">{{ $attendee->checked_in ? 'Checked in' : 'Pending' }}</span></td></tr>
  @empty<tr><td colspan="4" class="text-center text-muted py-5">No registrations have been received.</td></tr>@endforelse
  </tbody></table></div></section>
</div>
@endsection
