<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\WorkResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorksRelationManager extends RelationManager
{
    protected static string $relationship = 'works';

    protected static ?string $title = 'Trabajos';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->recordUrl(fn ($record) => WorkResource::getUrl('rubric', ['record' => $record]))
            ->defaultGroup('group.period')
            ->groupingSettingsHidden()
            ->paginated(false)
            ->groups([
                Group::make('group.period')
                    ->orderQueryUsing(fn (Builder $query, string $direction) => $query
                        ->join('groups as g', 'works.group_id', '=', 'g.id')
                        ->select('works.*', 'g.year', 'g.cycle')
                        ->orderBy('g.year', 'desc')
                        ->orderBy('g.cycle', 'desc')
                    )
                    ->titlePrefixedWithLabel(false)
                    ->getTitleFromRecordUsing(fn ($record): string => $record->group->period)
                    ->getDescriptionFromRecordUsing(fn ($record): string => $record->group->title),
            ])
            ->columns([
                Tables\Columns\Layout\Split::make([
                    ViewColumn::make('cover')
                        ->view('filament.tables.columns.cover'),
                ]),
                Tables\Columns\TextColumn::make('score')
                    ->extraAttributes(['class' => 'hidden'])
                    ->summarize(
                        Average::make()
                            ->label('Promedio')
                            ->numeric(decimalPlaces: 0)
                            ->suffix(' puntos')
                    ),
                Tables\Columns\TextColumn::make('project.title')
                    ->extraAttributes(['class' => 'hidden'])
                    ->summarize(
                        Count::make()
                            ->label('Proyectos')
                    ),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Editar')
                    ->icon('phosphor-pencil-duotone')
                    ->color(Color::Blue)
                    ->url(fn ($record) => WorkResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\Action::make('rubric')
                    ->label('Rúbrica')
                    ->icon('phosphor-exam-duotone')
                    ->color(Color::Green)
                    ->url(fn ($record) => WorkResource::getUrl('rubric', ['record' => $record])),
            ]);
    }
}
