<?php

namespace App\Filament\Admin\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';

    protected static ?string $title = 'Participants';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('participants', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn ($context) => $context === 'edit'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'declined' => 'Declined',
                        'completed' => 'Completed',
                    ])
                    ->required()
                    ->native(false),
                Forms\Components\Select::make('role')
                    ->options([
                        'gm' => 'Game Master',
                        'player' => 'Player',
                        'spectator' => 'Spectator',
                    ])
                    ->required()
                    ->native(false)
                    ->default('player'),
                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pivot.role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'gm' => 'danger',
                        'player' => 'success',
                        'spectator' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('pivot.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'declined' => 'danger',
                        'completed' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('pivot.notes')
                    ->label('Notes')
                    ->limit(30)
                    ->toggleable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('pivot.created_at')
                    ->label('Joined')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('event_user.created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'declined' => 'Declined',
                        'completed' => 'Completed',
                    ])
                    ->attribute('pivot.status'),
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'gm' => 'Game Master',
                        'player' => 'Player',
                        'spectator' => 'Spectator',
                    ])
                    ->attribute('pivot.role'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Add Participant')
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('User')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'declined' => 'Declined',
                                'completed' => 'Completed',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('role')
                            ->options([
                                'gm' => 'Game Master',
                                'player' => 'Player',
                                'spectator' => 'Spectator',
                            ])
                            ->default('player')
                            ->required()
                            ->native(false),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ])
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn ($record, $livewire) =>
                        $livewire->ownerRecord->participants()->updateExistingPivot($record->id, ['status' => 'confirmed'])
                    )
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->pivot->status === 'pending'),
                Tables\Actions\Action::make('decline')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(fn ($record, $livewire) =>
                        $livewire->ownerRecord->participants()->updateExistingPivot($record->id, ['status' => 'declined'])
                    )
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->pivot->status === 'pending'),
                Tables\Actions\Action::make('complete')
                    ->icon('heroicon-o-check-badge')
                    ->color('info')
                    ->action(fn ($record, $livewire) =>
                        $livewire->ownerRecord->participants()->updateExistingPivot($record->id, ['status' => 'completed'])
                    )
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->pivot->status === 'confirmed'),
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'declined' => 'Declined',
                                'completed' => 'Completed',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('role')
                            ->options([
                                'gm' => 'Game Master',
                                'player' => 'Player',
                                'spectator' => 'Spectator',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ])
                    ->mutateRecordDataUsing(function (array $data, $record): array {
                        $data['status'] = $record->pivot->status;
                        $data['role'] = $record->pivot->role;
                        $data['notes'] = $record->pivot->notes;
                        return $data;
                    })
                    ->using(function ($record, array $data, $livewire): void {
                        $livewire->ownerRecord->participants()->updateExistingPivot($record->id, $data);
                    }),
                Tables\Actions\DetachAction::make()
                    ->label('Remove'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\AttachAction::make()
                    ->label('Add First Participant')
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('User')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'declined' => 'Declined',
                                'completed' => 'Completed',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('role')
                            ->options([
                                'gm' => 'Game Master',
                                'player' => 'Player',
                                'spectator' => 'Spectator',
                            ])
                            ->default('player')
                            ->required()
                            ->native(false),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                    ])
                    ->preloadRecordSelect(),
            ]);
    }
}
