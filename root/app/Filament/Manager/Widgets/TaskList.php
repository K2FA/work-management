<?php

namespace App\Filament\Manager\Widgets;

use App\Enums\Status;
use App\Filament\Manager\Resources\TaskResource;
use App\Filament\Manager\Resources\TaskResource\Pages\CreateTask;
use App\Filament\Manager\Resources\TaskResource\Pages\EditTask;
use App\Filament\Manager\Resources\TaskResource\Pages\ListTasks;
use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TaskList extends BaseWidget
{
    protected static ?string $heading = '';
    protected int | string | array $columnSpan = 'full';

    protected function getEloquentQuery(): Builder
    {
        return Task::query()
            ->when(session('current_project_id'), function ($query) {
                return $query->where('project_id', session('current_project_id'));
            });
    }

    // #[on('taskDeleted')]
    // public function refreshTableOnDelete(): void
    // {
    //     $this->resetTable();
    // }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getEloquentQuery())
            ->poll("1s")
            ->columns([
                // Column for task name
                TextColumn::make('name')->searchable()->sortable()->label("Task Name"),

                // Column for task description
                TextColumn::make('description')
                    ->searchable()
                    ->label("Task Description")
                    ->wrap()
                    ->limit(null),

                // Column for task status, with color and icon based on the status
                TextColumn::make('status')
                    ->label("Status")
                    ->color(function ($state) {
                        if ($state === Status::todo->value) return 'danger';
                        if ($state === Status::in_progress->value) return 'warning';
                        if ($state === Status::done->value) return 'success';
                        return 'gray';
                    })
                    ->icon(function ($state) {
                        if ($state === Status::todo->value) return 'heroicon-o-document-text';
                        if ($state === Status::in_progress->value) return 'heroicon-o-clock';
                        if ($state === Status::done->value) return 'heroicon-o-check-circle';
                        return 'heroicon-o-question-mark-circle';
                    })
                    ->sortable(),

                // Column for task deadline
                TextColumn::make('deadline')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->label("Deadline"),

                // Column for the assigned employee's name
                TextColumn::make('user.name')
                    ->label('Employee')
                    ->sortable()
                    ->searchable()
            ])
            ->actions([ // Actions available for each row in the table
                EditAction::make()->url(fn(Task $record): string => TaskResource::getUrl('edit', ['record' => $record->getKey()]))->label(''), // Edit task action
                DeleteAction::make() // Delete task action
                    ->action(fn(Task $record) => $record->delete())
                    ->requiresConfirmation()
                    ->label('')
                // ->after(function () {
                //     $this->dispatch('taskDeleted');
                // })
                // ->after(fn() => self::redirectToProjectTasks()) // Redirect after delete action
            ])
            ->bulkActions([ // Actions available for bulk operations
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
