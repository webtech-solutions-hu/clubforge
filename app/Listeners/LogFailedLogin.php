<?php

namespace App\Listeners;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        // Get user if exists
        $user = null;
        if (isset($event->credentials['email'])) {
            $user = User::where('email', $event->credentials['email'])->first();
        }

        AuditLog::log(
            eventType: 'login_failed',
            user: $user,
            properties: [
                'email' => $event->credentials['email'] ?? null,
            ],
            description: "Failed login attempt" . ($user ? " for user: {$user->name}" : " with email: {$event->credentials['email']}")
        );
    }
}
