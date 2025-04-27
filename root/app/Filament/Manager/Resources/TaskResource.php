<?php

namespace App\Filament\Manager\Resources;

use App\Enums\Status;
use App\Filament\Manager\Resources\TaskResource\Pages;
use App\Filament\Manager\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms\Components\DateTimePicker;
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
                    ->options([
                        Status::todo->value => Status::todo->value,
                        Status::in_progress->value => Status::in_progress->value,
                        Status::done->value => Status::done->value,
                    ])
                    ->default(Status::todo),
                Textarea::make('description')
                    ->nullable()
                    ->label("Task Description"),
                Hidden::make('project_id')
                    ->default(fn() => request()->query('project_id'))
                    ->required(),
                DateTimePicker::make('deadline')
                    ->label("Task Deadline")
                    ->required()
                    ->minDate(now())
                    ->format('Y-m-d H:i')
                    ->displayFormat('d/m/Y H:i')
                    ->minutesStep(1),
                Select::make('user_id')
                    ->relationship('user', 'name', modifyQueryUsing: fn($query) => $query->role('Employee'))
                    ->label("Employee")
                    ->preload()
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->label("Task Name"),
                TextColumn::make('description')
                    ->searchable()
                    ->sortable()
                    ->label("Task Description")
                    ->wrap()
                    ->limit(null),
                TextColumn::make('status')
                    ->label("Status")
                    ->color(fn($state) => match ($state) {
                        Status::todo->value => 'info',
                        Status::in_progress->value => 'warning',
                        Status::done->value => 'success',
                    })
                    ->icon(fn($state) => match ($state) {
                        Status::todo->value => 'heroicon-o-document-text',
                        Status::in_progress->value => 'heroicon-o-clock',
                        Status::done->value => 'heroicon-o-check-circle',
                    })
                    ->sortable(),
                TextColumn::make('deadline')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->label("Deadline"),

                TextColumn::make('user.name')
                    ->label('Employee')
                    ->sortable()
                    ->searchable()
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
            ]);
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
