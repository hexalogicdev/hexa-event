<?php

namespace App\Services\Organizations;

use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Support\Str;

class LegacyOrganizationBackfillService
{
    public function run(bool $dryRun = true): array
    {
        $report = ['organizers' => 0, 'organizations_created' => 0, 'owner_memberships_created' => 0, 'events_mapped' => 0, 'security_memberships_created' => 0];

        User::query()->where('user_type', 'organizer')->orderBy('id')->eachById(function (User $organizer) use (&$report, $dryRun) {
            $report['organizers']++;
            $organization = Organization::query()->where('owner_id', $organizer->id)->first();

            if (! $organization) {
                $report['organizations_created']++;
                if ($dryRun) {
                    $report['owner_memberships_created']++;
                    $report['events_mapped'] += $organizer->events()->whereNull('organization_id')->count();
                    $report['security_memberships_created'] += $organizer->securityOfficers()->count();
                    return;
                }

                $organization = Organization::create([
                    'owner_id' => $organizer->id,
                    'name' => $organizer->name,
                    'slug' => Str::slug($organizer->name).'-'.$organizer->id,
                    'status' => $organizer->status ? 'active' : 'suspended',
                    'email' => $organizer->email,
                    'country' => $organizer->country,
                    'timezone' => $organizer->timezone ?: 'UTC',
                    'activated_at' => $organizer->status ? now() : null,
                    'suspended_at' => $organizer->status ? null : now(),
                ]);
            }

            if (! OrganizationMembership::query()->where('organization_id', $organization->id)->where('user_id', $organizer->id)->exists()) {
                $report['owner_memberships_created']++;
                if (! $dryRun) {
                    OrganizationMembership::create(['organization_id'=>$organization->id,'user_id'=>$organizer->id,'role'=>'owner','status'=>$organizer->status?'active':'suspended','joined_at'=>$organizer->created_at ?? now()]);
                }
            }

            $unmappedEvents = $organizer->events()->whereNull('organization_id')->count();
            $report['events_mapped'] += $unmappedEvents;
            if (! $dryRun && $unmappedEvents) {
                $organizer->events()->whereNull('organization_id')->update(['organization_id' => $organization->id]);
            }

            $organizer->securityOfficers()->each(function (User $officer) use ($organization, &$report, $dryRun) {
                if (OrganizationMembership::query()->where('organization_id', $organization->id)->where('user_id', $officer->id)->exists()) {
                    return;
                }
                $report['security_memberships_created']++;
                if (! $dryRun) {
                    OrganizationMembership::create(['organization_id'=>$organization->id,'user_id'=>$officer->id,'role'=>'security_officer','status'=>$officer->status?'active':'suspended','joined_at'=>$officer->created_at ?? now()]);
                }
            });
        });

        return $report;
    }
}
