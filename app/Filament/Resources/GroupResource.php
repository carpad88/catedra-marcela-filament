<?php

namespace App\Filament\Resources;

use App\Enums\Status;
use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\GroupResource\RelationManagers\UsersRelationManager;
use App\Filament\Resources\GroupResource\RelationManagers\WorksRelationManager;
use App\Models\Group;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'phosphor-chalkboard-teacher-duotone';

    protected static ?int $navigationSort = 1;

    protected static ?string $label = 'Grupo';

    protected static ?string $pluralLabel = 'Grupos';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(4)
            ->schema([
                Components\TextInput::make('title')
                    ->label('Descripción')
                    ->hiddenOn('view')
                    ->columnSpan(4)
                    ->required(),
                Components\TextInput::make('year')
                    ->label('Año')
                    ->hiddenOn('view')
                    ->default(date('Y'))
                    ->numeric()
                    ->length(4)
                    ->required(),
                Components\ToggleButtons::make('cycle')
                    ->label('Ciclo')
                    ->hiddenOn('view')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                    ])
                    ->default('A')
                    ->grouped()
                    ->required(),
                Components\ToggleButtons::make('status')
                    ->label('¿Estado del grupo?')
                    ->hiddenOn(['view', 'create'])
                    ->columnSpan(2)
                    ->options(Status::class)
                    ->grouped(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->owned())
            ->defaultPaginationPageOption(25)
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Descripción'),
                Tables\Columns\TextColumn::make('year')
                    ->label('Año')
                    ->alignCenter()
                    ->badge(),
                Tables\Columns\TextColumn::make('cycle')
                    ->label('Ciclo')
                    ->alignCenter()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'A' => 'primary',
                        'B' => 'warning'
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge(Status::class),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Docente')
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),
                Tables\Columns\TextColumn::make('projects_count')
                    ->counts('projects')
                    ->label('Proyectos')
                    ->badge()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('students_count')
                    ->counts('students')
                    ->label('Estudiantes')
                    ->badge()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('works_count')
                    ->counts('works')
                    ->label('Trabajos')
                    ->badge()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('works_avg_score')
                    ->label('Promedio')
                    ->color(Color::Green)
                    ->avg('works', 'score')
                    ->numeric(decimalPlaces: 0)
                    ->badge()
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->modalWidth('2xl'),
                    Tables\Actions\Action::make('archive')
                        ->label('Archivar')
                        ->icon('phosphor-archive-duotone')
                        ->visible(fn (Group $record) => $record->status == Status::Active)
                        ->action(function (Group $record) {
                            $record->update(['status' => Status::Archived]);
                            $record->projects()->update(['status' => Status::Archived]);

                            Notification::make()
                                ->title('Grupo archivado')
                                ->body('Los proyectos y trabajos asociados también han sido archivados.')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\Action::make('unarchive')
                        ->label('Desarchivar')
                        ->icon('phosphor-box-arrow-up-duotone')
                        ->visible(fn (Group $record) => $record->status == Status::Archived)
                        ->action(function (Group $record) {
                            $record->update(['status' => Status::Active]);
                            $record->projects()->update(['status' => Status::Active]);

                            Notification::make()
                                ->title('Grupo desarchivado')
                                ->body('Los proyectos y trabajos asociados también han sido desarchivados.')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make(),
                ])->link(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGroups::route('/'),
            'view' => Pages\ViewGroup::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
            ProjectsRelationManager::class,
            WorksRelationManager::class,
        ];
    }
}
