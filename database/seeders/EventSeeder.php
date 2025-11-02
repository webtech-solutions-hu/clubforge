<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $organizers = User::whereHas('roles', function ($query) {
            $query->whereIn('slug', ['administrator', 'game-master', 'owner']);
        })->pluck('id')->toArray();

        if (empty($organizers)) {
            $organizers = [1]; // Fallback to first user
        }

        $events = [
            [
                'name' => 'Weekly D&D Campaign - Session 5',
                'type' => 'rpg',
                'description' => 'Continue our epic fantasy adventure! New players welcome.',
                'location' => 'Gaming Room A',
                'start_date' => now()->addDays(3)->setTime(18, 0),
                'end_date' => now()->addDays(3)->setTime(22, 0),
                'max_participants' => 6,
                'status' => 'upcoming',
                'organizer_id' => $organizers[array_rand($organizers)],
            ],
            [
                'name' => 'Catan Tournament Finals',
                'type' => 'tournament',
                'description' => 'Championship round for our monthly Catan tournament. Winner takes all!',
                'location' => 'Main Hall',
                'start_date' => now()->addDays(7)->setTime(14, 0),
                'end_date' => now()->addDays(7)->setTime(18, 0),
                'max_participants' => 12,
                'status' => 'upcoming',
                'organizer_id' => $organizers[array_rand($organizers)],
            ],
            [
                'name' => 'Board Game Night - Strategy Games',
                'type' => 'board-game',
                'description' => 'Bring your favorite strategy games or try something new from our library.',
                'location' => 'Club Lounge',
                'start_date' => now()->addDays(2)->setTime(19, 0),
                'end_date' => now()->addDays(2)->setTime(23, 0),
                'max_participants' => null,
                'status' => 'upcoming',
                'organizer_id' => $organizers[array_rand($organizers)],
            ],
            [
                'name' => 'Warhammer 40K Painting Workshop',
                'type' => 'workshop',
                'description' => 'Learn advanced painting techniques for miniatures. Materials provided.',
                'location' => 'Art Room',
                'start_date' => now()->addDays(5)->setTime(15, 0),
                'end_date' => now()->addDays(5)->setTime(19, 0),
                'max_participants' => 8,
                'status' => 'upcoming',
                'organizer_id' => $organizers[array_rand($organizers)],
            ],
            [
                'name' => 'Monthly Social Mixer',
                'type' => 'social',
                'description' => 'Meet fellow gamers, enjoy snacks, and play casual party games.',
                'location' => 'Community Center',
                'start_date' => now()->addDays(10)->setTime(17, 0),
                'end_date' => now()->addDays(10)->setTime(21, 0),
                'max_participants' => null,
                'status' => 'upcoming',
                'organizer_id' => $organizers[array_rand($organizers)],
            ],
            [
                'name' => 'Magic: The Gathering Draft',
                'type' => 'tournament',
                'description' => 'Booster draft tournament. Entry fee includes 3 booster packs.',
                'location' => 'Gaming Room B',
                'start_date' => now()->addDays(4)->setTime(18, 30),
                'end_date' => now()->addDays(4)->setTime(22, 30),
                'max_participants' => 8,
                'status' => 'upcoming',
                'organizer_id' => $organizers[array_rand($organizers)],
            ],
            [
                'name' => 'Pathfinder One-Shot Adventure',
                'type' => 'rpg',
                'description' => 'Self-contained adventure perfect for beginners. Pre-gen characters available.',
                'location' => 'Gaming Room C',
                'start_date' => now()->addDays(6)->setTime(13, 0),
                'end_date' => now()->addDays(6)->setTime(17, 0),
                'max_participants' => 5,
                'status' => 'upcoming',
                'organizer_id' => $organizers[array_rand($organizers)],
            ],
            [
                'name' => 'Pandemic Legacy Campaign',
                'type' => 'board-game',
                'description' => 'Week 1 of our Pandemic Legacy season. Committed players only.',
                'location' => 'Private Room 1',
                'start_date' => now()->subDays(2)->setTime(19, 0),
                'end_date' => now()->subDays(2)->setTime(22, 0),
                'max_participants' => 4,
                'status' => 'completed',
                'organizer_id' => $organizers[array_rand($organizers)],
            ],
            [
                'name' => 'Chess Tournament Qualifier',
                'type' => 'tournament',
                'description' => 'Qualify for the regional chess championship.',
                'location' => 'Main Hall',
                'start_date' => now()->subDays(7)->setTime(10, 0),
                'end_date' => now()->subDays(7)->setTime(16, 0),
                'max_participants' => 16,
                'status' => 'completed',
                'organizer_id' => $organizers[array_rand($organizers)],
            ],
            [
                'name' => 'Intro to RPGs Workshop',
                'type' => 'workshop',
                'description' => 'Never played an RPG? Learn the basics and try your first game!',
                'location' => 'Conference Room',
                'start_date' => now()->subDays(14)->setTime(14, 0),
                'end_date' => now()->subDays(14)->setTime(18, 0),
                'max_participants' => 10,
                'status' => 'completed',
                'organizer_id' => $organizers[array_rand($organizers)],
            ],
            [
                'name' => 'Ticket to Ride Championship',
                'type' => 'tournament',
                'description' => 'Annual Ticket to Ride tournament with prizes for top 3 players.',
                'location' => 'Main Hall',
                'start_date' => now()->subDays(21)->setTime(12, 0),
                'end_date' => now()->subDays(21)->setTime(17, 0),
                'max_participants' => 20,
                'status' => 'completed',
                'organizer_id' => $organizers[array_rand($organizers)],
            ],
            [
                'name' => 'Summer Gaming Marathon',
                'type' => 'social',
                'description' => '24-hour gaming event with food, prizes, and tons of games!',
                'location' => 'Entire Venue',
                'start_date' => now()->addDays(30)->setTime(12, 0),
                'end_date' => now()->addDays(31)->setTime(12, 0),
                'max_participants' => null,
                'status' => 'upcoming',
                'organizer_id' => $organizers[array_rand($organizers)],
            ],
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }
    }
}
