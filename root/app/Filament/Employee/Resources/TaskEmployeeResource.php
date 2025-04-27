<?php

namespace App\Filament\Employee\Resources;

use App\Enums\Status;
use App\Filament\Employee\Resources\TaskEmployeeResource\Pages;
use App\Filament\Employee\Resources\TaskEmployeeResource\RelationManagers;
use App\Models\Task;
use App\Models\TaskEmployee;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskEmployeeResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationGroup = 'Information';

    protected static ?string $navigationLabel = 'Projects';

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Task Name')->searchable()->sortable(),
                TextColumn::make('description')->label('Task Description'),
                TextColumn::make('deadline')->label('Deadline')->searchable()->sortable(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaskEmployees::route('/'),
            'edit' => Pages\EditTaskEmployee::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->hasRole('Employee');
    }
}
