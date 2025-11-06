<?php

namespace App\Filament\Admin\Resources\FailedJobResource\Pages;

use App\Filament\Admin\Resources\FailedJobResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFailedJob extends CreateRecord
{
    protected static string $resource = FailedJobResource::class;
}
