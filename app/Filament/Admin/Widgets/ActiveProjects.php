<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\Status;
use App\Filament\Admin\Resources\ProjectResource;
use App\Models\Project;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActiveProjects extends BaseWidget
{
    protected int|string|array $columnSpan = 6;

    public function table(Table $table): Table
    {

        return $table
            ->query(
                ProjectResource::getEloquentQuery()
                    ->where('status', Status::Active)
                    ->with('groups')
                    ->withCount('works')
                    ->withCount('criterias')
            )
            ->heading('Proyectos activos')
            ->paginated(false)
            ->recordUrl(
                fn ($record) => Filament::getPanel('app')
                    ->getResourceUrl(Project::class, 'view', ['record' => $record->id]),
                shouldOpenInNewTab: true
            )
            ->contentGrid([
                'sm' => 2,
                'xl' => 3,
            ])
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\ViewColumn::make('title')
                        ->view('filament.admin.widgets.active-projects'),
                ]),
            ]);
    }
}
