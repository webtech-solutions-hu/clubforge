<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EventResource\Pages;
use App\Filament\Admin\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Events';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'upcoming')->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Event Information')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('events')
                            ->disk('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->options([
                                'board-game' => 'Board Game',
                                'rpg' => 'RPG Session',
                                'tournament' => 'Tournament',
                                'workshop' => 'Workshop',
                                'social' => 'Social Event',
                                'other' => 'Other',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('organizer_id')
                            ->label('Organizer')
                            ->relationship('organizer', 'name')
                            ->required()
                            ->default(fn () => auth()->id())
                            ->searchable()
                            ->preload(),
                        Forms\Components\Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('start_date')
                            ->required()
                            ->native(false)
                            ->seconds(false),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->native(false)
                            ->seconds(false),
                        Forms\Components\TextInput::make('max_participants')
                            ->numeric()
                            ->minValue(1)
                            ->label('Max Participants (leave empty for unlimited)'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'upcoming' => 'Upcoming',
                                'ongoing' => 'Ongoing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('upcoming')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                Tables\Columns\TextColumn::make('organizer.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),
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
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'upcoming' => 'info',
                        'ongoing' => 'success',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('start_date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'board-game' => 'Board Game',
                        'rpg' => 'RPG Session',
                        'tournament' => 'Tournament',
                        'workshop' => 'Workshop',
                        'social' => 'Social Event',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'upcoming' => 'Upcoming',
                        'ongoing' => 'Ongoing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('join')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->action(function ($record) {
                        $record->participants()->attach(auth()->id(), [
                            'status' => 'pending',
                            'role' => 'player',
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn ($record) => !$record->isParticipant(auth()->user()) && !$record->isFull()),
                Tables\Actions\Action::make('leave')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->action(fn ($record) => $record->participants()->detach(auth()->id()))
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->isParticipant(auth()->user())),
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->slideOver(),
                Tables\Actions\EditAction::make()
                    ->label(''),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->visible(fn ($record) => auth()->user()?->hasRole('administrator') || auth()->id() === $record->organizer_id),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('administrator')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ParticipantsRelationManager::class,
            RelationManagers\ResultsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'view' => Pages\ViewEvent::route('/{record}'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole(['administrator', 'game-master', 'owner']) ?? false;
    }
}
