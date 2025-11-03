<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Event;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingEvents extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Event::query()
                    ->where('status', 'upcoming')
                    ->where('start_date', '>', now())
                    ->orderBy('start_date', 'asc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold')
                    ->url(fn ($record) => route('filament.admin.resources.events.view', $record)),
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
                    ->since(),
                Tables\Columns\TextColumn::make('location')
                    ->limit(30)
                    ->icon('heroicon-o-map-pin'),
                Tables\Columns\TextColumn::make('participants_count')
                    ->label('Participants')
                    ->counts('confirmedParticipants')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->max_participants
                            ? "{$state}/{$record->max_participants}"
                            : $state
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('join')
                    ->label('Join')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->action(function ($record) {
                        $record->participants()->attach(auth()->id(), [
                            'status' => 'pending',
                            'role' => 'player',
                        ]);
                    })
                    ->requiresConfirmation()
                    ->successNotificationTitle('Join request sent')
                    ->visible(fn ($record) =>
                        !$record->isParticipant(auth()->user()) &&
                        !$record->isFull()
                    ),
                Tables\Actions\Action::make('leave')
                    ->label('Leave')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->action(fn ($record) => $record->participants()->detach(auth()->id()))
                    ->requiresConfirmation()
                    ->successNotificationTitle('You have left the event')
                    ->visible(fn ($record) =>
                        $record->isParticipant(auth()->user()) &&
                        auth()->id() !== $record->organizer_id
                    ),
                Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->slideOver(),
            ])
            ->heading('Upcoming Events')
            ->emptyStateHeading('No Upcoming Events')
            ->emptyStateDescription('There are no upcoming events scheduled.')
            ->emptyStateIcon('heroicon-o-calendar');
    }
}
