<?php

namespace App\Filament\Manager\Resources\TaskResource\Pages;

use App\Filament\Manager\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
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
            'Edit Task',
        ];
    }
}
