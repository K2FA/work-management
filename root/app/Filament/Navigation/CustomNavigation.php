<?php

namespace App\Filament\Navigation;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;

class CustomNavigation
{
    public static function build(): NavigationBuilder
    {
        return (new NavigationBuilder())
            ->groups([
                NavigationGroup::make()
                    ->label('Main')
                    ->items([
                        NavigationItem::make('Dashboard')
                            ->url(self::getDashboardUrl())
                            ->icon('heroicon-o-home'),
                    ]),

                ...self::customNavigation(),
            ]);
    }

    protected static function customNavigation(): array
    {
        if (Filament::auth()->user()?->hasRole('Employee')) {
            return [
                NavigationGroup::make()
                    ->label('Information')
                    ->items([
                        NavigationItem::make('Projects')
                            ->url(route('filament.employee.resources.project-employees.index'))
                            ->icon('heroicon-o-briefcase'),
                    ]),
            ];
        }

        if (Filament::auth()->user()?->hasRole('Manager')) {
            return [
                NavigationGroup::make()
                    ->label('Management Data')
                    ->items([
                        NavigationItem::make('Projects')
                            ->url(route('filament.manager.resources.projects.index'))
                            ->icon('heroicon-o-briefcase'),
                    ]),
            ];
        }

        return [];
    }

    protected static function getDashboardUrl(): string
    {
        $user = Filament::auth()->user();

        if (!$user) {
            return '/login';
        }

        if ($user->hasRole('Employee')) {
            return route('filament.employee.pages.dashboard');
        } elseif ($user->hasRole('Manager')) {
            return route('filament.manager.pages.dashboard');
        }

        return '/';
    }
}
