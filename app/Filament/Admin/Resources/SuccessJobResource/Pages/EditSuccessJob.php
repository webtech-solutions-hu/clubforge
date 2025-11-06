<?php

namespace App\Filament\Admin\Resources\SuccessJobResource\Pages;

use App\Filament\Admin\Resources\SuccessJobResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuccessJob extends EditRecord
{
    protected static string $resource = SuccessJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
