<?php

namespace Tests\Feature;

use App\Models\EventAllowanceRequest;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Role;
use App\Models\User;
use App\Notifications\EventAllowanceRequestReviewedNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class EventAllowanceRequestTest extends TestCase
{
    use DatabaseTransactions;

    public function test_owner_can_request_and_cancel_allowance_but_cannot_request_for_another_organization(): void
    {
        $ownerA = $this->user('organizer');
        $ownerB = $this->user('organizer');
        $organizationA = $this->organization($ownerA, 'Request Alpha');
        $organizationB = $this->organization($ownerB, 'Request Beta');
        $payload = ['requested_quantity' => 4, 'justification' => 'Four additional client events are contracted.'];

        $this->actingAs($ownerA)->post(route('organizations.event-allowance-requests.store', $organizationB), $payload)->assertForbidden();
        $this->actingAs($ownerA)->post(route('organizations.event-allowance-requests.store', $organizationA), $payload)->assertRedirect();

        $request = EventAllowanceRequest::where('organization_id', $organizationA->id)->firstOrFail();
        $this->assertSame('pending', $request->status);
        $this->actingAs($ownerA)->patch(route('event-allowance-requests.cancel', $request))->assertRedirect();
        $this->assertSame('cancelled', $request->fresh()->status);
    }

    public function test_approval_creates_one_immutable_adjustment_and_duplicate_processing_is_rejected(): void
    {
        Notification::fake();
        $admin = $this->user('superadmin');
        $owner = $this->user('organizer');
        $organization = $this->organization($owner, 'Approval Org');
        $request = $organization->allowanceRequests()->create(['requested_by' => $owner->id, 'requested_quantity' => 5, 'justification' => 'Capacity required for the approved annual programme.', 'status' => 'pending']);

        $this->actingAs($admin)->patch(route('event-allowance-requests.review', $request), ['action' => 'approve', 'approved_quantity' => 3, 'internal_notes' => 'Approved against agreement.'])->assertRedirect();

        $request->refresh();
        $this->assertSame('approved', $request->status);
        $this->assertSame(3, $request->approved_quantity);
        $this->assertDatabaseHas('organization_event_allowance_adjustments', ['organization_id' => $organization->id, 'quantity' => 3, 'agreement_reference' => 'ALLOWANCE-REQUEST-'.$request->id]);
        Notification::assertSentTo($owner, EventAllowanceRequestReviewedNotification::class);

        $this->actingAs($admin)->patch(route('event-allowance-requests.review', $request), ['action' => 'approve', 'approved_quantity' => 3])->assertSessionHasErrors('action');
        $this->assertSame(1, $organization->allowanceAdjustments()->where('agreement_reference', 'ALLOWANCE-REQUEST-'.$request->id)->count());
    }

    public function test_non_superadmin_cannot_review_and_rejection_does_not_allocate(): void
    {
        Notification::fake();
        $admin = $this->user('superadmin');
        $owner = $this->user('organizer');
        $organization = $this->organization($owner, 'Rejected Org');
        $request = $organization->allowanceRequests()->create(['requested_by' => $owner->id, 'requested_quantity' => 2, 'justification' => 'Additional events requested for a new campaign.', 'status' => 'pending']);

        $this->actingAs($owner)->patch(route('event-allowance-requests.review', $request), ['action' => 'reject', 'internal_notes' => 'No agreement.'])->assertForbidden();
        $this->actingAs($admin)->patch(route('event-allowance-requests.review', $request), ['action' => 'reject', 'internal_notes' => 'No agreement reference was supplied.'])->assertRedirect();

        $this->assertSame('rejected', $request->fresh()->status);
        $this->assertSame(0, $organization->allowanceAdjustments()->count());
    }

    private function user(string $type): User
    {
        $role = Role::firstOrCreate(['name' => $type]);
        return User::create(['name' => ucfirst($type), 'email' => $type.'-'.Str::uuid().'@example.test', 'password' => Hash::make('Password123'), 'user_type' => $type, 'role_id' => $role->id, 'status' => true]);
    }

    private function organization(User $owner, string $name): Organization
    {
        $organization = Organization::create(['owner_id' => $owner->id, 'name' => $name, 'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)), 'status' => 'active', 'timezone' => 'UTC', 'activated_at' => now()]);
        OrganizationMembership::create(['organization_id' => $organization->id, 'user_id' => $owner->id, 'role' => 'owner', 'status' => 'active', 'joined_at' => now()]);
        return $organization;
    }
}
