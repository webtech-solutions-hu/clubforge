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
            ->rules([new RecaptchaRule('LOGIN', 0.5)])
            ->extraInputAttributes([
                'class' => 'g-recaptcha-response hidden',
                'style' => 'display: none !important; visibility: hidden !important; position: absolute !important;',
                'tabindex' => '-1',
                'aria-hidden' => 'true',
            ])
            ->helperText(new HtmlString('
                <script src="https://www.google.com/recaptcha/enterprise.js?render=' . $siteKey . '"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        async function generateRecaptchaToken() {
                            if (typeof grecaptcha === "undefined" || !grecaptcha.enterprise) {
                                setTimeout(generateRecaptchaToken, 100);
                                return;
                            }

                            grecaptcha.enterprise.ready(async () => {
                                try {
                                    const token = await grecaptcha.enterprise.execute("' . $siteKey . '", {action: "LOGIN"});
                                    const tokenInput = document.querySelector("input.g-recaptcha-response");
                                    if (tokenInput) {
                                        tokenInput.value = token;
                                    }
                                } catch (error) {
                                    console.error("reCAPTCHA error:", error);
                                }
                            });
                        }

                        // Generate token when page loads
                        generateRecaptchaToken();

                        // Regenerate token every 90 seconds (tokens expire after 2 minutes)
                        setInterval(generateRecaptchaToken, 90000);
                    });
                </script>
            '))
            ->validationAttribute('reCAPTCHA');
    }
}
