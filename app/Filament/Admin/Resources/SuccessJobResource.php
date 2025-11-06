<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SuccessJobResource\Pages;
use App\Models\SuccessJob;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SuccessJobResource extends Resource
{
    protected static ?string $model = SuccessJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'System Settings';

    protected static ?string $navigationLabel = 'Completed Jobs';

    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->label('ID')
                    ->disabled(),
                Forms\Components\TextInput::make('uuid')
                    ->label('UUID')
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('job_name')
                    ->label('Job Name')
                    ->disabled(),
                Forms\Components\TextInput::make('queue')
                    ->disabled(),
                Forms\Components\TextInput::make('connection')
                    ->disabled(),
                Forms\Components\TextInput::make('execution_time_human')
                    ->label('Execution Time')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('completed_at')
                    ->disabled(),
                Forms\Components\Textarea::make('payload')
                    ->label('Payload (JSON)')
                    ->disabled()
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT))
                    ->rows(10)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('job_name')
                    ->label('Job')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('queue')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('execution_time_human')
                    ->label('Duration')
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy('execution_time', $direction))
                    ->badge()
                    ->color(fn (SuccessJob $record): string => match (true) {
                        $record->execution_time < 1000 => 'success',
                        $record->execution_time < 5000 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(20),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('queue')
                    ->options(fn () => SuccessJob::query()->distinct()->pluck('queue', 'queue')->toArray())
                    ->label('Queue'),
                Tables\Filters\Filter::make('slow_jobs')
                    ->label('Slow Jobs (>5s)')
                    ->query(fn (Builder $query): Builder => $query->where('execution_time', '>', 5000)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->slideOver(),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Job record deleted')
                            ->body('The completed job record has been removed.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Job records deleted')
                                ->body('Selected completed job records have been removed.')
                        ),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('clear_old')
                    ->label('Clear Old Jobs')
                    ->icon('heroicon-o-trash')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Clear Old Completed Jobs')
                    ->modalDescription('This will delete all completed job records older than 7 days.')
                    ->modalSubmitActionLabel('Yes, clear old jobs')
                    ->action(function () {
                        $count = SuccessJob::where('completed_at', '<', now()->subDays(7))->count();
                        SuccessJob::where('completed_at', '<', now()->subDays(7))->delete();
                        Notification::make()
                            ->success()
                            ->title('Old jobs cleared')
                            ->body("Deleted {$count} job records older than 7 days.")
                            ->send();
                    }),
                Tables\Actions\Action::make('clear_all')
                    ->label('Clear All')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Clear All Completed Jobs')
                    ->modalDescription('Are you sure you want to delete all completed job records? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, clear all')
                    ->action(function () {
                        $count = SuccessJob::count();
                        SuccessJob::truncate();
                        Notification::make()
                            ->success()
                            ->title('All jobs cleared')
                            ->body("Deleted {$count} completed job records.")
                            ->send();
                    }),
            ])
            ->emptyStateHeading('No completed jobs')
            ->emptyStateDescription('Completed jobs will appear here once they finish processing.')
            ->emptyStateIcon('heroicon-o-check-circle');
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
            'index' => Pages\ListSuccessJobs::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isSupervisor() ?? false;
    }
}
