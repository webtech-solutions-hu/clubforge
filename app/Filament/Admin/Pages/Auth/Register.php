<?php

namespace App\Filament\Admin\Pages\Auth;

use App\Models\Role;
use App\Rules\RecaptchaRule;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getRecaptchaFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getRecaptchaFormComponent(): Component
    {
        if (!config('recaptcha.enabled', true)) {
            return TextInput::make('recaptcha_disabled')->hidden();
        }

        $siteKey = config('recaptcha.site_key');

        return TextInput::make('g-recaptcha-response')
            ->label('')
            ->dehydrated()
            ->required()
            ->rules([new RecaptchaRule('REGISTER', 0.5)])
            ->extraInputAttributes([
                'class' => 'g-recaptcha-response',
            ])
            ->helperText(new HtmlString('
                <script src="https://www.google.com/recaptcha/enterprise.js?render=' . $siteKey . '"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const form = document.querySelector("form");
                        if (form) {
                            form.addEventListener("submit", async function(e) {
                                e.preventDefault();

                                grecaptcha.enterprise.ready(async () => {
                                    try {
                                        const token = await grecaptcha.enterprise.execute("' . $siteKey . '", {action: "REGISTER"});
                                        document.querySelector("input.g-recaptcha-response").value = token;
                                        form.submit();
                                    } catch (error) {
                                        console.error("reCAPTCHA error:", error);
                                    }
                                });
                            });
                        }
                    });
                </script>
            '))
            ->validationAttribute('reCAPTCHA');
    }

    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration($data);

        // Assign Guest role to new users
        $guestRole = Role::where('slug', 'guest')->first();
        if ($guestRole) {
            $user->roles()->attach($guestRole);
        }

        return $user;
    }
}
