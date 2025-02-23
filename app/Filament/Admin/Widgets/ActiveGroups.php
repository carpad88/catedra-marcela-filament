<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\Status;
use App\Filament\Admin\Resources\GroupResource;
use App\Models\Group;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActiveGroups extends BaseWidget
{
    protected int|string|array $columnSpan = 6;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                GroupResource::getEloquentQuery()
                    ->owned()
                    ->where('status', Status::Active)
                    ->withAvg('works', 'score')
                    ->withCount('works')
                    ->withCount('students')
                    ->withCount('projects')
            )
            ->heading('Grupos activos')
            ->paginated(false)
            ->recordUrl(
                fn (Group $record): string => GroupResource::getUrl('view', ['record' => $record])
            )
            ->contentGrid([
                'sm' => 2,
                'xl' => 3,
            ])
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\ViewColumn::make('title')
                        ->view('filament.admin.widgets.active-groups'),
                ]),
            ]);
    }
}
