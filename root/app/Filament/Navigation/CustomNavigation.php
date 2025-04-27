<?php

namespace App\Filament\Navigation;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;

class CustomNavigation
{
    /**
     * Build the custom navigation for the Filament panel.
     *
     * This method constructs the navigation menu for the Filament admin panel,
     * dynamically adding menu items based on the user's role (Employee or Manager).
     * It also sets up the main dashboard item, pointing to the correct dashboard
     * URL based on the user's role.
     *
     * @return NavigationBuilder
     */
    public static function build(): NavigationBuilder
    {
        return (new NavigationBuilder())
            ->groups([ // Start of the navigation groups
                // Main navigation group containing the dashboard item
                NavigationGroup::make()
                    ->label('Main') // Label for the group
                    ->items([ // Items inside the 'Main' group
                        NavigationItem::make('Dashboard') // Dashboard item
                            ->url(self::getDashboardUrl()) // URL of the dashboard, dynamically fetched based on user role
                            ->icon('heroicon-o-home'), // Icon for the dashboard item
                    ]),

                ...self::customNavigation(), // Append custom navigation based on roles
            ]);
    }

    /**
     * Build custom navigation items based on the user's role.
     *
     * This method checks the user's role (Employee or Manager) and dynamically
     * adds specific navigation items for each role.
     *
     * @return array An array of NavigationGroup objects containing the custom items
     */
    protected static function customNavigation(): array
    {
        // If the user has the 'Employee' role, provide employee-specific navigation
        if (Filament::auth()->user()?->hasRole('Employee')) {
            return [
                NavigationGroup::make()
                    ->label('Information') // Label for the group
                    ->items([ // Items inside the 'Information' group
                        NavigationItem::make('Projects') // Projects item
                            ->url(route('filament.employee.resources.project-employees.index')) // URL for the projects
                            ->icon('heroicon-o-briefcase'), // Icon for the projects item
                    ]),
            ];
        }

        // If the user has the 'Manager' role, provide manager-specific navigation
        if (Filament::auth()->user()?->hasRole('Manager')) {
            return [
                NavigationGroup::make()
                    ->label('Management Data') // Label for the group
                    ->items([ // Items inside the 'Management Data' group
                        NavigationItem::make('Projects') // Projects item
                            ->url(route('filament.manager.resources.projects.index')) // URL for the projects
                            ->icon('heroicon-o-briefcase'), // Icon for the projects item
                    ]),
            ];
        }

        // Return an empty array if no specific role was matched
        return [];
    }

    /**
     * Get the dashboard URL based on the user's role.
     *
     * This method checks if the user is logged in and determines which dashboard
     * URL to return based on the user's role. If the user is an Employee, they
     * will be redirected to the employee dashboard. If the user is a Manager,
     * they will be redirected to the manager dashboard. If the user is not logged
     * in or does not match any role, they will be redirected to the login page or
     * the root URL.
     *
     * @return string The URL of the dashboard or fallback URL
     */
    protected static function getDashboardUrl(): string
    {
        // Get the currently authenticated user
        $user = Filament::auth()->user();

        // If no user is authenticated, redirect to the login page
        if (!$user) {
            return '/login';
        }

        // Check the role of the authenticated user and return the appropriate dashboard URL
        if ($user->hasRole('Employee')) {
            return route('filament.employee.pages.dashboard'); // Employee dashboard route
        } elseif ($user->hasRole('Manager')) {
            return route('filament.manager.pages.dashboard'); // Manager dashboard route
        }

        // Fallback to the root URL if no matching role is found
        return '/';
    }
}
