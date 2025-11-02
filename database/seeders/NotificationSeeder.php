<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        $this->command->info('Creating sample notifications...');

        foreach ($users as $user) {
            // Welcome notification
            NotificationService::welcome($user);

            // Sample event reminder
            NotificationService::create(
                user: $user,
                type: 'event_reminder',
                title: 'Upcoming Tournament',
                message: 'Chess Tournament starts in 2 hours! Get ready to compete.',
                icon: 'heroicon-o-trophy',
                iconColor: 'amber',
                actionUrl: '/admin/events'
            );

            // Sample post liked notification
            NotificationService::create(
                user: $user,
                type: 'post_liked',
                title: 'Someone liked your post',
                message: 'Your post about game strategies received a new like!',
                icon: 'heroicon-o-heart',
                iconColor: 'red',
                actionUrl: '/admin/posts'
            );

            // Sample comment notification
            NotificationService::create(
                user: $user,
                type: 'post_commented',
                title: 'New comment on your post',
                message: 'Someone commented on your recent post about the gaming event.',
                icon: 'heroicon-o-chat-bubble-left',
                iconColor: 'blue',
                actionUrl: '/admin/posts'
            );

            // Sample role notification
            NotificationService::create(
                user: $user,
                type: 'system',
                title: 'Profile Updated',
                message: 'Your profile information has been successfully updated.',
                icon: 'heroicon-o-check-circle',
                iconColor: 'green',
                actionUrl: '/admin/profile'
            );

            // One more unread notification
            NotificationService::create(
                user: $user,
                type: 'announcement',
                title: 'New Feature Available',
                message: 'Check out the new notifications system! Stay updated with all club activities.',
                icon: 'heroicon-o-sparkles',
                iconColor: 'purple',
                actionUrl: '/admin'
            );
        }

        $this->command->info('Sample notifications created successfully!');
    }
}
