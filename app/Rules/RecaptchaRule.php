<?php

namespace App\Rules;

use App\Services\RecaptchaEnterpriseService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RecaptchaRule implements ValidationRule
{
    protected string $action;
    protected float $minScore;

    /**
     * Create a new rule instance.
     *
     * @param string $action The reCAPTCHA action (e.g., 'LOGIN', 'REGISTER')
     * @param float $minScore Minimum acceptable score for Enterprise (0.0 to 1.0)
     */
    public function __construct(string $action = 'SUBMIT', float $minScore = 0.5)
    {
        $this->action = $action;
        $this->minScore = $minScore;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip validation if reCAPTCHA is disabled
        if (!config('recaptcha.enabled', true)) {
            return;
        }

        if (empty($value)) {
            $fail('Please complete the reCAPTCHA verification.');
            return;
        }

        // Use Enterprise service for validation
        $service = new RecaptchaEnterpriseService();
        $result = $service->createAssessment($value, $this->action, $this->minScore);

        if (!$result['valid']) {
            $fail('reCAPTCHA verification failed. Please try again.');
        }
    }
}
