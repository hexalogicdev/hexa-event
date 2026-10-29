<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Organization;
use App\Models\OrganizationEventAllowanceAdjustment;
use App\Models\OrganizationMembership;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class EventAllowanceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_only_superadmin_can_record_an_immutable_allowance_adjustment(): void
    {
        $owner = $this->user('organizer');
        $admin = $this->user('superadmin');
        $organization = $this->organization($owner);
        $payload = ['quantity' => 5, 'reason' => 'Annual agreement', 'effective_date' => now()->toDateString()];

        $this->actingAs($owner)->post(route('organizations.event-allowance-adjustments.store', $organization), $payload)->assertForbidden();
        $this->actingAs($admin)->post(route('organizations.event-allowance-adjustments.store', $organization), $payload)->assertRedirect();

        $adjustment = OrganizationEventAllowanceAdjustment::where('organization_id', $organization->id)->firstOrFail();
        $this->assertSame(5, $adjustment->quantity);
        $this->assertSame($admin->id, $adjustment->adjusted_by);
        $this->expectException(\LogicException::class);
        $adjustment->update(['quantity' => 6]);
    }

    public function test_event_creation_requires_remaining_allowance(): void
    {
        $owner = $this->user('organizer');
        $organization = $this->organization($owner);

        $this->actingAs($owner)->post(route('events.store'), $this->eventPayload())->assertSessionHasErrors('organization_id');
        $this->assertSame(0, Event::where('organization_id', $organization->id)->count());
    }

    public function test_successful_event_creation_consumes_allowance_and_retry_token_is_idempotent(): void
    {
        $owner = $this->user('organizer');
        $organization = $this->organization($owner);
        $organization->allowanceAdjustments()->create([
            'adjusted_by' => $this->user('superadmin')->id,
            'quantity' => 1,
            'reason' => 'Initial allocation',
            'effective_date' => now()->toDateString(),
        ]);
        $payload = $this->eventPayload();

        $this->actingAs($owner)->post(route('events.store'), $payload)->assertRedirect(route('events.index'));
        $this->actingAs($owner)->post(route('events.store'), $payload)->assertRedirect(route('events.index'));

        $this->assertSame(1, Event::where('organization_id', $organization->id)->count());
        $this->assertDatabaseHas('events', ['organization_id' => $organization->id, 'creation_token' => $payload['creation_token']]);

        $secondPayload = $this->eventPayload();
        $this->actingAs($owner)->post(route('events.store'), $secondPayload)->assertSessionHasErrors('organization_id');
        $this->assertSame(1, Event::where('organization_id', $organization->id)->count());
    }

    private function eventPayload(): array
    {
        return [
            'name' => 'Allowance Event',
            'location' => 'London',
            'event_start_date' => now()->addDays(5)->toDateString(),
            'event_end_date' => now()->addDays(6)->toDateString(),
            'booking_start_date' => now()->toDateString(),
            'booking_end_date' => now()->addDays(4)->toDateString(),
            'max_attendees' => 50,
            'checkin_password' => 'door-secret',
            'ticketing_type' => 'free',
            'status' => 'draft',
            'visibility' => 'private',
            'is_active' => '1',
            'creation_token' => (string) Str::uuid(),
        ];
    }

    private function user(string $type): User
    {
        $role = Role::firstOrCreate(['name' => $type]);
        return User::create(['name' => ucfirst($type), 'email' => $type.'-'.Str::uuid().'@example.test', 'password' => Hash::make('Password123'), 'user_type' => $type, 'role_id' => $role->id, 'status' => true]);
    }

    private function organization(User $owner): Organization
    {
        $organization = Organization::create(['owner_id' => $owner->id, 'name' => 'Allowance Org', 'slug' => 'allowance-'.Str::lower(Str::random(8)), 'status' => 'active', 'timezone' => 'UTC', 'activated_at' => now()]);
        OrganizationMembership::create(['organization_id' => $organization->id, 'user_id' => $owner->id, 'role' => 'owner', 'status' => 'active', 'joined_at' => now()]);
        return $organization;
    }
}
