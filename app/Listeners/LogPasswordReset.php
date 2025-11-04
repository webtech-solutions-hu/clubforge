<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\PasswordReset;

class LogPasswordReset
{
    /**
     * Handle the event.
     */
    public function handle(PasswordReset $event): void
    {
        AuditLog::log(
            eventType: 'password_reset',
            user: $event->user,
            description: "User reset their password"
        );
    }
}
