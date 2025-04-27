<?php

namespace App\Filament\Employee\Resources\ProjectEmployeeResource\Pages;

use App\Filament\Employee\Resources\ProjectEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectEmployee extends EditRecord
{
    protected static string $resource = ProjectEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
