<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Event;
use App\Models\Post;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClubStats extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $totalMembers = User::count();
        $newMembersThisMonth = User::whereMonth('created_at', now()->month)->count();

        $totalEvents = Event::count();
        $upcomingEvents = Event::where('status', 'upcoming')->count();
        $completedEvents = Event::where('status', 'completed')->count();

        $totalPosts = Post::count();
        $postsThisWeek = Post::where('created_at', '>=', now()->subWeek())->count();

        return [
            Stat::make('Total Members', $totalMembers)
                ->description($newMembersThisMonth . ' new this month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Events', $totalEvents)
                ->description($upcomingEvents . ' upcoming, ' . $completedEvents . ' completed')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Message Board Activity', $totalPosts)
                ->description($postsThisWeek . ' posts this week')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning'),
        ];
    }
}
