<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Event;
use App\Models\Like;
use App\Models\Post;
use App\Models\Result;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommunityDataSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $events = Event::all();

        if ($users->count() < 10) {
            $this->command->warn('Not enough users found. Please run UserSeeder first.');
            return;
        }

        // Get user IDs dynamically
        $userIds = $users->pluck('id')->toArray();

        // Create Posts
        $posts = [
            [
                'user_id' => $userIds[0],
                'title' => 'Welcome to ClubForge!',
                'content' => 'Hello everyone! Welcome to our new club management system. Feel free to explore and start connecting with fellow gamers!',
                'is_pinned' => true,
            ],
            [
                'user_id' => $userIds[1],
                'title' => 'Looking for D&D Players',
                'content' => 'Starting a new campaign next week. We need 2 more players. Experience not required, just bring enthusiasm!',
                'is_pinned' => false,
            ],
            [
                'user_id' => $userIds[2],
                'title' => null,
                'content' => 'Had an amazing time at last night\'s board game session! Thanks to everyone who came out.',
                'is_pinned' => false,
            ],
            [
                'user_id' => $userIds[3],
                'title' => 'Tournament Results Posted',
                'content' => 'Congratulations to all participants in last weekend\'s tournament! Results are now available.',
                'is_pinned' => true,
            ],
            [
                'user_id' => $userIds[4],
                'title' => null,
                'content' => 'Does anyone have recommendations for good 2-player cooperative games?',
                'is_pinned' => false,
            ],
            [
                'user_id' => $userIds[5],
                'title' => 'Game Library Update',
                'content' => 'We just added 15 new titles to our game library including Wingspan, Everdell, and Scythe. Come check them out!',
                'is_pinned' => false,
            ],
            [
                'user_id' => $userIds[6],
                'title' => null,
                'content' => 'Looking forward to the painting workshop this weekend! Who else is attending?',
                'is_pinned' => false,
            ],
            [
                'user_id' => $userIds[7],
                'title' => 'Rules Question - Catan',
                'content' => 'Quick question about longest road in Catan. If someone breaks your road, do you immediately lose the longest road card or only when someone else gets a longer road?',
                'is_pinned' => false,
            ],
            [
                'user_id' => $userIds[8],
                'title' => null,
                'content' => 'The social mixer was fantastic! Met so many amazing people. Can\'t wait for next month!',
                'is_pinned' => false,
            ],
            [
                'user_id' => $userIds[9],
                'title' => 'New Game Design Playtest',
                'content' => 'I\'m designing a new card game and need playtesters. If you\'re interested in trying out prototypes and providing feedback, let me know!',
                'is_pinned' => false,
            ],
            [
                'user_id' => $userIds[4],
                'title' => null,
                'content' => 'Just finished an epic 4-hour game of Twilight Imperium. My brain hurts but it was worth it!',
                'is_pinned' => false,
            ],
            [
                'user_id' => $userIds[6],
                'title' => 'Painting Tips',
                'content' => 'For anyone interested in miniature painting, I highly recommend starting with Citadel paints and getting a wet palette. Game changers!',
                'is_pinned' => false,
            ],
        ];

        $createdPosts = [];
        foreach ($posts as $postData) {
            $createdPosts[] = Post::create($postData);
        }

        // Create Comments (using actual post IDs from created posts)
        if (count($createdPosts) >= 10) {
            $comments = [
                ['post_id' => $createdPosts[0]->id, 'user_id' => $userIds[1], 'content' => 'This looks amazing! Excited to be part of this community.'],
                ['post_id' => $createdPosts[0]->id, 'user_id' => $userIds[4], 'content' => 'Great to see everyone here!'],
                ['post_id' => $createdPosts[0]->id, 'user_id' => $userIds[7], 'content' => 'Love the new system. Much easier to navigate!'],
                ['post_id' => $createdPosts[1]->id, 'user_id' => $userIds[5], 'content' => 'I\'d love to join! I\'ve never played but always wanted to learn.'],
                ['post_id' => $createdPosts[1]->id, 'user_id' => $userIds[8], 'content' => 'Count me in! I have some experience with 5e.'],
                ['post_id' => $createdPosts[2]->id, 'user_id' => $userIds[0], 'content' => 'Glad you had fun! Same time next week?'],
                ['post_id' => $createdPosts[4]->id, 'user_id' => $userIds[6], 'content' => 'Pandemic Legacy is fantastic for 2 players!'],
                ['post_id' => $createdPosts[4]->id, 'user_id' => $userIds[9], 'content' => 'I\'d also recommend 7 Wonders Duel and Patchwork.'],
                ['post_id' => $createdPosts[5]->id, 'user_id' => $userIds[1], 'content' => 'Wingspan! I\'ve been wanting to try that one.'],
                ['post_id' => $createdPosts[5]->id, 'user_id' => $userIds[3], 'content' => 'Scythe is incredible. Highly recommend!'],
                ['post_id' => $createdPosts[7]->id, 'user_id' => $userIds[3], 'content' => 'You lose it immediately when your road is broken, even if no one else has a longer road yet.'],
                ['post_id' => $createdPosts[7]->id, 'user_id' => $userIds[9], 'content' => 'Yeah, it\'s an instant loss. Make sure to build defensively!'],
                ['post_id' => $createdPosts[9]->id, 'user_id' => $userIds[0], 'content' => 'I\'d be happy to help playtest! Send me the details.'],
                ['post_id' => $createdPosts[9]->id, 'user_id' => $userIds[4], 'content' => 'Sounds fun! What\'s the game about?'],
                ['post_id' => $createdPosts[11]->id, 'user_id' => $userIds[7], 'content' => 'Wet palettes are a must! Also invest in good brushes.'],
            ];
        } else {
            $comments = [];
        }

        foreach ($comments as $commentData) {
            Comment::create($commentData);
        }

        // Create Likes
        foreach ($createdPosts as $post) {
            // Random number of likes per post (3-8 likes)
            $likeCount = rand(3, 8);
            $likedUsers = $users->random($likeCount);

            foreach ($likedUsers as $user) {
                Like::create([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                ]);
            }
        }

        // Create Event Participants
        foreach ($events as $event) {
            // Determine participant count based on event status and max_participants
            if ($event->status === 'completed') {
                $maxParticipants = $event->max_participants ?? 10;
                $participantCount = rand(max(3, $maxParticipants - 2), $maxParticipants);
            } else {
                $maxParticipants = $event->max_participants ?? 15;
                $participantCount = rand(2, min($users->count(), $maxParticipants));
            }

            $participants = $users->random($participantCount);

            foreach ($participants as $index => $participant) {
                // Determine role (first participant often GM, rest are players or spectators)
                if ($index === 0 && in_array($event->type, ['rpg', 'workshop'])) {
                    $role = 'gm';
                } else {
                    $role = rand(1, 10) > 8 ? 'spectator' : 'player';
                }

                // Determine status based on event status
                if ($event->status === 'completed') {
                    $status = 'completed';
                } elseif ($event->status === 'upcoming') {
                    $statuses = ['confirmed', 'confirmed', 'confirmed', 'pending'];
                    $status = $statuses[array_rand($statuses)];
                } else {
                    $status = 'confirmed';
                }

                $event->participants()->attach($participant->id, [
                    'role' => $role,
                    'status' => $status,
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create Results for completed events
        $completedEvents = Event::where('status', 'completed')->get();

        foreach ($completedEvents as $event) {
            $eventParticipants = $event->participants()->wherePivot('status', 'completed')->get();

            if ($event->type === 'tournament') {
                // Tournament with rankings and scores
                foreach ($eventParticipants as $index => $participant) {
                    Result::create([
                        'event_id' => $event->id,
                        'user_id' => $participant->id,
                        'score' => rand(50, 150),
                        'ranking' => $index + 1,
                        'experience_points' => null,
                        'narrative_outcome' => null,
                        'achievements' => $index < 3 ? [
                            ['name' => ($index === 0 ? 'Champion' : ($index === 1 ? 'Runner-up' : 'Third Place')), 'icon' => ($index === 0 ? '🥇' : ($index === 1 ? '🥈' : '🥉')), 'description' => 'Tournament placement'],
                        ] : null,
                        'notes' => null,
                    ]);
                }
            } elseif ($event->type === 'rpg') {
                // RPG with XP and narrative outcomes
                $narratives = [
                    'Successfully defeated the dragon and saved the village!',
                    'Discovered the ancient artifact and unlocked its secrets.',
                    'Made a pact with a mysterious entity for future power.',
                    'Survived the dungeon with clever thinking and teamwork.',
                ];

                foreach ($eventParticipants as $participant) {
                    Result::create([
                        'event_id' => $event->id,
                        'user_id' => $participant->id,
                        'score' => null,
                        'ranking' => null,
                        'experience_points' => rand(1000, 5000),
                        'narrative_outcome' => $narratives[array_rand($narratives)],
                        'achievements' => [
                            ['name' => 'Quest Complete', 'icon' => '⚔️', 'description' => 'Completed the adventure'],
                        ],
                        'notes' => null,
                    ]);
                }
            } else {
                // Other events with simple participation tracking
                foreach ($eventParticipants as $participant) {
                    Result::create([
                        'event_id' => $event->id,
                        'user_id' => $participant->id,
                        'score' => rand(0, 100),
                        'ranking' => null,
                        'experience_points' => null,
                        'narrative_outcome' => null,
                        'achievements' => null,
                        'notes' => 'Participated successfully',
                    ]);
                }
            }
        }
    }
}
