<?php

namespace App\Filament\Admin\Resources\SessionResource\Pages;

use App\Filament\Admin\Resources\SessionResource;
use App\Models\Session;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSessions extends ListRecords
{
    protected static string $resource = SessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('clear_expired')
                ->label('Clear Expired Sessions')
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $count = Session::where('last_activity', '<', now()->subHours(2))->delete();
                    \Filament\Notifications\Notification::make()
                        ->title("Cleared {$count} expired session(s)")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Sessions'),
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('last_activity', '>', now()->subMinutes(30)))
                ->badge(Session::where('last_activity', '>', now()->subMinutes(30))->count()),
            'authenticated' => Tab::make('Authenticated')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('user_id'))
                ->badge(Session::whereNotNull('user_id')->count()),
            'guests' => Tab::make('Guests')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('user_id'))
                ->badge(Session::whereNull('user_id')->count()),
        ];
    }
}
