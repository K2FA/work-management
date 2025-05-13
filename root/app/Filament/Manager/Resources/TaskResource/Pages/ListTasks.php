<?php

namespace App\Filament\Manager\Resources\TaskResource\Pages;

use App\Filament\Manager\Pages\TaskTotal;
use App\Filament\Manager\Resources\TaskResource;
use App\Filament\Manager\Widgets\TaskDuplicateTable;
use App\Filament\Manager\Widgets\TaskList;
use App\Models\Task;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    #[on('taskDeleted')]
    public function refreshTableOnDelete(): void
    {
        $this->dispatch('refreshTable');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon('heroicon-o-plus')
                ->url(fn() => route('filament.manager.resources.tasks.create', ['project_id' => session('current_project_id')]))
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TaskTotal::class
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [TaskDuplicateTable::class];
    }


    public function getBreadcrumbs(): array
    {
        $projectId = session('current_project_id');
        return [
            route('filament.manager.resources.tasks.index', ['project_id' => $projectId]) => 'Tasks',
            'List',
        ];
    }
}
