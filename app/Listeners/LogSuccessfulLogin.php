<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        AuditLog::log(
            eventType: 'login',
            user: $event->user,
            description: "User logged in successfully"
        );
    }
}
