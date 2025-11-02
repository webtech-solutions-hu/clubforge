<?php

namespace App\Filament\Admin\Resources\EventResource\Pages;

use App\Filament\Admin\Resources\EventResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->after(function () {
                    // Log event deletion
                    AuditLog::log(
                        eventType: 'event_deleted',
                        user: auth()->user(),
                        properties: [
                            'event_id' => $this->record->id,
                            'event_name' => $this->record->name,
                            'event_type' => $this->record->type,
                        ],
                        description: auth()->user()->name . ' deleted event: ' . $this->record->name
                    );
                }),
        ];
    }

    protected function afterSave(): void
    {
        // Log event modification
        AuditLog::log(
            eventType: 'event_updated',
            user: auth()->user(),
            properties: [
                'event_id' => $this->record->id,
                'event_name' => $this->record->name,
                'event_type' => $this->record->type,
                'status' => $this->record->status,
            ],
            description: auth()->user()->name . ' updated event: ' . $this->record->name
        );
    }
}
