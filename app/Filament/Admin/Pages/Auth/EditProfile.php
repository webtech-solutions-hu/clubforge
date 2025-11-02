<?php

namespace App\Filament\Admin\Pages\Auth;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Information')
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
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
            ]);
    }
}
