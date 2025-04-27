<?php

namespace App\Filament\Employee\Resources\TaskEmployeeResource\Pages;

use App\Filament\Employee\Resources\TaskEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTaskEmployees extends ListRecords
{
    protected static string $resource = TaskEmployeeResource::class;

    public function getBreadcrumbs(): array
    {
        $projectId = session('current_project_id');
        return [
            route('filament.employee.resources.task-employees.index', ['project_id' => $projectId]) => 'Tasks',
            'List',
        ];
    }
}
