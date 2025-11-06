<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LicenseResource\Pages;
use App\Models\License;
use App\Services\SubscriptionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'System Settings';

    protected static ?string $navigationLabel = 'License';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('License Information')
                    ->schema([
                        Forms\Components\TextInput::make('license_key')
                            ->label('License Key')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('XXXX-XXXX-XXXX-XXXX'),
                        Forms\Components\Select::make('tier')
                            ->label('Subscription Tier')
                            ->required()
                            ->options([
                                'starter' => 'Starter (Free)',
                                'pro' => 'Pro ($29/mo)',
                                'club_plus' => 'Club+ ($79/mo)',
                                'enterprise' => 'Enterprise ($199/mo)',
                            ])
                            ->default('starter'),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'expired' => 'Expired',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active'),
                        Forms\Components\DateTimePicker::make('activated_at')
                            ->label('Activated At')
                            ->default(now()),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->nullable()
                            ->helperText('Leave empty for perpetual license'),
                    ])->columns(2),

                Forms\Components\Section::make('Usage Statistics')
                    ->schema([
                        Forms\Components\Placeholder::make('current_users')
                            ->label('Current Users')
                            ->content(fn (?License $record) => $record ? number_format($record->current_users) : '0'),
                        Forms\Components\Placeholder::make('current_storage_mb')
                            ->label('Current Storage')
                            ->content(fn (?License $record) => $record ? round($record->current_storage_mb / 1024, 2) . ' GB' : '0 GB'),
                    ])->columns(2)
                    ->hidden(fn ($context) => $context === 'create'),

                Forms\Components\Section::make('Metadata')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Additional Information')
                            ->nullable(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tier')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => config("subscription.tiers.{$state}.name", ucfirst($state)))
                    ->color(fn (string $state): string => match($state) {
                        'starter' => 'gray',
                        'pro' => 'info',
                        'club_plus' => 'warning',
                        'enterprise' => 'success',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('license_key')
                    ->label('License Key')
                    ->limit(20)
                    ->copyable()
                    ->copyMessage('License key copied')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'expired' => 'danger',
                        'suspended' => 'warning',
                        'inactive' => 'gray',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('current_users')
                    ->label('Users')
                    ->formatStateUsing(fn (License $record) =>
                        $record->max_users
                            ? "{$record->current_users} / {$record->max_users}"
                            : "{$record->current_users} / ∞"
                    )
                    ->badge()
                    ->color(fn (License $record) =>
                        $record->max_users && $record->users_used_percentage > 80
                            ? 'danger'
                            : ($record->users_used_percentage > 50 ? 'warning' : 'success')
                    ),
                Tables\Columns\TextColumn::make('activated_at')
                    ->dateTime()
                    ->since(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->placeholder('Never')
                    ->color(fn (?string $state) => $state && now()->parse($state)->isPast() ? 'danger' : 'success'),
            ])
            ->defaultSort('activated_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'expired' => 'Expired',
                        'suspended' => 'Suspended',
                    ]),
                Tables\Filters\SelectFilter::make('tier')
                    ->options([
                        'starter' => 'Starter',
                        'pro' => 'Pro',
                        'club_plus' => 'Club+',
                        'enterprise' => 'Enterprise',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (License $record) => $record->status !== 'active')
                    ->requiresConfirmation()
                    ->modalHeading('Activate License')
                    ->modalDescription('This will deactivate any other active license and activate this one.')
                    ->action(function (License $record) {
                        License::where('status', 'active')->update(['status' => 'inactive']);
                        $record->update(['status' => 'active', 'activated_at' => now()]);
                        SubscriptionService::updateUsage();
                        Notification::make()
                            ->success()
                            ->title('License activated')
                            ->body('The license has been activated successfully.')
                            ->send();
                    }),
                Tables\Actions\Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (License $record) => $record->status === 'active')
                    ->requiresConfirmation()
                    ->action(function (License $record) {
                        $record->update(['status' => 'inactive']);
                        Notification::make()
                            ->warning()
                            ->title('License deactivated')
                            ->body('The license has been deactivated.')
                            ->send();
                    }),
                Tables\Actions\Action::make('update_usage')
                    ->label('Update Usage')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function (License $record) {
                        $record->updateUserCount();
                        $record->updateStorageUsage();
                        Notification::make()
                            ->success()
                            ->title('Usage updated')
                            ->body('License usage statistics have been recalculated.')
                            ->send();
                    }),
                Tables\Actions\ViewAction::make()
                    ->slideOver(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No license activated')
            ->emptyStateDescription('Activate a license to unlock premium features.')
            ->emptyStateIcon('heroicon-o-key');
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
            'index' => Pages\ListLicenses::route('/'),
            'create' => Pages\CreateLicense::route('/create'),
            'edit' => Pages\EditLicense::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isSupervisor() ?? false;
    }
}
