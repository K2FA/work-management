<?php

namespace App\Filament\Manager\Resources\TaskResource\Pages;

use App\Filament\Manager\Resources\TaskResource;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->url(fn() => route('filament.manager.resources.tasks.create', ['project_id' => session('current_project_id')]))
        ];
    }
}
