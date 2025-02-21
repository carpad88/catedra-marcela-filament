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

        return $table
            ->when($isStudent, function ($table) {
                $table
                    ->modifyQueryUsing(fn ($query) => $query
                        ->whereHas('groups', function ($query) {
                            $query->whereIn('id', auth()->user()->groups->pluck('id'));
                        })
                        ->orderBy('finished_at', 'desc')
                    );
            })
            ->when(! $isStudent, function ($table) {
                $table
                    ->modifyQueryUsing(fn ($query) => $query
                        ->where('status', Status::Active)
                        ->orderBy('finished_at', 'desc')
                    );
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
            'index' => Pages\ListProjects::route('/'),
            'view' => Pages\ViewProject::route('/{record}'),
        ];
    }
}
