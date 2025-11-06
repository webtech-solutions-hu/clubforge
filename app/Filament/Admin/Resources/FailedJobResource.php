<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FailedJobResource\Pages;
use App\Models\FailedJob;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-x-circle';

    protected static ?string $navigationGroup = 'System Settings';

    protected static ?string $navigationLabel = 'Failed Jobs';

    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
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
                Forms\Components\DateTimePicker::make('failed_at')
                    ->disabled(),
                Forms\Components\Textarea::make('exception_message')
                    ->label('Error Message')
                    ->disabled()
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('exception')
                    ->label('Full Stack Trace')
                    ->disabled()
                    ->rows(10)
                    ->columnSpanFull(),
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
                    ->limit(40),
                Tables\Columns\TextColumn::make('queue')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('exception_message')
                    ->label('Error')
                    ->limit(50)
                    ->searchable(query: function ($query, $search) {
                        return $query->where('exception', 'like', "%{$search}%");
                    })
                    ->tooltip(fn ($record) => $record->exception_message),
                Tables\Columns\TextColumn::make('failed_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('queue')
                    ->options(fn () => FailedJob::query()->distinct()->pluck('queue', 'queue')->toArray())
                    ->label('Queue'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->slideOver(),
                Tables\Actions\Action::make('retry')
                    ->label('')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Retry Failed Job')
                    ->modalDescription('This will attempt to re-run the failed job.')
                    ->modalSubmitActionLabel('Retry Job')
                    ->action(function (FailedJob $record) {
                        Artisan::call('queue:retry', ['id' => [$record->uuid]]);
                        $record->delete();
                        Notification::make()
                            ->success()
                            ->title('Job retried')
                            ->body('The job has been queued for retry.')
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Failed job deleted')
                            ->body('The failed job record has been removed.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('retry')
                        ->label('Retry Selected')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Retry Selected Failed Jobs')
                        ->modalDescription('This will attempt to re-run all selected failed jobs.')
                        ->modalSubmitActionLabel('Retry Jobs')
                        ->action(function ($records) {
                            $uuids = $records->pluck('uuid')->toArray();
                            Artisan::call('queue:retry', ['id' => $uuids]);
                            FailedJob::whereIn('uuid', $uuids)->delete();
                            Notification::make()
                                ->success()
                                ->title('Jobs retried')
                                ->body(count($uuids) . ' jobs have been queued for retry.')
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Failed jobs deleted')
                                ->body('Selected failed job records have been removed.')
                        ),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('retry_all')
                    ->label('Retry All')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Retry All Failed Jobs')
                    ->modalDescription('Are you sure you want to retry all failed jobs?')
                    ->modalSubmitActionLabel('Yes, retry all')
                    ->action(function () {
                        $count = FailedJob::count();
                        Artisan::call('queue:retry', ['id' => ['all']]);
                        FailedJob::truncate();
                        Notification::make()
                            ->success()
                            ->title('All jobs retried')
                            ->body("{$count} jobs have been queued for retry.")
                            ->send();
                    }),
                Tables\Actions\Action::make('clear_all')
                    ->label('Clear All')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Clear All Failed Jobs')
                    ->modalDescription('Are you sure you want to delete all failed job records? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, clear all')
                    ->action(function () {
                        $count = FailedJob::count();
                        FailedJob::truncate();
                        Notification::make()
                            ->success()
                            ->title('Failed jobs cleared')
                            ->body("Deleted {$count} failed job records.")
                            ->send();
                    }),
            ])
            ->emptyStateHeading('No failed jobs')
            ->emptyStateDescription('Great! There are no failed jobs at the moment.')
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
            'index' => Pages\ListFailedJobs::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isSupervisor() ?? false;
    }
}
