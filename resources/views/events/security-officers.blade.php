@extends('layouts.layoutMaster')
@section('title', 'Event Security Team')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <a href="{{ route('events.show', $event) }}" class="text-muted small">&larr; Back to event</a>
        <h2 class="mb-1 mt-2">Security team</h2>
        <p class="text-muted mb-0">Assign scanner access for {{ $event->name }}.</p>
    </div>
    <a href="{{ route('security-officers.index') }}" class="btn btn-outline-primary">Manage staff accounts</a>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<form method="POST" action="{{ route('events.security-officers.update', $event) }}">
    @csrf @method('PUT')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-1">Available security officers</h5>
            <small class="text-muted">Only selected officers can scan tickets for this event.</small>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light"><tr><th style="width: 70px">Access</th><th>Name</th><th>Email</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($officers as $officer)
                    <tr>
                        <td><input class="form-check-input" type="checkbox" name="officer_ids[]" value="{{ $officer->id }}" @checked(in_array($officer->id, $assignedIds))></td>
                        <td class="fw-semibold">{{ $officer->name }}</td>
                        <td>{{ $officer->email }}</td>
                        <td><span class="badge bg-label-success">Active</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-5">No active security officers are available for this organizer.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white d-flex justify-content-end gap-2">
            <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary">Cancel</a>
            <button class="btn btn-primary">Save assignments</button>
        </div>
    </div>
</form>
@endsection
