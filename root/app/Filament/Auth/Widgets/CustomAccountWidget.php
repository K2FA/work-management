<?php

namespace App\Filament\Auth\Widgets;

use Filament\Widgets\AccountWidget as BaseAccountWidget;
use Filament\Actions\Action;

/**
 * Custom Account Widget for Filament.
 *
 * Overrides the default logout behavior to handle
 * different logout routes based on the current panel.
 */
class CustomAccountWidget extends BaseAccountWidget
{
    /**
     * Customize the logout action based on the user's current panel.
     *
     * @return Action
     */
    protected function getLogoutFormAction(): Action
    {
        // Get the ID of the current active panel (admin, manager, or employee)
        $panelId = filament()->getCurrentPanel()->getId();

        // Determine the appropriate logout route name based on the panel
        $logoutRouteName = match ($panelId) {
            'admin' => 'filament.admin.auth.logout',
            'manager' => 'filament.manager.auth.logout',
            'employee' => 'filament.employee.auth.logout',
            default => 'filament.auth.auth.logout', // fallback logout route
        };

        // Return the customized logout action
        return Action::make('logout')
            ->label(__('filament::widgets/account-widget.actions.logout.label')) // localized logout label
            ->url(route($logoutRouteName)) // logout URL
            ->color('gray') // button color
            ->icon('heroicon-o-arrow-left-on-rectangle') // logout icon
            ->openUrlInNewTab(false); // open in same tab
    }
}
