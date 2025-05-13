<?php

namespace App\Filament\Manager\Pages;

use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TaskTotal extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('', Task::count())
                ->label('Total Tasks')
        ];
    }
}
