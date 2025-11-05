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
        if (!config('recaptcha.enabled', true)) {
            return TextInput::make('recaptcha_disabled')->hidden();
        }

        $siteKey = config('recaptcha.site_key');

        return TextInput::make('g-recaptcha-response')
            ->label('')
            ->hiddenLabel()
            ->dehydrated()
            ->required()
            ->rules([new RecaptchaRule('LOGIN', 0.5)])
            ->extraInputAttributes([
                'class' => 'g-recaptcha-response',
                'style' => 'display: none;',
            ])
            ->helperText(new HtmlString('
                <script src="https://www.google.com/recaptcha/enterprise.js?render=' . $siteKey . '"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const form = document.querySelector("form");
                        let isSubmitting = false;

                        if (form) {
                            form.addEventListener("submit", async function(e) {
                                if (isSubmitting) {
                                    return true;
                                }

                                const tokenInput = document.querySelector("input.g-recaptcha-response");
                                if (!tokenInput || !tokenInput.value) {
                                    e.preventDefault();

                                    grecaptcha.enterprise.ready(async () => {
                                        try {
                                            const token = await grecaptcha.enterprise.execute("' . $siteKey . '", {action: "LOGIN"});
                                            tokenInput.value = token;
                                            isSubmitting = true;
                                            form.requestSubmit();
                                        } catch (error) {
                                            console.error("reCAPTCHA error:", error);
                                            isSubmitting = false;
                                        }
                                    });
                                }
                            });
                        }
                    });
                </script>
            '))
            ->validationAttribute('reCAPTCHA');
    }
}
