<?php

namespace App\Filament\Admin\Pages;

use App\Models\Event;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyEvents extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'My Data';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'My Events';

    protected static string $view = 'filament.admin.pages.my-events';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Event::query()
                    ->whereHas('participants', function (Builder $query) {
                        $query->where('user_id', auth()->id());
                    })
                    ->with(['participants' => function ($query) {
                        $query->where('user_id', auth()->id());
                    }])
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'board-game' => 'success',
                        'rpg' => 'info',
                        'tournament' => 'warning',
                        'workshop' => 'purple',
                        'social' => 'pink',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                Tables\Columns\TextColumn::make('location')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('participants')
                    ->label('My Role')
                    ->formatStateUsing(fn ($record) =>
                        $record->participants->first()?->pivot->role ?? 'Unknown'
                    )
                    ->badge()
                    ->color(fn ($record): string => match ($record->participants->first()?->pivot->role) {
                        'gm' => 'danger',
                        'player' => 'success',
                        'spectator' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('participants')
                    ->label('My Status')
                    ->formatStateUsing(fn ($record) =>
                        $record->participants->first()?->pivot->status ?? 'Unknown'
                    )
                    ->badge()
                    ->color(fn ($record): string => match ($record->participants->first()?->pivot->status) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'declined' => 'danger',
                        'completed' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'upcoming' => 'info',
                        'ongoing' => 'success',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('results')
                    ->label('My Ranking')
                    ->formatStateUsing(function ($record) {
                        $result = $record->results()->where('user_id', auth()->id())->first();
                        return $result?->ranking_badge ?? '—';
                    })
                    ->badge()
                    ->color(fn ($record): string => {
                        $result = $record->results()->where('user_id', auth()->id())->first();
                        return match ($result?->ranking) {
                            1 => 'warning',
                            2 => 'gray',
                            3 => 'orange',
                            default => 'info',
                        };
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('results')
                    ->label('My Score')
                    ->formatStateUsing(function ($record) {
                        $result = $record->results()->where('user_id', auth()->id())->first();
                        return $result?->score ?? '—';
                    })
                    ->badge()
                    ->color('success')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('results')
                    ->label('My XP')
                    ->formatStateUsing(function ($record) {
                        $result = $record->results()->where('user_id', auth()->id())->first();
                        return $result?->experience_points ?? '—';
                    })
                    ->badge()
                    ->color('info')
                    ->toggleable(),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('my_status')
                    ->label('My Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'declined' => 'Declined',
                        'completed' => 'Completed',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (filled($data['value'])) {
                            $query->whereHas('participants', function (Builder $q) use ($data) {
                                $q->where('user_id', auth()->id())
                                  ->where('event_user.status', $data['value']);
                            });
                        }
                    }),
                Tables\Filters\SelectFilter::make('my_role')
                    ->label('My Role')
                    ->options([
                        'gm' => 'Game Master',
                        'player' => 'Player',
                        'spectator' => 'Spectator',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (filled($data['value'])) {
                            $query->whereHas('participants', function (Builder $q) use ($data) {
                                $q->where('user_id', auth()->id())
                                  ->where('event_user.role', $data['value']);
                            });
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('leave')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->action(fn ($record) => $record->participants()->detach(auth()->id()))
                    ->requiresConfirmation(),
            ])
            ->emptyStateHeading('No Events')
            ->emptyStateDescription('You haven\'t joined any events yet.')
            ->emptyStateIcon('heroicon-o-calendar');
    }
}
