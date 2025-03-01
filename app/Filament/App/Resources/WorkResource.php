<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\WorkResource\Pages;
use App\Models\Work;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkResource extends Resource
{
    protected static ?string $model = Work::class;

    protected static ?string $label = 'Mis proyectos';

    protected static ?string $navigationIcon = 'phosphor-pencil-line-duotone';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('student');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->disabled(fn (Work $record) => $record->finished < now())
            ->schema([
                Forms\Components\Section::make()
                    ->columns(3)
                    ->hiddenOn('create')
                    ->schema([
                        Forms\Components\FileUpload::make('cover')
                            ->label('Portada')
                            ->columnSpan(1)
                            ->directory(\App\Filament\Admin\Resources\WorkResource::getWorkFolder())
                            ->image()
                            ->optimize('webp')
                            ->maxSize(1024)
                            ->required(),
                        Forms\Components\FileUpload::make('images')
                            ->label('Imágenes')
                            ->columnSpan(2)
                            ->panelLayout('grid')
                            ->directory(\App\Filament\Admin\Resources\WorkResource::getWorkFolder())
                            ->required()
                            ->image()
                            ->optimize('webp')
                            ->maxSize(1024)
                            ->minFiles(3)
                            ->maxFiles(10)
                            ->multiple(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('group')
                ->where('user_id', auth()->user()->id)
            )
            ->defaultGroup('group.period')
            ->recordUrl(null)
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
                        ->view('filament.admin.tables.columns.cover'),
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
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Previsualizar')
                    ->visible(fn (Work $record) => $record->images),
                Tables\Actions\EditAction::make()
                    ->label('Agregar imágenes')
                    ->icon('phosphor-images-duotone')
                    ->visible(fn (Work $record) => auth()->user()->can('update_work')
                        && now() < $record->finished->addDays(3)
                    ),
                Tables\Actions\Action::make('rubric')
                    ->label('Rúbrica')
                    ->icon('phosphor-exam-duotone')
                    ->visible(fn () => auth()->user()->can('update_work'))
                    ->url(fn ($record) => WorkResource::getUrl('rubric', ['record' => $record])),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListWorks::route('/'),
            'view' => Pages\ViewWork::route('/{record}'),
            'edit' => Pages\EditWork::route('/{record}/edit'),
            'rubric' => Pages\GradeWork::route('/{record}/rubric'),
        ];
    }
}
