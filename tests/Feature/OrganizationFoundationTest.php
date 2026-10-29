<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\OrganizationMembership;
use App\Models\Role;
use App\Models\User;
use App\Notifications\OrganizationInvitationNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrganizationFoundationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_superadmin_creates_pending_organization_and_secure_owner_invitation(): void
    {
        Notification::fake();
        $admin = $this->user('superadmin');

        $this->actingAs($admin)->post(route('organizations.store'), [
            'name' => 'Northstar Events', 'owner_name' => 'Nora Owner',
            'owner_email' => 'nora-' . Str::uuid() . '@example.test',
            'country' => 'GB', 'timezone' => 'Europe/London',
        ])->assertRedirect(route('organizations.index'));

        $organization = Organization::where('name', 'Northstar Events')->firstOrFail();
        $this->assertSame('pending', $organization->status);
        $this->assertNull($organization->owner_id);
        $invitation = $organization->invitations()->firstOrFail();
        $this->assertSame(64, strlen($invitation->token_hash));
        $this->assertDatabaseMissing('organization_invitations', ['token_hash' => '']);
        Notification::assertSentOnDemand(OrganizationInvitationNotification::class);
    }

    public function test_owner_invitation_is_single_use_and_activates_membership(): void
    {
        $admin = $this->user('superadmin');
        $organization = Organization::create(['name'=>'Invitation Org','slug'=>'invitation-'.Str::lower(Str::random(8)),'status'=>'pending','timezone'=>'UTC']);
        $token = Str::random(64);
        OrganizationInvitation::create(['organization_id'=>$organization->id,'name'=>'Invited Owner','email'=>'invite-'.Str::uuid().'@example.test','role'=>'owner','token_hash'=>hash('sha256',$token),'invited_by'=>$admin->id,'expires_at'=>now()->addHour()]);

        $this->post(route('organization-invitations.accept',$token), ['name'=>'Invited Owner','password'=>'StrongPass123','password_confirmation'=>'StrongPass123'])->assertRedirect(route('dashboard'));

        $organization->refresh();
        $this->assertSame('active', $organization->status);
        $this->assertNotNull($organization->owner_id);
        $this->assertDatabaseHas('organization_memberships', ['organization_id'=>$organization->id,'user_id'=>$organization->owner_id,'role'=>'owner','status'=>'active']);
        $this->get(route('organization-invitations.show',$token))->assertGone();
    }

    public function test_members_can_only_list_and_open_events_from_their_organization(): void
    {
        $ownerA = $this->user('organizer');
        $ownerB = $this->user('organizer');
        $organizationA = $this->organization($ownerA, 'Alpha');
        $organizationB = $this->organization($ownerB, 'Beta');
        $eventA = $this->event($ownerA, $organizationA, 'Alpha Event');
        $eventB = $this->event($ownerB, $organizationB, 'Beta Event');

        $this->actingAs($ownerA)->get(route('organizations.index'))->assertOk()->assertSee('Alpha')->assertDontSee('Beta');
        $this->actingAs($ownerA)->get(route('events.index'))->assertOk()->assertSee('Alpha Event')->assertDontSee('Beta Event');
        $this->actingAs($ownerA)->get(route('events.show',$eventA))->assertOk();
        $this->actingAs($ownerA)->get(route('events.show',$eventB))->assertForbidden();
    }

    public function test_non_superadmin_cannot_create_organization(): void
    {
        $organizer = $this->user('organizer');
        $this->actingAs($organizer)->post(route('organizations.store'), ['name'=>'Blocked','owner_name'=>'Blocked','owner_email'=>'blocked@example.test','timezone'=>'UTC'])->assertForbidden();
    }

    public function test_owner_can_invite_member_but_cannot_invite_into_another_organization(): void
    {
        Notification::fake();
        $ownerA = $this->user('organizer');
        $ownerB = $this->user('organizer');
        $organizationA = $this->organization($ownerA, 'Member Alpha');
        $organizationB = $this->organization($ownerB, 'Member Beta');

        $payload = ['name'=>'Event Manager','email'=>'manager-'.Str::uuid().'@example.test','role'=>'event_manager'];
        $this->actingAs($ownerA)->post(route('organizations.members.store',$organizationA),$payload)->assertRedirect();
        $this->assertDatabaseHas('organization_invitations',['organization_id'=>$organizationA->id,'email'=>$payload['email'],'role'=>'event_manager']);
        $this->actingAs($ownerA)->post(route('organizations.members.store',$organizationB),$payload)->assertForbidden();
    }

    public function test_only_superadmin_can_change_lifecycle_and_suspension_removes_member_access(): void
    {
        $admin = $this->user('superadmin');
        $owner = $this->user('organizer');
        $organization = $this->organization($owner, 'Lifecycle');

        $this->actingAs($owner)->patch(route('organizations.status',$organization),['status'=>'suspended'])->assertForbidden();
        $this->actingAs($admin)->patch(route('organizations.status',$organization),['status'=>'suspended'])->assertRedirect();
        $this->assertSame('suspended',$organization->fresh()->status);
        $this->actingAs($owner)->get(route('organizations.show',$organization))->assertForbidden();
    }

    public function test_existing_user_invitation_preserves_existing_password(): void
    {
        $admin = $this->user('superadmin');
        $existing = $this->user('organizer');
        $organization = Organization::create(['name'=>'Existing User Org','slug'=>'existing-'.Str::lower(Str::random(8)),'status'=>'active','timezone'=>'UTC']);
        $token = Str::random(64);
        OrganizationInvitation::create(['organization_id'=>$organization->id,'name'=>$existing->name,'email'=>$existing->email,'role'=>'event_manager','token_hash'=>hash('sha256',$token),'invited_by'=>$admin->id,'expires_at'=>now()->addHour()]);

        $this->post(route('organization-invitations.accept',$token),['name'=>'Attempted Rename','password'=>'DifferentPass123','password_confirmation'=>'DifferentPass123'])->assertRedirect(route('dashboard'));

        $existing->refresh();
        $this->assertTrue(Hash::check('Password123',$existing->password));
        $this->assertDatabaseHas('organization_memberships',['organization_id'=>$organization->id,'user_id'=>$existing->id,'role'=>'event_manager','status'=>'active']);
    }

    public function test_suspended_organization_events_are_hidden_from_discovery_and_registration_but_remain_manageable_by_superadmin(): void
    {
        $admin = $this->user('superadmin');
        $owner = $this->user('organizer');
        $organization = $this->organization($owner, 'Suspended Discovery');
        $event = $this->event($owner, $organization, 'Hidden Suspended Event');
        $organization->update(['status'=>'suspended','suspended_at'=>now()]);

        $this->get('/')->assertOk()->assertDontSee('Hidden Suspended Event');
        $this->get(route('events.registration',$event->event_code))->assertNotFound();
        $this->actingAs($admin)->get(route('events.show',$event))->assertOk();
    }

    private function user(string $type): User
    {
        $role = Role::firstOrCreate(['name'=>$type]);
        return User::create(['name'=>ucfirst($type),'email'=>$type.'-'.Str::uuid().'@example.test','password'=>Hash::make('Password123'),'user_type'=>$type,'role_id'=>$role->id,'status'=>true]);
    }

    private function organization(User $owner, string $name): Organization
    {
        $organization = Organization::create(['owner_id'=>$owner->id,'name'=>$name,'slug'=>Str::lower($name).'-'.Str::lower(Str::random(8)),'status'=>'active','timezone'=>'UTC','activated_at'=>now()]);
        OrganizationMembership::create(['organization_id'=>$organization->id,'user_id'=>$owner->id,'role'=>'owner','status'=>'active','joined_at'=>now()]);
        return $organization;
    }

    private function event(User $owner, Organization $organization, string $name): Event
    {
        return Event::create(['user_id'=>$owner->id,'organization_id'=>$organization->id,'name'=>$name,'location'=>'Test venue','event_start_date'=>now()->addDay(),'event_end_date'=>now()->addDays(2),'booking_start_date'=>now()->subDay(),'booking_end_date'=>now()->addDay(),'max_attendees'=>20,'event_code'=>Str::upper(Str::random(10)),'checkin_password'=>'door-secret','price'=>0,'ticketing_type'=>'free','status'=>'published','visibility'=>'public','is_active'=>true]);
    }
}

