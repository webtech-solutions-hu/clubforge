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
        $isEnterprise = config('recaptcha.enterprise_enabled', false);

        if ($isEnterprise) {
            // reCAPTCHA Enterprise v3 (invisible)
            return TextInput::make('g-recaptcha-response')
                ->label('')
                ->dehydrated()
                ->required()
                ->rules([new RecaptchaRule('LOGIN', 0.5)])
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
                                            const token = await grecaptcha.enterprise.execute("' . $siteKey . '", {action: "LOGIN"});
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
        } else {
            // reCAPTCHA v2 (checkbox)
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
                         data-sitekey="' . $siteKey . '"
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
}
