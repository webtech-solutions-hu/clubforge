<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Registered;

class LogRegistered
{
    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        AuditLog::log(
            eventType: 'user_registered',
            user: $event->user,
            properties: [
                'email' => $event->user->email,
                'name' => $event->user->name,
            ],
            description: "New user registered: {$event->user->name}"
        );
    }
}
