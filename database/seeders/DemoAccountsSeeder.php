<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // if (app()->isProduction()) {
        //     throw new RuntimeException('Demo accounts cannot be seeded in production.');
        // }

        $roles = collect(['superadmin', 'organizer', 'security_officer'])
            ->mapWithKeys(fn (string $name) => [$name => Role::firstOrCreate(['name' => $name])]);

        $superadmin = User::withTrashed()->updateOrCreate(
            ['email' => 'superadmin@hexa.com'],
            [
                'name' => 'Demo Super Admin',
                'password' => Hash::make('Demo@12345'),
                'user_type' => 'superadmin',
                'role_id' => $roles['superadmin']->id,
                'organizer_id' => null,
                'timezone' => 'Europe/London',
                'status' => true,
            ]
        );
        if ($superadmin->trashed()) {
            $superadmin->restore();
        }

        $organizer = User::withTrashed()->updateOrCreate(
            ['email' => 'organizer@hexa.com'],
            [
                'name' => 'Demo Organizer',
                'password' => Hash::make('Demo@12345'),
                'user_type' => 'organizer',
                'role_id' => $roles['organizer']->id,
                'organizer_id' => null,
                'timezone' => 'Europe/London',
                'status' => true,
            ]
        );
        if ($organizer->trashed()) {
            $organizer->restore();
        }

        $officer = User::withTrashed()->updateOrCreate(
            ['email' => 'security@hexa.com'],
            [
                'name' => 'Demo Security Officer',
                'password' => Hash::make('Demo@12345'),
                'user_type' => 'security_officer',
                'role_id' => $roles['security_officer']->id,
                'organizer_id' => $organizer->id,
                'timezone' => 'Europe/London',
                'status' => true,
            ]
        );
        if ($officer->trashed()) {
            $officer->restore();
        }

        $organizer->events()->each(function ($event) use ($officer) {
            $event->securityOfficers()->syncWithoutDetaching([$officer->id]);
        });
    }
}
