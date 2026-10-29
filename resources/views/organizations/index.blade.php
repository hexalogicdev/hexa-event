@extends('layouts.dashboard')
@section('title','Organizations')
@section('dashboard-content')
<x-ui.page-header eyebrow="Platform tenancy" title="Organizations" description="Manage isolated organizer workspaces and their ownership.">
    @if(auth()->user()->isSuperadmin())
        <x-slot:actions>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createOrganization"><i class="ti ti-building-plus me-2"></i>Create organization</button>
        </x-slot:actions>
    @endif
</x-ui.page-header>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<x-ui.card title="Organization directory" description="Membership and event totals are scoped to your authorized access." body-class="p-0">
<div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Organization</th><th>Owner</th><th>Members</th><th>Events</th><th>Status</th><th>Created</th></tr></thead><tbody>
@forelse($organizations as $organization)<tr><td><a href="{{ route('organizations.show',$organization) }}"><strong>{{ $organization->name }}</strong></a><div class="text-muted small">{{ $organization->email }}</div></td><td>{{ $organization->owner?->name ?? 'Invitation pending' }}</td><td>{{ number_format($organization->memberships_count) }}</td><td>{{ number_format($organization->events_count) }}</td><td><x-ui.status :tone="match($organization->status){'active'=>'success','suspended'=>'warning','archived'=>'neutral',default=>'info'}">{{ ucfirst($organization->status) }}</x-ui.status></td><td>{{ $organization->created_at?->format('d M Y') }}</td></tr>
@empty<tr><td colspan="6"><x-ui.empty-state title="No organizations available" description="Create the first organization to begin secure onboarding." icon="building" /></td></tr>@endforelse
</tbody></table></div><div class="p-3">{{ $organizations->links() }}</div>
</x-ui.card>
@if(auth()->user()->isSuperadmin())<div class="modal fade" id="createOrganization" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><form class="modal-content" method="POST" action="{{ route('organizations.store') }}">@csrf<div class="modal-header"><div><h2 class="modal-title h5">Create organization</h2><p class="text-muted small mb-0">The owner receives a secure, single-use invitation.</p></div><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div><div class="modal-body d-grid gap-3"><div><label class="form-label">Organization name</label><input class="form-control" name="name" value="{{ old('name') }}" required></div><div><label class="form-label">Owner name</label><input class="form-control" name="owner_name" value="{{ old('owner_name') }}" required></div><div><label class="form-label">Owner email</label><input type="email" class="form-control" name="owner_email" value="{{ old('owner_email') }}" required></div><div class="row g-3"><div class="col-4"><label class="form-label">Country</label><input class="form-control text-uppercase" name="country" maxlength="2" value="{{ old('country','GB') }}"></div><div class="col-8"><label class="form-label">Timezone</label><select class="form-select" name="timezone" required><option value="Europe/London">Europe/London</option><option value="UTC">UTC</option></select></div></div></div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Create and invite</button></div></form></div></div>@endif
@endsection

