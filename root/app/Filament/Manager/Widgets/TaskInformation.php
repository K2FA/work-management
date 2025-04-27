<?php

namespace App\Filament\Manager\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TaskInformation extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    // Filament v3.7+ akan membaca properti ini
    protected static string $recordKeyColumnName = 'record_key';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->selectRaw('MIN(id) AS id, project_id, user_id')
                    ->with(['project', 'user'])
                    ->groupBy('project_id', 'user_id')
            )
            ->columns([
                TextColumn::make('project.name')->label('Project')->searchable()->sortable(),
                TextColumn::make('user.name')->label('Employee')->searchable()->sortable(),
                TextColumn::make('todo')
                    ->label('To Do')
                    ->state(function ($record) {
                        return Task::where('project_id', $record->project_id)
                            ->where('user_id', $record->user_id)
                            ->where('status', 'To do')
                            ->count();
                    }),
                TextColumn::make('in_progress')
                    ->label('In Progress')
                    ->state(function ($record) {
                        return Task::where('project_id', $record->project_id)
                            ->where('user_id', $record->user_id)
                            ->where('status', 'In progress')
                            ->count();
                    }),
                TextColumn::make('done')
                    ->label('Done')
                    ->state(function ($record) {
                        return Task::where('project_id', $record->project_id)
                            ->where('user_id', $record->user_id)
                            ->where('status', 'Done')
                            ->count();
                    }),
            ]);
    }

    // protected function getTableRecordKeyColumnName(): string
    // {
    //     return 'record_key';
    // }
}
