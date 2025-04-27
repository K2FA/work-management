<?php

namespace App\Filament\Employee\Resources\ProjectEmployeeResource\Pages;

use App\Filament\Employee\Resources\ProjectEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectEmployees extends ListRecords
{
    protected static string $resource = ProjectEmployeeResource::class;
}
