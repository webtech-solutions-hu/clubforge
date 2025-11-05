<?php

namespace App\Filament\Admin\Pages\Auth;

use App\Rules\RecaptchaRule;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Illuminate\Support\HtmlString;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent(),
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
            ->rules([new RecaptchaRule('PASSWORD_RESET', 0.5)])
            ->extraInputAttributes([
                'class' => 'g-recaptcha-response hidden',
                'style' => 'display: none !important; visibility: hidden !important; position: absolute !important;',
                'tabindex' => '-1',
                'aria-hidden' => 'true',
            ])
            ->helperText(new HtmlString('
                <div id="recaptcha-container"></div>
                <script>
                    (function() {
                        let isLoaded = false;
                        let isSubmitting = false;

                        function loadRecaptcha() {
                            if (isLoaded) return;
                            isLoaded = true;

                            const script = document.createElement("script");
                            script.src = "https://www.google.com/recaptcha/enterprise.js?render=' . $siteKey . '";
                            script.async = true;
                            script.defer = true;
                            document.head.appendChild(script);
                        }

                        if (document.readyState === "loading") {
                            document.addEventListener("DOMContentLoaded", loadRecaptcha);
                        } else {
                            loadRecaptcha();
                        }

                        window.addEventListener("load", function() {
                            const form = document.querySelector("form");
                            if (!form) return;

                            form.addEventListener("submit", async function(e) {
                                if (isSubmitting) return true;

                                const tokenInput = document.querySelector("input.g-recaptcha-response");
                                if (!tokenInput || tokenInput.value) return true;

                                e.preventDefault();
                                e.stopPropagation();

                                if (typeof grecaptcha === "undefined" || !grecaptcha.enterprise) {
                                    console.error("reCAPTCHA not loaded");
                                    return false;
                                }

                                grecaptcha.enterprise.ready(async () => {
                                    try {
                                        const token = await grecaptcha.enterprise.execute("' . $siteKey . '", {action: "PASSWORD_RESET"});
                                        tokenInput.value = token;
                                        isSubmitting = true;

                                        // Use native form submission
                                        HTMLFormElement.prototype.submit.call(form);
                                    } catch (error) {
                                        console.error("reCAPTCHA error:", error);
                                        isSubmitting = false;
                                    }
                                });
                            });
                        });
                    })();
                </script>
            '))
            ->validationAttribute('reCAPTCHA');
    }
}
