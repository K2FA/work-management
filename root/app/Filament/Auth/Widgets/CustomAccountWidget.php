<?php

namespace App\Filament\Auth\Widgets;

use Filament\Widgets\AccountWidget as BaseAccountWidget;
use Filament\Actions\Action;

class CustomAccountWidget extends BaseAccountWidget
{
    protected function getLogoutFormAction(): Action
    {
        $panelId = filament()->getCurrentPanel()->getId();

        $logoutRouteName = match ($panelId) {
            'admin' => 'filament.admin.auth.logout',
            'manager' => 'filament.manager.auth.logout',
            'employee' => 'filament.employee.auth.logout',
            default => 'filament.auth.auth.logout',
        };

        return Action::make('logout')
            ->label(__('filament::widgets/account-widget.actions.logout.label'))
            ->url(route($logoutRouteName))
            ->color('gray')
            ->icon('heroicon-o-arrow-left-on-rectangle')
            ->openUrlInNewTab(false);
    }
}
