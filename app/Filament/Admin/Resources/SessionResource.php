<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SessionResource\Pages;
use App\Filament\Admin\Resources\SessionResource\RelationManagers;
use App\Models\Session;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SessionResource extends Resource
{
    protected static ?string $model = Session::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?string $navigationGroup = 'System Monitoring';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Session Information')
                    ->schema([
                        Forms\Components\TextInput::make('id')
                            ->label('Session ID')
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabled(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address')
                            ->disabled(),
                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('last_activity')
                            ->label('Last Activity')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Session ID')
                    ->searchable()
                    ->copyable()
                    ->limit(20),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable()
                    ->default('Guest'),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state;
                        }
                        return null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('last_activity')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->description(fn ($record) => $record->last_activity->format('Y-m-d H:i:s')),
            ])
            ->defaultSort('last_activity', 'desc')
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Active Sessions')
                    ->query(fn (Builder $query): Builder => $query->where('last_activity', '>', now()->subMinutes(30))),
                Tables\Filters\Filter::make('authenticated')
                    ->label('Authenticated Only')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('user_id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->slideOver(),
                Tables\Actions\Action::make('terminate')
                    ->label('')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Session $record) => $record->delete())
                    ->successNotification(
                        fn () => \Filament\Notifications\Notification::make()
                            ->title('Session terminated')
                            ->success()
                            ->send()
                    ),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Terminate Selected')
                        ->successNotification(
                            fn () => \Filament\Notifications\Notification::make()
                                ->title('Sessions terminated')
                                ->success()
                                ->send()
                        ),
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
            'index' => Pages\ListSessions::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canView($record): bool
    {
        return true;
    }
}
