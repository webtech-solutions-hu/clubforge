<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'mobile' => '+1-555-0101',
                'city' => 'New York',
                'address' => '123 Admin Street, NY 10001',
                'bio' => 'System administrator and club organizer.',
                'social_media_links' => [
                    ['platform' => 'github', 'url' => 'https://github.com/admin'],
                    ['platform' => 'discord', 'url' => 'https://discord.gg/admin'],
                ],
                'roles' => ['administrator'],
            ],
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'mobile' => '+1-555-0102',
                'city' => 'Los Angeles',
                'address' => '456 Game Ave, LA 90001',
                'bio' => 'Passionate board game enthusiast and RPG game master with 10 years of experience.',
                'social_media_links' => [
                    ['platform' => 'twitter', 'url' => 'https://twitter.com/johnsmith'],
                    ['platform' => 'twitch', 'url' => 'https://twitch.tv/johnsmith'],
                ],
                'roles' => ['game-master'],
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.j@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'mobile' => '+1-555-0103',
                'city' => 'Chicago',
                'address' => '789 Club Blvd, Chicago 60601',
                'bio' => 'Club owner and event organizer. Love bringing people together through gaming.',
                'social_media_links' => [
                    ['platform' => 'facebook', 'url' => 'https://facebook.com/sarahjohnson'],
                    ['platform' => 'instagram', 'url' => 'https://instagram.com/sarahj'],
                ],
                'roles' => ['owner'],
            ],
            [
                'name' => 'Mike Anderson',
                'email' => 'mike.anderson@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'mobile' => '+1-555-0104',
                'city' => 'Seattle',
                'address' => '321 Board St, Seattle 98101',
                'bio' => 'Tournament organizer and competitive player.',
                'social_media_links' => [
                    ['platform' => 'youtube', 'url' => 'https://youtube.com/mikeanderson'],
                ],
                'roles' => ['game-master'],
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'mobile' => '+1-555-0105',
                'city' => 'Boston',
                'address' => '654 Player Rd, Boston 02101',
                'bio' => 'Longtime member of the gaming community. Favorite games include Catan and D&D.',
                'social_media_links' => [
                    ['platform' => 'discord', 'url' => 'https://discord.gg/emilyd'],
                ],
                'roles' => ['member'],
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.w@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'mobile' => '+1-555-0106',
                'city' => 'Austin',
                'address' => '987 Gamer Ln, Austin 78701',
                'bio' => 'RPG enthusiast and character builder extraordinaire.',
                'roles' => ['member'],
            ],
            [
                'name' => 'Lisa Martinez',
                'email' => 'lisa.m@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'mobile' => '+1-555-0107',
                'city' => 'Denver',
                'address' => '147 Card St, Denver 80201',
                'bio' => 'Card game specialist and strategy expert.',
                'social_media_links' => [
                    ['platform' => 'website', 'url' => 'https://lisamartinez.com'],
                ],
                'roles' => ['member'],
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'robert.t@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'mobile' => '+1-555-0108',
                'city' => 'Portland',
                'address' => '258 Dice Ave, Portland 97201',
                'bio' => 'Miniature painter and tabletop wargame player.',
                'roles' => ['member'],
            ],
            [
                'name' => 'Jennifer Brown',
                'email' => 'jennifer.b@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'mobile' => '+1-555-0109',
                'city' => 'Phoenix',
                'address' => '369 Token Dr, Phoenix 85001',
                'bio' => 'Casual player who loves social gaming events.',
                'roles' => ['member'],
            ],
            [
                'name' => 'James Miller',
                'email' => 'james.m@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'mobile' => '+1-555-0110',
                'city' => 'San Francisco',
                'address' => '741 Quest Blvd, SF 94101',
                'bio' => 'Game designer and playtest coordinator.',
                'social_media_links' => [
                    ['platform' => 'linkedin', 'url' => 'https://linkedin.com/in/jamesmiller'],
                    ['platform' => 'github', 'url' => 'https://github.com/jamesmiller'],
                ],
                'roles' => ['game-master'],
            ],
            [
                'name' => 'New Guest',
                'email' => 'guest@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => null,
                'mobile' => null,
                'city' => null,
                'address' => null,
                'bio' => null,
                'roles' => ['guest'],
            ],
            [
                'name' => 'Alex Thompson',
                'email' => 'alex.t@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'mobile' => '+1-555-0111',
                'city' => 'Miami',
                'address' => '852 Victory Way, Miami 33101',
                'bio' => 'Speed chess champion and puzzle solver.',
                'roles' => ['member'],
            ],
        ];

        foreach ($users as $userData) {
            $roles = $userData['roles'];
            unset($userData['roles']);

            $user = User::create($userData);

            foreach ($roles as $roleSlug) {
                $role = \App\Models\Role::where('slug', $roleSlug)->first();
                if ($role) {
                    $user->roles()->attach($role->id);
                }
            }
        }
    }
}
