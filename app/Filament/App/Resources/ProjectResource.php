<?php

namespace App\Filament\App\Resources;

use App\Enums\Status;
use App\Filament\App\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'phosphor-calendar-dots-duotone';

    protected static ?string $label = 'Proyectos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        $isStudent = auth()->user()->hasRole(['student']);

        $addFinishedAtColumn = [
            'finished_at' => \DB::table('group_project')
                ->whereColumn('group_project.project_id', 'projects.id')
                ->select('finished_at')
                ->orderBy('finished_at', 'desc')
                ->limit(1),
        ];

        return $table
            ->when($isStudent, fn ($table) => $table
                ->modifyQueryUsing(fn ($query) => $query
                    ->whereHas('groups', function ($query) {
                        $query->whereIn('id', auth()->user()->groups->pluck('id'));
                    })
                    ->select('projects.*')
                    ->addSelect($addFinishedAtColumn)
                    ->orderBy('finished_at', 'desc')
                )
            )
            ->when(! $isStudent, fn ($table) => $table
                ->modifyQueryUsing(fn ($query) => $query
                    ->where('status', Status::Active)
                    ->select('projects.*')
                    ->addSelect($addFinishedAtColumn)
                    ->orderBy('finished_at', 'desc')
                )
            );
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
            'view' => Pages\ViewProject::route('/{record}'),
        ];
    }
}
