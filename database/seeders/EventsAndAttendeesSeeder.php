<?php

namespace Database\Seeders;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EventsAndAttendeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizerRoleId = Role::where('name', 'organizer')->value('id') ?? 2;

        $organizer = User::firstOrCreate(
            ['email' => 'organizer@example.com'],
            [
                'name' => 'Demo Organizer',
                'password' => Hash::make('password123'),
                'user_type' => 'organizer',
                'role_id' => $organizerRoleId,
                'country' => 'Pakistan',
                'gender' => 'Female',
                'phone' => '+923001234567',
                'address' => 'Demo Street 1, Lahore',
                'status' => true,
            ]
        );

        $eventNames = [
            'Innovation Summit',
            'Creative Tech Meetup',
            'Community Leadership Forum',
            'Startup Growth Lab',
            'Design Systems Day',
            'Digital Marketing Expo',
            'Product Launch Week',
            'AI for Teams',
            'Culture & Collaboration',
            'Future of Work',
        ];

        $descriptions = [
            'A high-energy event focused on practical learning and meaningful networking.',
            'Bring your team together for a full day of insight, demos, and product discovery.',
            'An inviting gathering for professionals who want to exchange bold ideas and new strategies.',
            'A hands-on event tailored for founders, operators, and product leaders.',
            'Explore emerging tools, trends, and implementation stories in a collaborative setting.',
        ];

        $locations = [
            'Lahore Expo Center',
            'Karachi Convention Hall',
            'Islamabad Business District',
            'Peshawar Innovation Hub',
            'Faisalabad Tech Campus',
        ];

        for ($eventIndex = 1; $eventIndex <= 50; $eventIndex++) {
            $startDate = now()->addDays(20 + $eventIndex)->setTime(18, 0, 0);
            $endDate = (clone $startDate)->addHours(4);
            $bookingStartDate = (clone $startDate)->subDays(15);
            $bookingEndDate = (clone $startDate)->subDays(1);

            $event = Event::create([
                'user_id' => $organizer->id,
                'name' => sprintf('Event %02d - %s', $eventIndex, $eventNames[$eventIndex % count($eventNames)]),
                'description' => $descriptions[$eventIndex % count($descriptions)],
                'location' => $locations[$eventIndex % count($locations)],
                'event_start_date' => $startDate,
                'event_end_date' => $endDate,
                'booking_start_date' => $bookingStartDate->toDateString(),
                'booking_end_date' => $bookingEndDate->toDateString(),
                'max_attendees' => 200,
                'event_code' => Str::upper(Str::random(8)) . $eventIndex,
                'checkin_password' => 'checkin' . $eventIndex,
                'status' => 'published',
                'visibility' => 'public',
                'price' => number_format(random_int(0, 250) + (random_int(0, 9) / 10), 2, '.', ''),
                'is_active' => true,
                'registration_fields' => [],
            ]);

            for ($attendeeIndex = 1; $attendeeIndex <= 100; $attendeeIndex++) {
                $attendee = Attendee::create([
                    'event_id' => $event->id,
                    'name' => 'Attendee ' . $eventIndex . '-' . $attendeeIndex,
                    'email' => sprintf('attendee%02d-%03d@example.com', $eventIndex, $attendeeIndex),
                    'phone' => '+92' . str_pad((string) random_int(300000000, 399999999), 9, '0', STR_PAD_LEFT),
                    'status' => 'confirmed',
                    'amount' => $event->price,
                    'qr_id' => Str::random(10),
                    'checked_in' => false,
                    'checked_in_at' => null,
                    'checked_in_by' => null,
                    'custom_answers' => [],
                ]);

                if ($attendeeIndex % 5 === 0) {
                    $attendee->update([
                        'checked_in' => true,
                        'checked_in_at' => now()->subHours(random_int(1, 8)),
                        'checked_in_by' => $organizer->id,
                    ]);
                }
            }

            $checkedInIds = Attendee::where('event_id', $event->id)->where('checked_in', true)->pluck('id');
            if ($checkedInIds->isNotEmpty()) {
                Attendee::whereIn('id', $checkedInIds)->update([
                    'checked_in' => true,
                    'checked_in_at' => now()->subHours(random_int(1, 8)),
                    'checked_in_by' => $organizer->id,
                ]);
            }
        }
    }
}
