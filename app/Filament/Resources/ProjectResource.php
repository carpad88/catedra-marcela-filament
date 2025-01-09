<?php

namespace App\Filament\Resources;

use App\Enums\Status;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers\CriteriasRelationManager;
use App\Models\Group;
use App\Models\Project;
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
                        Components\TextInput::make('title')
                            ->label('Título')
                            ->required(),
                        Components\DatePicker::make('started_at')
                            ->label('Comienzo')
                            ->native(false)
                            ->default(now())
                            ->required(),
                        Components\DatePicker::make('finished_at')
                            ->label('Entrega')
                            ->native(false)
                            ->default(now()->addDays(10))
                            ->required(),
                        Components\FileUpload::make('cover')
                            ->label('Portada')
                            ->required(),
                        Components\Select::make('groups')
                            ->label('Grupos')
                            ->hiddenOn('create')
                            ->disabledOn('edit')
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
            ->defaultSort('started_at', 'desc')
            ->columns([
                Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge(Status::class),
                Columns\TextColumn::make('title')
                    ->label('Título')
                    ->words(5)
                    ->tooltip(function (Columns\TextColumn $column): ?string {
                        $state = $column->getState();

                        return str_word_count($state) <= $column->getWordLimit() ? null : $state;
                    }),
                Columns\TextColumn::make('started_at')
                    ->label('Comienzo')
                    ->dateTime('M j, Y'),
                Columns\TextColumn::make('finished_at')
                    ->label('Entrega')
                    ->dateTime('M j, Y'),
                Columns\TextColumn::make('groups.period')
                    ->default('--')
                    ->formatStateUsing(fn ($state) => explode(',', $state)[0])
                    ->label('Periodo')
                    ->badge()
                    ->alignCenter(),
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
                //
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
                    Tables\Actions\DeleteAction::make(),
                ])->link(),
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
            CriteriasRelationManager::class,
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
}
