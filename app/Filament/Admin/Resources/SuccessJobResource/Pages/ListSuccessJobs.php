<?php

namespace App\Filament\Admin\Resources\SuccessJobResource\Pages;

use App\Filament\Admin\Resources\SuccessJobResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuccessJobs extends ListRecords
{
    protected static string $resource = SuccessJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
