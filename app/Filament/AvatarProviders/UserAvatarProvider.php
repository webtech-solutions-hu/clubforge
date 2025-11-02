<?php

namespace App\Filament\AvatarProviders;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class UserAvatarProvider implements AvatarProvider
{
    public function get(Model | Authenticatable $record): string
    {
        if ($record->avatar) {
            return asset('storage/' . $record->avatar);
        }

        // Fallback to UI Avatars with user's name initials
        $name = str(Filament::getNameForDefaultAvatar($record))
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=F59E0B';
    }
}
