@extends('layouts.layoutMaster')
@section('title', 'Organizers')

@section('content')
<div class="ems-page-section">
    <div class="ems-dashboard-hero ems-fade-up">
        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-end gap-4">
            <div>
                <span class="ems-badge ems-badge--info mb-3">Super admin</span>
                <h1 class="ems-dashboard-title">Organizer access management</h1>
                <p class="ems-dashboard-hero__text mb-0">Create controlled organizer accounts. Public account registration remains disabled.</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createOrganizer"><i class="ti ti-user-plus me-2"></i>Onboard organizer</button>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger rounded-4 border-0">{{ $errors->first() }}</div>
@endif

<div class="ems-surface">
    <div class="ems-card-header d-flex justify-content-between align-items-center gap-3">
        <div><h2 class="ems-section-title h4 mb-1">Organizer accounts</h2><p class="ems-section-subtitle mb-0">Ownership and event totals at a glance.</p></div>
        <span class="ems-badge ems-badge--info">{{ $organizers->count() }} organizers</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Organizer</th><th>Events</th><th>Status</th><th>Created</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                @forelse ($organizers as $organizer)
                    <tr>
                        <td><div class="fw-semibold">{{ $organizer->name }}</div><div class="text-muted small">{{ $organizer->email }}</div></td>
                        <td>{{ $organizer->events_count }}</td>
                        <td><span class="ems-badge {{ $organizer->status ? 'ems-badge--success' : 'ems-badge--warning' }}">{{ $organizer->status ? 'Active' : 'Suspended' }}</span></td>
                        <td>{{ $organizer->created_at?->format('d M Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('changeStatus', ['User', $organizer->id]) }}" class="btn btn-sm btn-outline-primary">{{ $organizer->status ? 'Suspend' : 'Activate' }}</a>
                            <form action="{{ route('User.destroy', $organizer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this organizer account?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Remove</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">No organizers have been onboarded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="createOrganizer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-4 border-0">
        <form action="{{ route('User.store') }}" method="POST">@csrf
            <div class="modal-header"><div><h5 class="modal-title">Onboard organizer</h5><small class="text-muted">Create login credentials for a trusted event organizer.</small></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body d-grid gap-3">
                <div><label class="form-label">Full name</label><input class="form-control" name="name" value="{{ old('name') }}" required></div>
                <div><label class="form-label">Email address</label><input type="email" class="form-control" name="email" value="{{ old('email') }}" required></div>
                <div><label class="form-label">Temporary password</label><input type="password" class="form-control" name="password" required><div class="form-text">At least 8 characters with upper/lowercase and a special character.</div></div>
                <div><label class="form-label">Confirm password</label><input type="password" class="form-control" name="password_confirmation" required></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Create organizer</button></div>
        </form>
    </div></div>
</div>
@endsection
