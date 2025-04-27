<?php

namespace App\Filament\Manager\Resources;

use App\Filament\Manager\Resources\ProjectResource\Pages;
use App\Filament\Manager\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Facades\Filament;
use Filament\Forms;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = "Management";

    protected static ?string $navigationLabel = 'Projects';

    public static function boot()
    {
        parent::boot();

        session()->forget('current_project_id');
    }

    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                Grid::make(1)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->label("Project Name"),
                        Textarea::make('description')
                            ->nullable()
                            ->label("Project Description")
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->label("Name"),
                TextColumn::make('description')->searchable()->sortable()->label("Description")->wrap()->limit(null),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('detail')
                    ->label('Task')
                    ->color('info')
                    ->url(fn($record) => route('filament.manager.resources.tasks.index', ['project_id' => $record->id]))
                    ->icon('heroicon-o-document-duplicate'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->hasRole('Manager');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->user()?->hasRole('Manager');
    }
}
