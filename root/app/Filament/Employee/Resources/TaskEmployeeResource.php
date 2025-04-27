<?php

namespace App\Filament\Employee\Resources;

use App\Enums\Status;
use App\Filament\Employee\Resources\TaskEmployeeResource\Pages;
use App\Models\Task;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskEmployeeResource extends Resource
{
    /**
     * The Eloquent model associated with this resource.
     *
     * @var string|null
     */
    protected static ?string $model = Task::class;

    /**
     * The group name under which the resource appears (if registered).
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Information';

    /**
     * The label used for the navigation item.
     *
     * @var string|null
     */
    protected static ?string $navigationLabel = 'Projects';

    /**
     * Define the form schema used for editing a task.
     *
     * Only the task status can be updated by employees.
     *
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('status')
                    ->label("Task Status")
                    ->options([
                        Status::todo->value => Status::todo->value,
                        Status::in_progress->value => Status::in_progress->value,
                        Status::done->value => Status::done->value,
                    ])
                    ->default(Status::todo),
            ]);
    }

    /**
     * Define the table schema for listing tasks.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Display the task name
                TextColumn::make('name')
                    ->label('Task Name')
                    ->searchable()
                    ->sortable(),

                // Display the task description
                TextColumn::make('description')
                    ->label('Task Description'),

                // Display the task deadline
                TextColumn::make('deadline')
                    ->label('Deadline')
                    ->searchable()
                    ->sortable(),

                // Display the task status with color coding and icons
                TextColumn::make('status')
                    ->label("Status")
                    ->color(fn($state) => match ($state) {
                        Status::todo->value => 'danger',
                        Status::in_progress->value => 'warning',
                        Status::done->value => 'success',
                    })
                    ->icon(fn($state) => match ($state) {
                        Status::todo->value => 'heroicon-o-document-text',
                        Status::in_progress->value => 'heroicon-o-clock',
                        Status::done->value => 'heroicon-o-check-circle',
                    })
                    ->sortable(),
            ])
            ->filters([
                // No filters defined yet
            ])
            ->actions([
                // Allow employees to edit the task (only status)
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // Bulk delete option (optional for employees)
                ]),
            ]);
    }

    /**
     * Customize the Eloquent query to only show tasks assigned to the current employee,
     * and optionally filter by project if project_id is available.
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $userId = Filament::auth()->id();

        if ($projectId = request()->query('project_id')) {
            session(['current_project_id' => $projectId]);
        }

        $projectId = request()->query('project_id');

        return parent::getEloquentQuery()
            ->where('user_id', $userId)
            ->when($projectId, function (Builder $query) use ($projectId) {
                return $query->where('project_id', $projectId);
            });
    }

    /**
     * Define any relation managers for the resource.
     *
     * @return array
     */
    public static function getRelations(): array
    {
        return [
            // No relations defined yet
        ];
    }

    /**
     * Determine if the resource should be shown in navigation.
     *
     * @return bool
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false; // This resource won't be shown in the sidebar
    }

    /**
     * Define the available pages for this resource.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaskEmployees::route('/'), // List tasks
            'edit' => Pages\EditTaskEmployee::route('/{record}/edit'), // Edit task
        ];
    }

    /**
     * Determine if the current user can access this resource.
     *
     * Only users with the "Employee" role can access.
     *
     * @return bool
     */
    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->hasRole('Employee');
    }
}
