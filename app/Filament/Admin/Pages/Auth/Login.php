<?php

namespace App\Filament\Admin\Pages\Auth;

use App\Rules\RecaptchaRule;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                        $this->getRecaptchaFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getRecaptchaFormComponent(): Component
    {
        if (!config('services.recaptcha.enabled', true)) {
            return TextInput::make('recaptcha_disabled')->hidden();
        }

        return TextInput::make('g-recaptcha-response')
            ->label('')
            ->dehydrated()
            ->required()
            ->rules([new RecaptchaRule()])
            ->extraInputAttributes([
                'class' => 'g-recaptcha-response',
            ])
            ->helperText(new HtmlString('
                <div class="g-recaptcha"
                     data-sitekey="' . config('services.recaptcha.site_key') . '"
                     data-callback="onRecaptchaSuccess">
                </div>
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                <script>
                    function onRecaptchaSuccess(token) {
                        document.querySelector(\'input.g-recaptcha-response\').value = token;
                    }
                </script>
            '))
            ->validationAttribute('reCAPTCHA');
    }
}
