<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        AuditLog::log(
            eventType: 'logout',
            user: $event->user,
            description: "User logged out successfully"
        );
    }
}
