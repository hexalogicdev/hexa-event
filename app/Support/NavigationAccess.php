<?php

namespace App\Support;

use App\Models\User;
use App\Models\Organization;

final class NavigationAccess
{
    public static function allows(User $user, object $item): bool
    {
        if (($item->ability ?? null) === 'organizations.viewAny') {
            return $user->can('viewAny', Organization::class);
        }
        if (! isset($item->roles)) {
            return true;
        }

        $role = $user->role?->name ?? $user->user_type;

        return in_array($role, (array) $item->roles, true);
    }
}

