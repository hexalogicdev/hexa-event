<?php
namespace App\Policies;
use App\Models\Organization;
use App\Models\User;
class OrganizationPolicy
{
    public function before(User $user): ?bool { return $user->isSuperadmin() ? true : null; }
    public function viewAny(User $user): bool { return $user->activeOrganizations()->exists(); }
    public function view(User $user, Organization $organization): bool { return $user->hasOrganizationRole($organization, ['owner','administrator','event_manager','finance_manager','marketing','checkin_manager','security_officer']); }
    public function update(User $user, Organization $organization): bool { return $user->hasOrganizationRole($organization, ['owner','administrator']); }
    public function manageMembers(User $user, Organization $organization): bool { return $user->hasOrganizationRole($organization, ['owner','administrator']); }
    public function requestAllowance(User $user, Organization $organization): bool { return $organization->status === 'active' && $user->hasOrganizationRole($organization, ['owner','administrator']); }
}
