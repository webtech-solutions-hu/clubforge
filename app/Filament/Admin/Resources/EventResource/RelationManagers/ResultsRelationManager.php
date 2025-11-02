<?php

namespace App\Filament\Admin\Resources\EventResource\RelationManagers;

use App\Models\AuditLog;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'results';

    protected static ?string $title = 'Results';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Participant')
                    ->options(function ($livewire) {
                        return $livewire->ownerRecord->confirmedParticipants()
                            ->pluck('name', 'users.id');
                    })
                    ->required()
                    ->searchable()
                    ->disabled(fn ($context) => $context === 'edit'),

                Forms\Components\Section::make('Performance Metrics')
                    ->schema([
                        Forms\Components\TextInput::make('score')
                            ->numeric()
                            ->label('Score/Points')
                            ->placeholder('Enter score'),
                        Forms\Components\TextInput::make('ranking')
                            ->numeric()
                            ->minValue(1)
                            ->label('Ranking/Placement')
                            ->placeholder('1, 2, 3, etc.')
                            ->helperText('1 for 1st place, 2 for 2nd, etc.'),
                        Forms\Components\TextInput::make('experience_points')
                            ->numeric()
                            ->label('Experience Points (XP)')
                            ->placeholder('XP earned')
                            ->helperText('For RPG sessions'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('RPG Outcome')
                    ->schema([
                        Forms\Components\Textarea::make('narrative_outcome')
                            ->label('Narrative Outcome')
                            ->rows(4)
                            ->placeholder('Describe what happened in the story...')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Achievements & Awards')
                    ->schema([
                        Forms\Components\Repeater::make('achievements')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->placeholder('Achievement name'),
                                Forms\Components\TextInput::make('icon')
                                    ->placeholder('🏆 (emoji or icon)'),
                                Forms\Components\Textarea::make('description')
                                    ->rows(2)
                                    ->placeholder('Achievement description'),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->addActionLabel('Add Achievement')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Additional Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->placeholder('Additional notes or comments...')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\ImageColumn::make('user.avatar')
                    ->label('User')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('ranking')
                    ->label('Rank')
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->ranking_badge)
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        1 => 'warning',
                        2 => 'gray',
                        3 => 'orange',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->sortable()
                    ->numeric()
                    ->placeholder('—')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('experience_points')
                    ->label('XP')
                    ->sortable()
                    ->numeric()
                    ->placeholder('—')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('achievements')
                    ->label('Achievements')
                    ->formatStateUsing(fn ($state) => $state ? count($state) : 0)
                    ->badge()
                    ->color('purple')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('narrative_outcome')
                    ->label('Outcome')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recorded')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('ranking', 'asc')
            ->filters([
                Tables\Filters\Filter::make('has_ranking')
                    ->label('With Ranking')
                    ->query(fn ($query) => $query->whereNotNull('ranking')),
                Tables\Filters\Filter::make('has_score')
                    ->label('With Score')
                    ->query(fn ($query) => $query->whereNotNull('score')),
                Tables\Filters\Filter::make('has_xp')
                    ->label('With XP')
                    ->query(fn ($query) => $query->whereNotNull('experience_points')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Record Result')
                    ->after(function ($record, $livewire) {
                        $user = User::find($record->user_id);

                        // Log result recording
                        AuditLog::log(
                            user: $user,
                            causer: auth()->user(),
                            eventType: 'result_recorded',
                            description: auth()->user()->name . ' recorded results for ' . $user->name . ' in event: ' . $livewire->ownerRecord->name,
                            properties: [
                                'event_id' => $livewire->ownerRecord->id,
                                'event_name' => $livewire->ownerRecord->name,
                                'result_id' => $record->id,
                                'ranking' => $record->ranking,
                                'score' => $record->score,
                                'experience_points' => $record->experience_points,
                            ]
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->slideOver(),
                Tables\Actions\EditAction::make()
                    ->after(function ($record, $livewire) {
                        $user = User::find($record->user_id);

                        // Log result update
                        AuditLog::log(
                            user: $user,
                            causer: auth()->user(),
                            eventType: 'result_updated',
                            description: auth()->user()->name . ' updated results for ' . $user->name . ' in event: ' . $livewire->ownerRecord->name,
                            properties: [
                                'event_id' => $livewire->ownerRecord->id,
                                'event_name' => $livewire->ownerRecord->name,
                                'result_id' => $record->id,
                                'ranking' => $record->ranking,
                                'score' => $record->score,
                                'experience_points' => $record->experience_points,
                            ]
                        );
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record, $livewire) {
                        $user = User::find($record->user_id);

                        // Log result deletion
                        AuditLog::log(
                            user: $user,
                            causer: auth()->user(),
                            eventType: 'result_deleted',
                            description: auth()->user()->name . ' deleted results for ' . $user->name . ' in event: ' . $livewire->ownerRecord->name,
                            properties: [
                                'event_id' => $livewire->ownerRecord->id,
                                'event_name' => $livewire->ownerRecord->name,
                                'result_id' => $record->id,
                            ]
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Results Yet')
            ->emptyStateDescription('Record results for participants after the event.')
            ->emptyStateIcon('heroicon-o-trophy')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Record First Result'),
            ]);
    }

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        // Only show results tab for completed events or to organizers/admins
        return $ownerRecord->status === 'completed' ||
               auth()->id() === $ownerRecord->organizer_id ||
               auth()->user()?->hasRole('administrator');
    }
}
