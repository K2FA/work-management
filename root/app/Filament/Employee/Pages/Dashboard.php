<?php

namespace App\Filament\Employee\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

use App\Filament\Manager\Widgets\TasksSummary;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            TasksSummary::class,
        ];
    }
}
