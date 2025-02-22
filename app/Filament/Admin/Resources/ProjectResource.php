<?php

namespace App\Filament\Admin\Resources;

use App\Actions\BulkDeleteRecords;
use App\Actions\DeleteRecord;
use App\Actions\Projects\DuplicateProject;
use App\Enums\Status;
use App\Filament\Admin\Resources\ProjectResource\RelationManagers\CriteriasRelationManager;
use App\Models\Group;
use App\Models\Project;
use App\Models\Tag;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'phosphor-calendar-dots-duotone';

    protected static ?int $navigationSort = 3;

    protected static ?string $label = 'Proyecto';

    protected static ?string $pluralLabel = 'Proyectos';

    public static function form(Form $form): Form
    {
        $disabledButtons = ['attachFiles', 'blockquote', 'codeBlock', 'heading', 'link', 'redo', 'table', 'undo'];

        return $form
            ->columns(3)
            ->schema([
                Components\Section::make('Detalles')
                    ->columnSpan(1)
                    ->collapsible()
                    ->schema([
                        Components\FileUpload::make('cover')
                            ->label('Portada')
                            ->optimize('webp')
                            ->image()
                            ->directory('projects')
                            ->required(),
                        Components\TextInput::make('title')
                            ->label('Título')
                            ->required(),
                        Components\Select::make('category_id')
                            ->label('Categoría')
                            ->relationship('category', 'name')
                            ->preload()
                            ->options(fn () => Tag::where('type', 'Proyectos')
                                ->pluck('name', 'id')
                            )
                            ->required(),
                        Components\ToggleButtons::make('status')
                            ->label('¿Estado del proyecto?')
                            ->hiddenOn(['view', 'create'])
                            ->options(Status::class)
                            ->grouped(),
                        Components\Select::make('groups')
                            ->label('Grupos')
                            ->hiddenOn('create')
                            ->disabledOn('edit')
                            ->visible(fn ($record) => $record->groups->isNotEmpty())
                            ->multiple()
                            ->relationship('groups', 'period')
                            ->getOptionLabelFromRecordUsing(fn (Group $record) => "$record->period - $record->title"),
                    ]),

                Components\Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Components\Section::make('Descripción')
                            ->compact()
                            ->collapsible()
                            ->collapsed(fn ($operation) => $operation == 'edit')
                            ->schema([
                                Components\MarkdownEditor::make('description')
                                    ->hiddenLabel()
                                    ->disableToolbarButtons($disabledButtons)
                                    ->required(),
                            ]),
                        Components\Section::make('Objetivos')
                            ->compact()
                            ->collapsible()
                            ->collapsed(fn ($operation) => $operation == 'edit')
                            ->schema([
                                Components\MarkdownEditor::make('goals')
                                    ->hiddenLabel()
                                    ->disableToolbarButtons($disabledButtons)
                                    ->required(),
                            ]),
                        Components\Section::make('Actividades')
                            ->compact()
                            ->collapsible()
                            ->collapsed(fn ($operation) => $operation == 'edit')
                            ->schema([
                                Components\MarkdownEditor::make('activities')
                                    ->hiddenLabel()
                                    ->disableToolbarButtons($disabledButtons)
                                    ->required(),
                            ]),
                        Components\Section::make('Condiciones')
                            ->compact()
                            ->collapsible()
                            ->collapsed(fn ($operation) => $operation == 'edit')
                            ->schema([
                                Components\MarkdownEditor::make('conditions')
                                    ->hiddenLabel()
                                    ->disableToolbarButtons($disabledButtons)
                                    ->required(),
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->owned())
            ->defaultSort(fn ($query) => $query
                ->orderBy('status')
                ->orderBy(
                    fn ($query) => $query->select('period')
                        ->from('groups')
                        ->join('group_project', 'groups.id', '=', 'group_project.group_id')
                        ->whereColumn('group_project.project_id', 'projects.id')
                        ->orderBy('period')
                        ->limit(1), 'desc'
                ))
            ->columns([
                Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge(Status::class),
                Columns\TextColumn::make('title')
                    ->label('Título')
                    ->sortable()
                    ->searchable()
                    ->words(5)
                    ->tooltip(function (Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        return str_word_count($state) <= $column->getWordLimit() ? null : $state;
                    }),
                Columns\TextColumn::make('criterias_count')
                    ->counts('criterias')
                    ->label('Criterios')
                    ->tooltip('Criterios de evaluación')
                    ->badge()
                    ->alignCenter(),
                Columns\TextColumn::make('groups.period')
                    ->default('--')
                    ->formatStateUsing(fn ($state) => explode(',', $state)[0])
                    ->label('Periodo')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),
                Columns\TextColumn::make('groups_count')
                    ->label('Grupos')
                    ->counts('groups')
                    ->badge()
                    ->alignCenter(),
                Columns\TextColumn::make('works_count')
                    ->counts('works')
                    ->label('Trabajos')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->options(fn () => Tag::where('type', 'Proyectos')
                        ->pluck('name', 'id')
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('archive')
                        ->label('Archivar')
                        ->icon('phosphor-archive-duotone')
                        ->visible(fn (Project $record) => $record->status == Status::Active)
                        ->action(fn (Project $record) => $record->update(['status' => Status::Archived])),
                    Tables\Actions\Action::make('unarchive')
                        ->label('Desarchivar')
                        ->icon('phosphor-box-arrow-up-duotone')
                        ->visible(fn (Project $record) => $record->status == Status::Archived)
                        ->action(fn (Project $record) => $record->update(['status' => Status::Active])),
                    Tables\Actions\ReplicateAction::make()
                        ->label('Duplicar')
                        ->icon('phosphor-copy-duotone')
                        ->requiresConfirmation()
                        ->modalHeading('Duplicar proyecto')
                        ->modalDescription('¿Estás seguro de que deseas duplicar este proyecto?')
                        ->action(fn (Project $record) => DuplicateProject::handle($record)),
                    Tables\Actions\DeleteAction::make()
                        ->action(fn (Project $record) => DeleteRecord::handle($record)),
                ])->link(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(fn ($records) => BulkDeleteRecords::handle($records)),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CriteriasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\ProjectResource\Pages\ListProjects::route('/'),
            'create' => \App\Filament\Admin\Resources\ProjectResource\Pages\CreateProject::route('/create'),
            'edit' => \App\Filament\Admin\Resources\ProjectResource\Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
