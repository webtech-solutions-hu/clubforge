<?php

namespace App\Filament\Admin\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'My Club';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.admin.pages.profile';

    protected static ?string $title = 'My Profile';

    protected static ?string $slug = 'profile';

    public static function canAccess(): bool
    {
        // Allow all authenticated users to access their profile
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit_profile')
                ->label('Edit Profile')
                ->icon('heroicon-o-pencil-square')
                ->url(route('filament.admin.pages.edit-profile'))
                ->color('primary'),
            Action::make('my_events')
                ->label('My Events')
                ->icon('heroicon-o-calendar')
                ->url(route('filament.admin.pages.my-events'))
                ->color('info'),
            Action::make('message_board')
                ->label('Message Board')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->url(route('filament.admin.resources.posts.index'))
                ->color('success'),
        ];
    }
}
