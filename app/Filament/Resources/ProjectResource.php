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
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                        Components\Select::make('groups')
                            ->label('Grupos')
                            ->multiple()
                            ->relationship(
                                'groups',
                                'period',
                                modifyQueryUsing: fn (Builder $query, $operation) => $operation == 'create'
                                    ? $query->owned()->where('status', Status::Active)
                                    : $query->owned()
                            )
                            ->getOptionLabelFromRecordUsing(fn (Group $record) => "$record->period - $record->title")
                            ->preload(fn (Builder $query, $operation) => $operation == 'create')
                            ->optionsLimit(10),
                        Components\FileUpload::make('cover')
                            ->label('Portada')
                            ->required(),
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
                    ->label('Título'),
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
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
