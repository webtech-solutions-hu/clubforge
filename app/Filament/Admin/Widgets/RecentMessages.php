<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Post;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentMessages extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Post::query()
                    ->whereNull('event_id')
                    ->with(['user', 'likes', 'comments'])
                    ->withCount(['likes', 'comments'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('user.avatar')
                    ->label('Author')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png'))
                    ->size(40),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('title')
                    ->limit(30)
                    ->placeholder('No title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('content')
                    ->limit(60)
                    ->wrap(),
                Tables\Columns\TextColumn::make('comments_count')
                    ->label('Comments')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('likes_count')
                    ->label('Likes')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->sortable(),
            ])
            ->heading('Recent Messages')
            ->emptyStateHeading('No Messages Yet')
            ->emptyStateDescription('The message board is empty.')
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right');
    }
}
