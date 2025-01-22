<?php

namespace App\Filament\Admin\Resources;

use App\Actions\BulkDeleteRecords;
use App\Actions\DeleteRecord;
use App\Models\Group;
use App\Models\Project;
use App\Models\User;
use App\Models\Work;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkResource extends Resource
{
    protected static ?string $model = Work::class;

    protected static ?string $navigationIcon = 'phosphor-images-duotone';

    protected static ?int $navigationSort = 4;

    protected static ?string $label = 'Trabajo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Section::make()
                    ->columns(3)
                    ->schema([
                        Components\Select::make('group_id')
                            ->label('Grupo')
                            ->relationship(
                                'group',
                                'period',
                                modifyQueryUsing: fn (Builder $query, $operation) => $query->orderBy('period', 'desc')
                            )
                            ->getOptionLabelFromRecordUsing(fn (Group $record) => "$record->period - $record->title")
                            ->preload(fn (Builder $query, $operation) => $operation == 'create')
                            ->searchable(['period', 'title'])
                            ->optionsLimit(10)
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('project_id', []);
                                $set('user_id', []);
                            })
                            ->required(),
                        Components\Select::make('project_id')
                            ->label('Proyecto')
                            ->options(fn (Get $get): array => $get('group_id')
                                ? Group::where('id', $get('group_id'))->first()->projects->pluck('title',
                                    'id')->toArray()
                                : []
                            )
                            ->required(),
                        Components\Select::make('user_id')
                            ->label('Estudiante')
                            ->options(fn (Get $get): array => $get('group_id')
                                ? Group::where('id', $get('group_id'))->first()->students->pluck('name',
                                    'id')->toArray()
                                : []
                            )
                            ->required(),
                    ]),

                Components\Section::make()
                    ->columns(3)
                    ->hiddenOn('create')
                    ->schema([
                        Components\FileUpload::make('cover')
                            ->label('Portada')
                            ->columnSpan(1)
                            ->directory(self::getWorkFolder())
                            ->image()
                            ->optimize('webp')
                            ->maxSize(1024)
                            ->required(),
                        Components\FileUpload::make('images')
                            ->label('Imágenes')
                            ->columnSpan(2)
                            ->panelLayout('grid')
                            ->directory(self::getWorkFolder())
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
            ->defaultSort(fn (Builder $query) => $query
                ->join('projects', 'works.project_id', '=', 'projects.id')
                ->join('users', 'works.user_id', '=', 'users.id')
                ->orderBy('projects.finished_at', 'desc')
                ->orderBy('users.name')
                ->select('works.*')
            )
            ->recordUrl(false)
            ->columns([
                Columns\ImageColumn::make('cover')
                    ->label('Portada')
                    ->height(100)
                    ->defaultImageUrl(url('images/placeholder.png')),
                Columns\TextColumn::make('group.title')
                    ->label('Grupo')
                    ->description(fn (Work $record) => $record->group->period, 'above')
                    ->searchable()
                    ->sortable(),
                Columns\TextColumn::make('project.title')
                    ->label('Proyecto')
                    ->words(5)
                    ->searchable(),
                Columns\TextColumn::make('user.name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable(),
                Columns\TextColumn::make('project.finished_at')
                    ->label('Entrega')
                    ->dateTime('M j, Y')
                    ->sortable(),
                Columns\TextColumn::make('score')
                    ->label('Calificación')
                    ->alignCenter()
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('rubric')
                        ->label('Rúbrica')
                        ->icon('phosphor-exam-duotone')
                        ->color(Color::Green)
                        ->url(fn ($record) => WorkResource::getUrl('rubric', ['record' => $record])),
                    Tables\Actions\DeleteAction::make()
                        ->action(fn (Work $record) => DeleteRecord::handle($record)),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\WorkResource\Pages\ListWorks::route('/'),
            'edit' => \App\Filament\Admin\Resources\WorkResource\Pages\EditWork::route('/{record}/edit'),
            'rubric' => \App\Filament\Admin\Resources\WorkResource\Pages\GradeWork::route('/{record}/rubric'),
        ];
    }

    public static function getWorkFolder(): \Closure
    {
        return function (Get $get) {
            $group = Group::where('id', $get('group_id'))->first();
            $user = User::where('id', $get('user_id'))->first();
            $project = Project::where('id', $get('project_id'))->first();

            return "$group->folderName/$user->folderName/$project->folderName";
        };
    }
}
