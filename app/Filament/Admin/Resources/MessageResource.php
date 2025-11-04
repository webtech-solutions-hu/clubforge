<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MessageResource\Pages;
use App\Models\Message;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'System Resources';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return (string) Message::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Message Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('message')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('recipient_type')
                            ->label('Send To')
                            ->options([
                                'global' => 'Everyone (Global)',
                                'user' => 'Specific User',
                                'role' => 'Specific Roles',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('user_id', null))
                            ->default('global'),

                        Forms\Components\Select::make('user_id')
                            ->label('Select User')
                            ->options(User::all()->pluck('name', 'id'))
                            ->searchable()
                            ->visible(fn (Forms\Get $get): bool => $get('recipient_type') === 'user')
                            ->required(fn (Forms\Get $get): bool => $get('recipient_type') === 'user'),

                        Forms\Components\Select::make('recipient_roles')
                            ->label('Select Roles')
                            ->options(Role::all()->pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->visible(fn (Forms\Get $get): bool => $get('recipient_type') === 'role')
                            ->required(fn (Forms\Get $get): bool => $get('recipient_type') === 'role'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Message Options')
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->options([
                                'general' => 'General',
                                'announcement' => 'Announcement',
                                'event' => 'Event',
                                'reminder' => 'Reminder',
                                'alert' => 'Alert',
                            ])
                            ->default('general')
                            ->required(),

                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'normal' => 'Normal',
                                'high' => 'High',
                            ])
                            ->default('normal')
                            ->required(),

                        Forms\Components\TextInput::make('icon')
                            ->label('Icon (Heroicon name)')
                            ->placeholder('heroicon-o-bell')
                            ->helperText('Enter a Heroicon name (e.g., heroicon-o-bell)'),

                        Forms\Components\Select::make('icon_color')
                            ->options([
                                'gray' => 'Gray',
                                'primary' => 'Primary',
                                'success' => 'Success',
                                'warning' => 'Warning',
                                'danger' => 'Danger',
                                'info' => 'Info',
                            ])
                            ->default('gray'),

                        Forms\Components\TextInput::make('action_url')
                            ->label('Action URL')
                            ->url()
                            ->columnSpanFull()
                            ->helperText('Optional URL to navigate when message is clicked'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sender.name')
                    ->label('From')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('recipient_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'global' => 'success',
                        'user' => 'info',
                        'role' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'global' => 'Everyone',
                        'user' => 'User',
                        'role' => 'Roles',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Recipient')
                    ->visible(fn ($record) => $record?->recipient_type === 'user')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'normal' => 'info',
                        'high' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Sent At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('recipient_type')
                    ->options([
                        'global' => 'Global',
                        'user' => 'User',
                        'role' => 'Role',
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'general' => 'General',
                        'announcement' => 'Announcement',
                        'event' => 'Event',
                        'reminder' => 'Reminder',
                        'alert' => 'Alert',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->slideOver(),
                Tables\Actions\DeleteAction::make()
                    ->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole(['administrator', 'owner']) || $user->isSupervisor());
    }
}
