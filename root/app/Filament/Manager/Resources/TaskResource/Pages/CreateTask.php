<?php

namespace App\Filament\Manager\Resources\TaskResource\Pages;

use App\Filament\Manager\Resources\TaskResource;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!isset($data['project_id'])) {
            $data['project_id'] = session('current_project_id');
        }
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', ['project_id' => session('current_project_id')]);
    }

    public function getBreadcrumbs(): array
    {
        $projectId = session('current_project_id');
        return [
            route('filament.manager.resources.tasks.index', ['project_id' => $projectId]) => 'Tasks',
            'Create Task',
        ];
    }
}
