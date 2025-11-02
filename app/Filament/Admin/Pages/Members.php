<?php

namespace App\Filament\Admin\Pages;

use App\Models\User;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Members extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Club Wall';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Members';

    protected static string $view = 'filament.admin.pages.members';

    public static function getNavigationBadge(): ?string
    {
        return User::count();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
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
                    ->sortable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-o-phone'),
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-map-pin'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(',')
                    ->color(fn ($state) => match ($state) {
                        'Administrator' => 'danger',
                        'Owner' => 'warning',
                        'Game Master' => 'info',
                        'Member' => 'success',
                        'Guest' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email Verified')
                    ->nullable()
                    ->placeholder('All users')
                    ->trueLabel('Verified only')
                    ->falseLabel('Unverified only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->slideOver()
                    ->form(function ($record) {
                        $canViewPrivateInfo = auth()->user()?->hasRole(['administrator', 'owner', 'game-master']) ?? false;

                        return [
                            Forms\Components\Section::make('Profile Information')
                                ->schema([
                                    Forms\Components\FileUpload::make('avatar')
                                        ->image()
                                        ->disabled()
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('name')
                                        ->disabled(),
                                    Forms\Components\TextInput::make('email')
                                        ->email()
                                        ->disabled(),
                                ])
                                ->columns(2),

                            Forms\Components\Section::make('Contact Information')
                                ->schema([
                                    Forms\Components\TextInput::make('mobile')
                                        ->tel()
                                        ->disabled(),
                                    Forms\Components\TextInput::make('city')
                                        ->disabled(),
                                    Forms\Components\Textarea::make('address')
                                        ->rows(3)
                                        ->disabled()
                                        ->columnSpanFull(),
                                ])
                                ->columns(2)
                                ->visible($canViewPrivateInfo),

                            Forms\Components\Section::make('Social Media Links')
                                ->schema([
                                    Forms\Components\Repeater::make('social_media_links')
                                        ->schema([
                                            Forms\Components\Select::make('platform')
                                                ->options([
                                                    'facebook' => 'Facebook',
                                                    'twitter' => 'Twitter / X',
                                                    'instagram' => 'Instagram',
                                                    'linkedin' => 'LinkedIn',
                                                    'youtube' => 'YouTube',
                                                    'twitch' => 'Twitch',
                                                    'discord' => 'Discord',
                                                    'github' => 'GitHub',
                                                    'website' => 'Website',
                                                    'other' => 'Other',
                                                ])
                                                ->disabled(),
                                            Forms\Components\TextInput::make('url')
                                                ->label('URL')
                                                ->disabled(),
                                        ])
                                        ->columns(2)
                                        ->disabled()
                                        ->itemLabel(fn (array $state): ?string => $state['platform'] ?? null),
                                ])
                                ->collapsed()
                                ->visible(fn ($record) => !empty($record->social_media_links)),

                            Forms\Components\Section::make('About')
                                ->schema([
                                    Forms\Components\Textarea::make('bio')
                                        ->label('Biography')
                                        ->rows(5)
                                        ->disabled()
                                        ->columnSpanFull(),
                                ])
                                ->visible(fn ($record) => !empty($record->bio)),

                            Forms\Components\Section::make('Roles')
                                ->schema([
                                    Forms\Components\CheckboxList::make('roles')
                                        ->relationship('roles', 'name')
                                        ->label('User Roles')
                                        ->disabled()
                                        ->columns(3)
                                        ->gridDirection('row'),
                                ]),
                        ];
                    }),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns);
    }
}
