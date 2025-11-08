<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\JobResource\Pages;
use App\Models\Job;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationGroup = 'System Settings';

    protected static ?string $navigationLabel = 'Queue Jobs';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $count = static::getModel()::count();
        return $count > 10 ? 'warning' : 'success';
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
                    ->label('Job ID')
                    ->disabled(),
                Forms\Components\TextInput::make('queue')
                    ->disabled(),
                Forms\Components\TextInput::make('job_name')
                    ->label('Job Name')
                    ->disabled(),
                Forms\Components\TextInput::make('attempts')
                    ->disabled(),
                Forms\Components\TextInput::make('created_at_human')
                    ->label('Created At')
                    ->disabled(),
                Forms\Components\TextInput::make('available_at_human')
                    ->label('Available At')
                    ->disabled(),
                Forms\Components\Textarea::make('payload')
                    ->label('Payload (JSON)')
                    ->disabled()
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT))
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
                    ->color(fn (string $state): string => match ($state) {
                        'default' => 'gray',
                        'high' => 'danger',
                        'low' => 'info',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('attempts')
                    ->label('Tries')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => $state > 2 ? 'warning' : 'success'),
                Tables\Columns\TextColumn::make('created_at_human')
                    ->label('Created')
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy('created_at', $direction)),
                Tables\Columns\TextColumn::make('available_at_human')
                    ->label('Available')
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy('available_at', $direction))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('queue')
                    ->options(fn () => Job::query()->distinct()->pluck('queue', 'queue')->toArray())
                    ->label('Queue'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->slideOver(),
                Tables\Actions\Action::make('process')
                    ->label('')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->tooltip('Process this queue now')
                    ->requiresConfirmation()
                    ->modalHeading('Process Queue')
                    ->modalDescription(fn ($record) => "This will start processing jobs from the '{$record->queue}' queue.")
                    ->modalSubmitActionLabel('Start Processing')
                    ->action(function (Job $record) {
                        Artisan::call('queue:work', [
                            '--queue' => $record->queue,
                            '--once' => true,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Queue processing started')
                            ->body("Processing jobs from the '{$record->queue}' queue.")
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Job deleted')
                            ->body('The job has been removed from the queue.')
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('process')
                        ->label('Process Selected Queues')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Process Selected Job Queues')
                        ->modalDescription('This will start processing jobs from the selected queues.')
                        ->modalSubmitActionLabel('Start Processing')
                        ->action(function ($records) {
                            $queues = $records->pluck('queue')->unique();

                            foreach ($queues as $queue) {
                                Artisan::call('queue:work', [
                                    '--queue' => $queue,
                                    '--once' => true,
                                ]);
                            }

                            Notification::make()
                                ->success()
                                ->title('Queue processing started')
                                ->body('Processing jobs from ' . $queues->count() . ' queue(s).')
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Jobs deleted')
                                ->body('Selected jobs have been removed from the queue.')
                        ),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('start_worker')
                    ->label('Start Queue Worker')
                    ->icon('heroicon-o-play-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Start Queue Worker')
                    ->modalDescription('This will process all pending jobs in the queue.')
                    ->modalSubmitActionLabel('Start Worker')
                    ->action(function () {
                        Artisan::call('queue:work', [
                            '--once' => true,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Queue worker started')
                            ->body('Processing jobs from all queues.')
                            ->send();
                    }),
                Tables\Actions\Action::make('clear_all')
                    ->label('Clear All Jobs')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Clear All Queue Jobs')
                    ->modalDescription('Are you sure you want to delete all pending jobs? This action cannot be undone.')
                    ->modalSubmitActionLabel('Yes, clear all')
                    ->action(function () {
                        $count = Job::count();
                        Job::truncate();
                        Notification::make()
                            ->success()
                            ->title('Queue cleared')
                            ->body("Deleted {$count} jobs from the queue.")
                            ->send();
                    }),
            ])
            ->emptyStateHeading('No pending jobs')
            ->emptyStateDescription('The queue is empty. Jobs will appear here when they are queued for processing.')
            ->emptyStateIcon('heroicon-o-queue-list');
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
            'index' => Pages\ListJobs::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isSupervisor() ?? false;
    }
}
