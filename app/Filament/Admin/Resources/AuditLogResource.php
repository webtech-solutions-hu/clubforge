<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AuditLogResource\Pages;
use App\Filament\Admin\Resources\AuditLogResource\RelationManagers;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Roles & Permissions';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Audit Logs';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Audit Log Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('User')
                            ->disabled(),
                        Forms\Components\Select::make('causer_id')
                            ->relationship('causer', 'name')
                            ->label('Caused By')
                            ->disabled(),
                        Forms\Components\TextInput::make('event_type')
                            ->disabled(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address')
                            ->disabled(),
                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent')
                            ->disabled()
                            ->rows(2),
                        Forms\Components\Textarea::make('description')
                            ->disabled()
                            ->rows(2),
                        Forms\Components\KeyValue::make('properties')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event_type')
                    ->label('Event')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'login' => 'success',
                        'roles_changed', 'role_assigned', 'role_removed' => 'warning',
                        'event_created', 'participant_added', 'result_recorded' => 'info',
                        'event_updated', 'participant_confirmed', 'result_updated' => 'success',
                        'event_deleted', 'participant_declined', 'result_deleted' => 'danger',
                        'participant_completed' => 'purple',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Caused By')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date/Time')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->description(fn ($record) => $record->created_at->format('Y-m-d H:i:s')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event_type')
                    ->options([
                        'login' => 'Login',
                        'roles_changed' => 'Roles Changed',
                        'role_assigned' => 'Role Assigned',
                        'role_removed' => 'Role Removed',
                        'event_created' => 'Event Created',
                        'event_updated' => 'Event Updated',
                        'event_deleted' => 'Event Deleted',
                        'participant_added' => 'Participant Added',
                        'participant_confirmed' => 'Participant Confirmed',
                        'participant_declined' => 'Participant Declined',
                        'participant_completed' => 'Participant Completed',
                        'result_recorded' => 'Result Recorded',
                        'result_updated' => 'Result Updated',
                        'result_deleted' => 'Result Deleted',
                    ]),
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->slideOver(),
            ], position: Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                // No bulk actions for audit logs
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
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
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

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        // Only administrators can access audit logs
        return auth()->user()?->hasRole('administrator') ?? false;
    }
}
