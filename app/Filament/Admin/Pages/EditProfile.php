<?php

namespace App\Filament\Admin\Pages;

use App\Models\AuditLog;
use App\Services\MessageService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = null;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.admin.pages.edit-profile';

    protected static ?string $title = 'Edit Profile';

    protected static ?string $slug = 'edit-profile';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        // Allow all authenticated users to access their profile edit page
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
                        Forms\Components\FileUpload::make('avatar')
                            ->image()
                            ->directory('avatars')
                            ->disk('public')
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->columnSpanFull(),
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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_profile')
                ->label('View Profile')
                ->icon('heroicon-o-eye')
                ->url(route('filament.admin.pages.profile'))
                ->color('gray'),
        ];
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
        $passwordChanged = false;

        // Track what fields changed
        $changedFields = [];
        $originalData = $user->only(['name', 'email', 'avatar', 'mobile', 'city', 'address', 'social_media_links', 'bio']);

        // Update user data
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'avatar' => $data['avatar'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'city' => $data['city'] ?? null,
            'address' => $data['address'] ?? null,
            'social_media_links' => $data['social_media_links'] ?? null,
            'bio' => $data['bio'] ?? null,
        ];

        // Track changed fields
        foreach ($updateData as $field => $value) {
            if ($originalData[$field] != $value) {
                $changedFields[] = $field;
            }
        }

        // Update password if provided
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
            $passwordChanged = true;
        }

        $user->update($updateData);

        // Log profile update
        if (!empty($changedFields) || $passwordChanged) {
            AuditLog::log(
                eventType: 'profile_updated',
                user: $user,
                properties: [
                    'changed_fields' => $changedFields,
                    'password_changed' => $passwordChanged,
                ],
                description: "User updated their profile" . ($passwordChanged ? " (including password)" : "")
            );
        }

        // Log password change separately for better tracking
        if ($passwordChanged) {
            AuditLog::log(
                eventType: 'password_changed',
                user: $user,
                description: "User changed their password"
            );
        }

        // Create notification in database
        MessageService::profileUpdated($user);

        // Dispatch event to refresh notification bell
        $this->dispatch('notification-created');

        // Refresh form with updated data
        $this->form->fill($user->fresh()->toArray());

        // Redirect to profile page
        $this->redirect(route('filament.admin.pages.profile'));
    }
}
