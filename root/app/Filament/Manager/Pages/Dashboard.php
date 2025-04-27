<?php

namespace App\Filament\Manager\Pages;

use App\Filament\Manager\Widgets\ChartTask;
use App\Filament\Manager\Widgets\TaskInformation;
use Filament\Pages\Dashboard as BaseDashboard;

use App\Filament\Manager\Widgets\TasksSummary;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            TasksSummary::class,
            TaskInformation::class,
        ];
    }
}
