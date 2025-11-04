<?php

namespace App\Filament\Admin\Pages;

use App\Models\Message;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Infolists;
use Illuminate\Contracts\View\View;

class Messages extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static string $view = 'filament.admin.pages.messages';

    protected static ?string $navigationGroup = 'My Club';

    protected static ?string $navigationLabel = 'Messages';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'my-messages';

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if (!$user) {
            return null;
        }

        $unreadCount = Message::visibleToUser($user)
            ->unread()
            ->count();

        return $unreadCount > 0 ? (string) $unreadCount : null;
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->query(Message::query()->visibleToUser($user))
            ->columns([
                Tables\Columns\IconColumn::make('read_at')
                    ->label('')
                    ->state(fn ($record) => $record->isRead())
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('gray')
                    ->falseColor('primary')
                    ->tooltip(fn ($record) => $record->isUnread() ? 'Unread' : 'Read')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sender.name')
                    ->label('From')
                    ->default('System')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50)
                    ->weight(fn ($record) => $record->isUnread() ? 'bold' : 'normal'),

                Tables\Columns\TextColumn::make('recipient_type')
                    ->badge()
                    ->label('Type')
                    ->color(fn (string $state): string => match ($state) {
                        'global' => 'success',
                        'user' => 'info',
                        'role' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'global' => 'Everyone',
                        'user' => 'Direct',
                        'role' => 'Role',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'normal' => 'info',
                        'high' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('read_at')
                    ->label('Read Status')
                    ->nullable()
                    ->placeholder('All')
                    ->trueLabel('Read')
                    ->falseLabel('Unread'),

                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                    ]),

                Tables\Filters\SelectFilter::make('recipient_type')
                    ->label('Message Type')
                    ->options([
                        'global' => 'Everyone',
                        'user' => 'Direct',
                        'role' => 'Role',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->slideOver()
                    ->modalHeading(fn ($record) => $record->title)
                    ->infolist([
                        Infolists\Components\Section::make()
                            ->schema([
                                Infolists\Components\TextEntry::make('sender.name')
                                    ->label('From')
                                    ->default('System'),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('Received')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('priority')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'low' => 'gray',
                                        'normal' => 'info',
                                        'high' => 'danger',
                                        default => 'gray',
                                    }),
                                Infolists\Components\TextEntry::make('category')
                                    ->badge()
                                    ->color('primary'),
                            ])
                            ->columns(2),
                        Infolists\Components\Section::make('Message')
                            ->schema([
                                Infolists\Components\TextEntry::make('message')
                                    ->label('')
                                    ->markdown()
                                    ->columnSpanFull(),
                            ]),
                        Infolists\Components\Section::make('Action')
                            ->schema([
                                Infolists\Components\TextEntry::make('action_url')
                                    ->label('Link')
                                    ->url(fn ($record) => $record->action_url)
                                    ->openUrlInNewTab()
                                    ->placeholder('No action link'),
                            ])
                            ->visible(fn ($record) => $record->action_url !== null),
                    ])
                    ->after(fn ($record) => $record->markAsRead()),

                Tables\Actions\Action::make('markAsRead')
                    ->label('')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->isUnread())
                    ->action(fn ($record) => $record->markAsRead())
                    ->requiresConfirmation(false),

                Tables\Actions\Action::make('markAsUnread')
                    ->label('')
                    ->icon('heroicon-o-x-mark')
                    ->color('warning')
                    ->visible(fn ($record) => $record->isRead())
                    ->action(fn ($record) => $record->markAsUnread())
                    ->requiresConfirmation(false),

                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->visible(fn ($record) => $record->recipient_type === 'user')
                    ->modalDescription('Are you sure you want to delete this message? This action cannot be undone.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('markAsRead')
                    ->label('Mark as Read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(fn ($records) => $records->each->markAsRead()),

                Tables\Actions\BulkAction::make('markAsUnread')
                    ->label('Mark as Unread')
                    ->icon('heroicon-o-x-mark')
                    ->color('warning')
                    ->action(fn ($records) => $records->each->markAsUnread()),

                Tables\Actions\DeleteBulkAction::make()
                    ->action(function ($records) {
                        // Only delete direct messages (recipient_type = 'user')
                        $records->each(function ($record) {
                            if ($record->recipient_type === 'user') {
                                $record->delete();
                            }
                        });
                    })
                    ->deselectRecordsAfterCompletion()
                    ->modalDescription('Only direct messages will be deleted. Global and role-based messages cannot be deleted.'),
            ])
            ->emptyStateHeading('No messages yet')
            ->emptyStateDescription('You will see messages from administrators and owners here.')
            ->emptyStateIcon('heroicon-o-envelope');
    }
}
