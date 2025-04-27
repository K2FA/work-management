<?php

namespace App\Filament\Employee\Resources\TaskEmployeeResource\Pages;

use App\Filament\Employee\Resources\TaskEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTaskEmployee extends CreateRecord
{
    protected static string $resource = TaskEmployeeResource::class;
}
