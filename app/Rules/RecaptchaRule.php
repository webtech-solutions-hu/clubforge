<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use ReCaptcha\ReCaptcha;

class RecaptchaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip validation if reCAPTCHA is disabled
        if (!config('services.recaptcha.enabled', true)) {
            return;
        }

        $secretKey = config('services.recaptcha.secret_key');

        if (empty($secretKey)) {
            $fail('reCAPTCHA is not configured properly.');
            return;
        }

        if (empty($value)) {
            $fail('Please complete the reCAPTCHA verification.');
            return;
        }

        $recaptcha = new ReCaptcha($secretKey);
        $response = $recaptcha->verify($value, request()->ip());

        if (!$response->isSuccess()) {
            $fail('reCAPTCHA verification failed. Please try again.');
        }
    }
}
