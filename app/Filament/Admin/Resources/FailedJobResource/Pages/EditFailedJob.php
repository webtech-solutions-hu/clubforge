<?php

namespace App\Filament\Admin\Resources\FailedJobResource\Pages;

use App\Filament\Admin\Resources\FailedJobResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFailedJob extends EditRecord
{
    protected static string $resource = FailedJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
