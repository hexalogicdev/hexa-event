@extends('layouts.layoutMaster')
@section('title', 'Security Officers')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div><h2 class="mb-1">Security team</h2><p class="text-muted mb-0">Create scanner-only accounts for entrance staff.</p></div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOfficer"><i class="ti ti-user-plus me-2"></i>Add security officer</button>
</div>

@if ($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
        <div><h5 class="mb-1">Staff accounts</h5><small class="text-muted">These users can only access authorized event check-in.</small></div>
        <span class="badge bg-label-primary">{{ $officers->count() }} staff</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>Name</th><th>Organizer</th><th>Access</th><th>Status</th><th>Created</th><th class="text-end">Action</th></tr></thead>
            <tbody>
                @forelse($officers as $officer)
                    <tr>
                        <td><div class="fw-semibold">{{ $officer->name }}</div><small class="text-muted">{{ $officer->email }}</small></td>
                        <td>{{ $officer->organizer?->name ?? 'Not assigned' }}</td>
                        <td><span class="badge bg-label-info">QR scanner only</span></td>
                        <td><span class="badge {{ $officer->status ? 'bg-label-success' : 'bg-label-secondary' }}">{{ $officer->status ? 'Active' : 'Inactive' }}</span></td>
                        <td>{{ $officer->created_at?->format('d M Y') }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <form method="POST" action="{{ route('security-officers.status', $officer) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $officer->status ? 0 : 1 }}">
                                    <button class="btn btn-sm {{ $officer->status ? 'btn-outline-warning' : 'btn-outline-success' }}">{{ $officer->status ? 'Suspend' : 'Activate' }}</button>
                                </form>
                                <form method="POST" action="{{ route('security-officers.destroy', $officer) }}" onsubmit="return confirm('Remove this security officer?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">No security officers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addOfficer" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <form method="POST" action="{{ route('security-officers.store') }}">@csrf
        <div class="modal-header"><div><h5 class="modal-title">Add security officer</h5><small class="text-muted">They will be sent directly to the QR scanner after login.</small></div><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body d-grid gap-3">
            @if(auth()->user()->isSuperadmin())
                <div><label class="form-label">Organizer</label><select name="organizer_id" class="form-select" required><option value="">Select organizer</option>@foreach($organizers as $organizer)<option value="{{ $organizer->id }}">{{ $organizer->name }} — {{ $organizer->email }}</option>@endforeach</select></div>
            @endif
            <div><label class="form-label">Full name</label><input name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div><label class="form-label">Email address</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
            <div><label class="form-label">Temporary password</label><input type="password" name="password" class="form-control" required></div>
            <div><label class="form-label">Confirm password</label><input type="password" name="password_confirmation" class="form-control" required></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Create account</button></div>
    </form>
</div></div></div>
@endsection
