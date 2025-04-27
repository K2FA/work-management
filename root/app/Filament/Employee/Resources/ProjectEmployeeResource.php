<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\ProjectEmployeeResource\Pages\ListProjectEmployees;
use App\Models\Project;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectEmployeeResource extends Resource
{
    /**
     * The model associated with this resource.
     *
     * @var string|null
     */
    protected static ?string $model = Project::class;

    /**
     * The icon displayed in the navigation menu.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    /**
     * The group name for the navigation menu.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Information';

    /**
     * The label for the navigation menu item.
     *
     * @var string|null
     */
    protected static ?string $navigationLabel = 'Projects';

    /**
     * Boot method to reset session data if accessing the project list.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        if (request()->routeIs('filament.employee.resources.project-employees.index')) {
            session()->forget('current_project_id');
        }
    }

    /**
     * Define the form schema (currently empty, as editing is not enabled).
     *
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // No form fields defined
            ]);
    }

    /**
     * Define the table schema for listing projects.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Display project name
                TextColumn::make('name')
                    ->label('Project Name')
                    ->searchable(),

                // Display project description
                TextColumn::make('description')
                    ->label('Project Description')
                    ->searchable(),
            ])
            ->filters([
                // No filters defined yet
            ])
            ->actions([
                // Custom action to view related tasks of the project
                Action::make('task')
                    ->label('Task')
                    ->color('info')
                    ->url(fn($record) => route('filament.employee.resources.task-employees.index', ['project_id' => $record->id]))
                    ->icon('heroicon-o-document-duplicate'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // Bulk delete action
                ]),
            ]);
    }

    /**
     * Customize the Eloquent query to only show projects assigned to the employee.
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $userId = Filament::auth()->id();

        return parent::getEloquentQuery()
            ->whereHas('tasks', function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            });
    }

    /**
     * Define any relation managers (none for now).
     *
     * @return array
     */
    public static function getRelations(): array
    {
        return [
            // No relations defined
        ];
    }

    /**
     * Define the available pages for this resource.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => ListProjectEmployees::route('/'),
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

    /**
     * Determine if this resource should appear in the navigation.
     *
     * @return bool
     */
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->user()?->hasRole('Employee');
    }
}
