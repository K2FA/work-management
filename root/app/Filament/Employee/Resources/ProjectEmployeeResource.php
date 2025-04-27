<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\ProjectEmployeeResource\Pages\ListProjectEmployees;
use App\Models\Project;
use Filament\Facades\Filament;
use Filament\Tables\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class ProjectEmployeeResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Information';

    protected static ?string $navigationLabel = 'Projects';

    public static function boot()
    {
        parent::boot();

        if (request()->routeIs('filament.employee.resources.project-employees.index')) {
            session()->forget('current_project_id');
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Project Name')->searchable(),
                TextColumn::make('description')->label('Project Descirption')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('task')
                    ->label('Task')
                    ->color('info')
                    ->url(fn($record) => route('filament.employee.resources.task-employees.index', ['project_id' => $record->id]))
                    ->icon('heroicon-o-document-duplicate'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $userId = Filament::auth()->id();

        return parent::getEloquentQuery()
            ->whereHas('tasks', function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            });
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
            'index' => ListProjectEmployees::route('/'),

        ];
    }

    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->hasRole('Employee');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Filament::auth()->user()?->hasRole('Employee');
    }
}
