<?php

namespace App\Filament\Manager\Resources\ProjectResource\Pages;

use App\Filament\Manager\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function setUp(): void
    {
        parent::setUp();

        session()->forget('current_project_id');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
