<?php

namespace App\Filament\Admin\Resources\EventResource\Pages;

use App\Filament\Admin\Resources\EventResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function afterCreate(): void
    {
        // Log event creation
        AuditLog::log(
            eventType: 'event_created',
            user: auth()->user(),
            properties: [
                'event_id' => $this->record->id,
                'event_name' => $this->record->name,
                'event_type' => $this->record->type,
                'start_date' => $this->record->start_date->toDateTimeString(),
                'status' => $this->record->status,
            ],
            description: auth()->user()->name . ' created event: ' . $this->record->name
        );
    }
}
