<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Result;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MyResults extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Result::query()
                    ->where('user_id', auth()->id())
                    ->with('event')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('event.image')
                    ->label('Event')
                    ->size(40),
                Tables\Columns\TextColumn::make('event.name')
                    ->searchable()
                    ->weight('bold')
                    ->url(fn ($record) => route('filament.admin.resources.events.view', $record->event)),
                Tables\Columns\TextColumn::make('event.type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'board-game' => 'success',
                        'rpg' => 'info',
                        'tournament' => 'warning',
                        'workshop' => 'purple',
                        'social' => 'pink',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('ranking')
                    ->formatStateUsing(fn ($record) => $record->ranking_badge)
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        1 => 'warning',
                        2 => 'gray',
                        3 => 'orange',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('score')
                    ->badge()
                    ->color('success')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('experience_points')
                    ->label('XP')
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('event.start_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
            ])
            ->heading('My Recent Results')
            ->emptyStateHeading('No Results Yet')
            ->emptyStateDescription('Your event results will appear here.')
            ->emptyStateIcon('heroicon-o-trophy');
    }
}
