<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    /**
     * The Eloquent model associated with this resource.
     *
     * @var string|null
     */
    protected static ?string $model = Project::class;

    /**
     * The icon used for the navigation item.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    /**
     * The group name under which the resource appears in the sidebar.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = "Management";

    /**
     * The label used for the navigation item.
     *
     * @var string|null
     */
    protected static ?string $navigationLabel = 'Projects';

    /**
     * Bootstrap method for the resource.
     *
     * This method clears the current project ID stored in session
     * whenever the project index page is accessed.
     */
    public static function boot()
    {
        parent::boot();
        if (request()->routeIs('filament.manager.resources.projects.index')) {
            session()->forget('current_project_id');
        }
    }

    /**
     * Define the form schema used for creating or editing a project.
     *
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1) // Single-column grid layout
                    ->schema([
                        // Input field for the project name
                        TextInput::make('name')
                            ->required()
                            ->label("Project Name"),

                        // Textarea field for the project description
                        Textarea::make('description')
                            ->nullable()
                            ->label("Project Description"),
                    ]),
            ]);
    }

    /**
     * Define the table schema for displaying the list of projects.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Column for displaying the project name
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label("Project Name"),

                // Column for displaying the project description
                TextColumn::make('description')
                    ->searchable()
                    ->label("Project Description")
                    ->wrap()
                    ->limit(null),
            ])
            ->filters([
                // No filters defined (can be added if needed)
            ])
            ->actions([
                // Custom action to redirect to related tasks of the project
                Action::make('task')
                    ->label('Task')
                    ->color('info')
                    ->url(fn($record) => route('filament.manager.resources.tasks.index', ['project_id' => $record->id]))
                    ->icon('heroicon-o-document-duplicate'),

                // Action to edit the project
                EditAction::make(),

                // Action to delete the project
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Define the pages available for this resource.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'), // List all projects
            'create' => Pages\CreateProject::route('/create'), // Create a new project
            'edit' => Pages\EditProject::route('/{record}/edit'), // Edit an existing project
        ];
    }

    /**
     * Determine if the authenticated user can access this resource.
     *
     * Only users with the "Manager" role can access.
     *
     * @return bool
     */
    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->hasRole('Manager');
    }

    /**
     * Determine if the resource should be registered in the sidebar navigation.
     *
     * Only users with the "Manager" role will see the navigation link.
     *
     * @return bool
     */
    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->user()?->hasRole('Manager');
    }
}
