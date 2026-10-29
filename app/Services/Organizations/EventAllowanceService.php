<?php

namespace App\Services\Organizations;

use App\Models\Event;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventAllowanceService
{
    public function summary(Organization $organization): array
    {
        $allocated = (int) $organization->allowanceAdjustments()->sum('quantity');
        $consumed = (int) $organization->events()->count();

        return [
            'allocated' => $allocated,
            'consumed' => $consumed,
            'remaining' => $allocated - $consumed,
            'utilization' => $allocated > 0 ? min(100, round(($consumed / $allocated) * 100, 1)) : 0,
        ];
    }

    public function createEvent(Organization $organization, array $attributes, callable $afterCreate): Event
    {
        return DB::transaction(function () use ($organization, $attributes, $afterCreate) {
            $lockedOrganization = Organization::query()->lockForUpdate()->findOrFail($organization->id);
            $token = $attributes['creation_token'] ?? null;

            if ($token && ($existing = Event::where('creation_token', $token)->first())) {
                abort_unless($existing->organization_id === $lockedOrganization->id, 409);
                return $existing;
            }

            if ($lockedOrganization->status !== 'active') {
                throw ValidationException::withMessages(['organization_id' => 'Events can only be created for an active organization.']);
            }

            $summary = $this->summary($lockedOrganization);
            if ($summary['remaining'] < 1) {
                throw ValidationException::withMessages(['organization_id' => 'This organization has no remaining event allowance.']);
            }

            $event = Event::create($attributes);
            $afterCreate($event);

            return $event;
        }, 3);
    }
}
