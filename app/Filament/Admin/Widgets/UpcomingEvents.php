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
            ->heading('Upcoming Events')
            ->emptyStateHeading('No Upcoming Events')
            ->emptyStateDescription('There are no upcoming events scheduled.')
            ->emptyStateIcon('heroicon-o-calendar');
    }
}
