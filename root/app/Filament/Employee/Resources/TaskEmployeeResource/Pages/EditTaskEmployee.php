<?php

namespace App\Filament\Employee\Resources\TaskEmployeeResource\Pages;

use App\Filament\Employee\Resources\TaskEmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaskEmployee extends EditRecord
{
    protected static string $resource = TaskEmployeeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', ['project_id' => session('current_project_id')]);
    }

    public function getBreadcrumbs(): array
    {
        $projectId = session('current_project_id');
        return [
            route('filament.employee.resources.task-employees.index', ['project_id' => $projectId]) => 'Tasks',
            'Edit Task',
        ];
    }
}
