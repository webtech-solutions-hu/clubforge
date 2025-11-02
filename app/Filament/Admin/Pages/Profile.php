<?php

namespace App\Filament\Admin\Pages;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'My Data';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.admin.pages.profile';

    protected static ?string $title = 'My Profile';

    protected static ?string $slug = 'profile';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        // Allow all authenticated users to access their profile
        return true;
    }

    public function mount(): void
    {
        $this->form->fill(auth()->user()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('mobile')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

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
                                    ->required(),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL')
                                    ->url()
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['platform'] ?? null)
                            ->addActionLabel('Add Social Link'),
                    ]),

                Forms\Components\Section::make('About')
                    ->schema([
                        Forms\Components\Textarea::make('bio')
                            ->label('Biography')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Change Password')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->password()
                            ->label('Current Password')
                            ->required()
                            ->currentPassword(),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->label('New Password')
                            ->confirmed()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->password()
                            ->label('Confirm New Password')
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = auth()->user();

        // Update user data
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'] ?? null,
            'city' => $data['city'] ?? null,
            'address' => $data['address'] ?? null,
            'social_media_links' => $data['social_media_links'] ?? null,
            'bio' => $data['bio'] ?? null,
        ];

        // Update password if provided
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        Notification::make()
            ->title('Profile updated successfully')
            ->success()
            ->send();

        // Refresh form with updated data
        $this->form->fill($user->fresh()->toArray());
    }
}
