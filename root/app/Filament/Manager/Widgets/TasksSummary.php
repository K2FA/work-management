<?php

namespace App\Filament\Manager\Widgets;

use App\Enums\Status;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TasksSummary extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Stat::make('Total Project', Project::count())
            //     ->description('Total number of Projects')
            //     ->descriptionIcon('heroicon-o-document-text')
            //     ->color('info'),

            // Stat::make('Total Tasks', Task::count())
            //     ->description('Total number of tasks')
            //     ->descriptionIcon('heroicon-o-document-duplicate')
            //     ->color('warning'),

            Stat::make('To Do', Task::where('status', Status::todo->value)->count())
                ->description('Total number of tasks to do')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('info'),

            Stat::make('In Progress', Task::where('status', Status::in_progress->value)->count())
                ->description('Total number of tasks in progress')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Done', Task::where('status', Status::done->value)->count())
                ->description('Total number of tasks done')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            // Stat::make('Total Employee', User::whereHas('roles', function ($query) {
            //     $query->where('name', 'Employee');
            // })->count())
            //     ->description('Total number of employees')
            //     ->descriptionIcon('heroicon-o-users')
            //     ->color('success`'),
        ];
    }
}
