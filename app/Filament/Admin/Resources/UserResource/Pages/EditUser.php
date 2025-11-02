<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Models\AuditLog;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store old roles before save
        $this->oldRoles = $this->record->roles()->pluck('name')->toArray();
        return $data;
    }

    protected function afterSave(): void
    {
        // Check if roles were changed
        $newRoles = $this->record->fresh()->roles()->pluck('name')->toArray();

        if (isset($this->oldRoles)) {
            $addedRoles = array_diff($newRoles, $this->oldRoles);
            $removedRoles = array_diff($this->oldRoles, $newRoles);

            if (!empty($addedRoles) || !empty($removedRoles)) {
                AuditLog::log(
                    eventType: 'roles_changed',
                    user: $this->record,
                    properties: [
                        'old_roles' => $this->oldRoles,
                        'new_roles' => $newRoles,
                        'added' => array_values($addedRoles),
                        'removed' => array_values($removedRoles),
                    ],
                    description: "User roles were modified by " . auth()->user()->name
                );
            }
        }
    }
}
