<?php

namespace App\Filament\Manager\Resources;

use App\Enums\Status;
use App\Filament\Manager\Resources\TaskResource\Pages;
use App\Filament\Manager\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Facades\Filament;
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
    /**
     * The model associated with the resource.
     *
     * @var string|null
     */
    protected static ?string $model = Task::class;

    protected static ?string $heading = 'Task Todo';

    /**
     * Define the form schema for creating or editing a task.
     *
     * This method defines the fields and layout for creating or editing tasks,
     * such as the task's name, description, status, deadline, and the associated employee.
     *
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Field for the task name (required)
                TextInput::make('name')
                    ->required()
                    ->label("Task Name"),

                // Field for selecting the task's status with predefined options
                Select::make('status')
                    ->label("Task Status")
                    ->options([
                        Status::todo->value => Status::todo->value,
                        Status::in_progress->value => Status::in_progress->value,
                        Status::done->value => Status::done->value,
                    ])
                    ->default(Status::todo),

                // Field for the task's description (nullable)
                Textarea::make('description')
                    ->nullable()
                    ->label("Task Description"),

                // Hidden field for storing the associated project ID
                Hidden::make('project_id')
                    ->default(fn() => request()->query('project_id'))
                    ->required(),

                // Field for selecting the task deadline
                DateTimePicker::make('deadline')
                    ->label("Task Deadline")
                    ->required()
                    ->minDate(now()) // Deadline must be in the future
                    ->format('Y-m-d H:i')
                    ->displayFormat('d/m/Y H:i')
                    ->minutesStep(1), // Step in minutes for deadline selection

                // Field for selecting the employee assigned to the task
                Select::make('user_id')
                    ->relationship('user', 'name', modifyQueryUsing: fn($query) => $query->role('Employee'))
                    ->label("Employee")
                    ->preload()
                    ->searchable()
                    ->required(),
            ]);
    }

    /**
     * Define the table schema for displaying tasks in a list.
     *
     * This method defines the columns and actions available for the task table,
     * such as task name, description, status, deadline, and employee. It also includes
     * actions for editing and deleting tasks.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
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
            ->filters([ // You can define filters here if needed
                //
            ])
            ->actions([ // Actions available for each row in the table
                EditAction::make()->label(''), // Edit task action
                DeleteAction::make() // Delete task action
                    ->action(function (Task $record, $livewire) {
                        $record->delete();
                        $livewire->dispatch('taskDeleted');
                    })
                    ->label('')
                // ->after(fn() => self::redirectToProjectTasks()) // Redirect after delete action
            ])
            ->bulkActions([ // Actions available for bulk operations
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Redirect to the project tasks page after deleting a task.
     *
     * This method checks if a project ID is stored in the session and redirects
     * the user to the task index page filtered by that project ID.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private static function redirectToProjectTasks()
    {
        $projectId = session('current_project_id');
        if ($projectId) {
            return redirect()->to(route('filament.manager.resources.tasks.index', ['project_id' => $projectId]));
        }
    }



    /**
     * Determine whether this resource should be registered in the navigation.
     *
     * This method returns false to prevent the task resource from appearing
     * in the Filament navigation menu.
     *
     * @return bool
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    /**
     * Modify the query used to retrieve tasks.
     *
     * This method modifies the query for tasks based on the project ID present in
     * the request or session. It filters tasks to only show those associated with
     * the current project.
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Check for project ID in the request and store it in the session
        if ($projectId = request()->query('project_id')) {
            session(['current_project_id' => $projectId]);
        }

        // If a project ID is stored in the session, filter tasks by that project
        if ($projectId = session('current_project_id')) {
            $query->where('project_id', $projectId);
        }

        return $query;
    }


    /**
     * Define the pages available for this resource.
     *
     * This method defines the routes for the index, create, and edit pages for tasks.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'), // Task list page
            'create' => Pages\CreateTask::route('/create'), // Task creation page
            'edit' => Pages\EditTask::route('/{record}/edit'), // Task editing page
        ];
    }

    /**
     * Determine if the current user can access this resource.
     *
     * This method checks if the authenticated user has the 'Manager' role and
     * returns true if they are authorized to access the tasks.
     *
     * @return bool
     */
    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->hasRole('Manager');
    }
}
