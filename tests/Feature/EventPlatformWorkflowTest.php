<?php

namespace Tests\Feature;

use App\Models\Attendee;
use App\Models\CheckInAttempt;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class EventPlatformWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_public_account_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [])->assertNotFound();
        $this->get('/check-in')->assertRedirect('/login');
        $this->get('/logsDownload')->assertNotFound();
        $this->get('/attendee')->assertMethodNotAllowed();
        $this->get('/thankYou/1')->assertForbidden();
    }

    public function test_authorized_qr_scan_marks_attendee_checked_in(): void
    {
        $role = Role::firstOrCreate(['name' => 'organizer']);
        $organizer = User::create([
            'name' => 'Workflow Organizer',
            'email' => 'workflow-' . Str::uuid() . '@example.test',
            'password' => Hash::make('Password!1'),
            'user_type' => 'organizer',
            'role_id' => $role->id,
            'status' => true,
        ]);
        $event = Event::create([
            'user_id' => $organizer->id,
            'name' => 'QR Workflow Test',
            'location' => 'Test venue',
            'event_start_date' => now()->addDay(),
            'event_end_date' => now()->addDays(2),
            'booking_start_date' => now()->subDay(),
            'booking_end_date' => now()->addDay(),
            'max_attendees' => 20,
            'event_code' => Str::upper(Str::random(10)),
            'checkin_password' => 'door-secret',
            'price' => 0,
            'ticketing_type' => 'free',
            'status' => 'published',
            'visibility' => 'public',
            'is_active' => true,
        ]);
        $attendee = Attendee::create([
            'event_id' => $event->id,
            'name' => 'Test Guest',
            'email' => 'guest-' . Str::uuid() . '@example.test',
            'qr_id' => 'QRTEST1234',
            'status' => 'confirmed',
        ]);

        $this->actingAs($organizer)->post('/check-in', [
            'qr_id' => route('attendee.qr', $attendee->qr_id),
        ])->assertOk()->assertViewIs('checkin.success');

        $this->assertTrue($attendee->fresh()->checked_in);
        $this->assertNotNull($attendee->fresh()->checked_in_at);
    }

    public function test_security_officer_is_limited_to_scanner_workspace(): void
    {
        $organizerRole = Role::firstOrCreate(['name' => 'organizer']);
        $securityRole = Role::firstOrCreate(['name' => 'security_officer']);
        $organizer = User::create([
            'name' => 'Owning Organizer', 'email' => 'owner-' . Str::uuid() . '@example.test',
            'password' => Hash::make('Password!1'), 'user_type' => 'organizer', 'role_id' => $organizerRole->id,
            'status' => true,
        ]);
        $officer = User::create([
            'name' => 'Door Officer', 'email' => 'officer-' . Str::uuid() . '@example.test',
            'password' => Hash::make('Password!1'), 'user_type' => 'security_officer',
            'role_id' => $securityRole->id, 'organizer_id' => $organizer->id,
            'status' => true,
        ]);

        $this->actingAs($officer)->get('/dashboard')->assertRedirect(route('attendee.qr.scan'));
        $this->actingAs($officer)->get('/events')->assertForbidden();
        $this->actingAs($officer)->get('/profile')->assertForbidden();
        $this->actingAs($officer)->get('/check-in')->assertOk()->assertDontSee('id="layout-menu"', false);

        $this->actingAs($organizer)->patch(route('security-officers.status', $officer), ['status' => 0])
            ->assertRedirect();
        $officer->refresh();
        $this->assertFalse($officer->status);
        $this->actingAs($officer)->get('/check-in')->assertRedirect(route('login'));
    }

    public function test_management_layout_renders_sidebar_for_organizer(): void
    {
        $role = Role::firstOrCreate(['name' => 'organizer']);
        $organizer = User::create([
            'name' => 'Sidebar Organizer',
            'email' => 'sidebar-' . Str::uuid() . '@example.test',
            'password' => Hash::make('Password!1'),
            'user_type' => 'organizer',
            'role_id' => $role->id,
            'status' => true,
        ]);

        $this->actingAs($organizer)->get('/dashboard')
            ->assertOk()
            ->assertSee('id="layout-menu"', false)
            ->assertSee('Overview')
            ->assertSee('Events')
            ->assertSee('QR check-in');
    }

    public function test_suspended_authenticated_account_is_logged_out(): void
    {
        $role = Role::firstOrCreate(['name' => 'organizer']);
        $organizer = User::create([
            'name' => 'Suspended Organizer',
            'email' => 'suspended-' . Str::uuid() . '@example.test',
            'password' => Hash::make('Password!1'),
            'user_type' => 'organizer',
            'role_id' => $role->id,
            'status' => false,
        ]);

        $this->actingAs($organizer)->get('/dashboard')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_security_officer_can_only_check_in_assigned_events_and_attempts_are_audited(): void
    {
        $organizerRole = Role::firstOrCreate(['name' => 'organizer']);
        $securityRole = Role::firstOrCreate(['name' => 'security_officer']);
        $organizer = User::create([
            'name' => 'Assignment Owner', 'email' => 'assignment-owner-' . Str::uuid() . '@example.test',
            'password' => Hash::make('Password!1'), 'user_type' => 'organizer',
            'role_id' => $organizerRole->id, 'status' => true,
        ]);
        $officer = User::create([
            'name' => 'Assigned Officer', 'email' => 'assigned-officer-' . Str::uuid() . '@example.test',
            'password' => Hash::make('Password!1'), 'user_type' => 'security_officer',
            'role_id' => $securityRole->id, 'organizer_id' => $organizer->id, 'status' => true,
        ]);
        $event = Event::create([
            'user_id' => $organizer->id, 'name' => 'Assignment Event', 'location' => 'Gate A',
            'event_start_date' => now()->addDay(), 'event_end_date' => now()->addDays(2),
            'booking_start_date' => now()->subDay(), 'booking_end_date' => now()->addDay(),
            'max_attendees' => 20, 'event_code' => Str::upper(Str::random(10)),
            'checkin_password' => 'unused', 'price' => 0, 'ticketing_type' => 'free',
            'status' => 'published', 'visibility' => 'public', 'is_active' => true,
        ]);
        $attendee = Attendee::create([
            'event_id' => $event->id, 'name' => 'Assigned Guest',
            'email' => 'assigned-guest-' . Str::uuid() . '@example.test',
            'qr_id' => Str::upper(Str::random(32)), 'status' => 'confirmed',
        ]);

        $this->actingAs($officer)->post('/check-in', ['qr_id' => $attendee->qr_id])
            ->assertSessionHasErrors('qr_id');
        $this->assertFalse($attendee->fresh()->checked_in);
        $this->assertDatabaseHas('check_in_attempts', [
            'attendee_id' => $attendee->id, 'user_id' => $officer->id, 'outcome' => 'wrong_event',
        ]);

        $event->securityOfficers()->attach($officer->id);
        $this->actingAs($officer)->post('/check-in', ['qr_id' => $attendee->qr_id])
            ->assertOk()->assertViewIs('checkin.success');
        $this->assertTrue($attendee->fresh()->checked_in);
        $this->assertDatabaseHas('check_in_attempts', [
            'attendee_id' => $attendee->id, 'user_id' => $officer->id, 'outcome' => 'accepted',
        ]);
    }

    public function test_free_registration_creates_one_idempotent_order_item_and_ticket(): void
    {
        $role = Role::firstOrCreate(['name' => 'organizer']);
        $organizer = User::create([
            'name' => 'Checkout Organizer', 'email' => 'checkout-owner-' . Str::uuid() . '@example.test',
            'password' => Hash::make('Password!1'), 'user_type' => 'organizer',
            'role_id' => $role->id, 'status' => true,
        ]);
        $event = Event::create([
            'user_id' => $organizer->id, 'name' => 'Free Checkout Event', 'location' => 'Main Hall',
            'event_start_date' => now()->addDays(3), 'event_end_date' => now()->addDays(4),
            'booking_start_date' => now()->subDay(), 'booking_end_date' => now()->addDay(),
            'max_attendees' => 20, 'event_code' => Str::upper(Str::random(10)),
            'checkin_password' => 'unused', 'price' => 0, 'ticketing_type' => 'free',
            'status' => 'published', 'visibility' => 'public', 'is_active' => true,
        ]);
        $key = (string) Str::uuid();
        $payload = [
            'event_id' => $event->id, 'idempotency_key' => $key,
            'name' => 'Free Guest', 'email' => 'free-guest-' . Str::uuid() . '@example.test',
            'phone' => '+44 20 1234 5678',
        ];

        $this->post(route('attendee.store'), $payload)->assertRedirect();
        $this->post(route('attendee.store'), $payload)->assertRedirect();

        $this->assertSame(1, $event->orders()->where('idempotency_key', $key)->count());
        $order = $event->orders()->where('idempotency_key', $key)->firstOrFail();
        $this->assertSame('completed', $order->status);
        $this->assertSame('not_required', $order->payment_status);
        $this->assertSame(1, $order->items()->count());
        $this->assertSame(1, $order->attendees()->count());
        $this->assertSame(1, $order->tickets()->count());
        $this->assertSame('valid', $order->tickets()->first()->status);
    }
}
