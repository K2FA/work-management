<?php

namespace App\Filament\Manager\Resources;

use App\Enums\Status;
use App\Filament\Manager\Resources\TaskResource\Pages;
use App\Filament\Manager\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label("Task Name"),
                Select::make('status')
                    ->label("Task Status")
                    ->options(Status::values())
                    ->default(Status::todo),
                Textarea::make('description')
                    ->nullable()
                    ->label("Task Description"),
                Hidden::make('project_id')
                    ->default(fn() => request()->query('project_id'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->label("Task Name"),
                TextColumn::make('status')
                    ->label("Status")
                    // ->enum(Status::values())
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->sortable()
                    ->label("Task Description")
                    ->wrap()
                    ->limit(null),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()->label(''),
                DeleteAction::make()
                    ->label('')
                    ->after(fn() => self::redirectToProjectTasks())
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->url(fn() => route('filament.manager.resources.tasks.create', ['project_id' => session('current_project_id')]))
                    ->visible(fn() => false)
            ]);;
    }
    private static function redirectToProjectTasks()
    {
        $projectId = session('current_project_id');
        if ($projectId) {
            return redirect()->to(route('filament.manager.resources.tasks.index', ['project_id' => $projectId]));
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }


    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if ($projectId = request()->query('project_id')) {
            session(['current_project_id' => $projectId]);
        }

        if ($projectId = session('current_project_id')) {
            $query->where('project_id', $projectId);
        }

        return $query;
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
