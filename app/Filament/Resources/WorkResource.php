<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkResource\Pages;
use App\Models\Group;
use App\Models\Work;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkResource extends Resource
{
    protected static ?string $model = Work::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    ->schema([
                        Components\FileUpload::make('cover')
                            ->columnSpan(1)
                            ->label('Portada')
                            ->image()
                            ->required(),
                        Components\FileUpload::make('images')
                            ->columnSpan(2)
                            ->label('Imágenes')
                            ->required()
                            ->image()
                            ->multiple()
                            ->panelLayout('grid'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Columns\ImageColumn::make('cover')
                    ->label('Portada')
                    ->height(100)
                    ->defaultImageUrl(url('images/placeholder.png')),
                Columns\TextColumn::make('group.period')
                    ->label('Grupo')
                    ->searchable()
                    ->sortable(),
                Columns\TextColumn::make('project.title')
                    ->label('Proyecto')
                    ->words(5)
                    ->searchable(),
                Columns\TextColumn::make('user.name')
                    ->label('Estudiante')
                    ->sortable(),
                Columns\TextColumn::make('project.finished_at')
                    ->label('Entrega')
                    ->dateTime('M j, Y')
                    ->sortable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorks::route('/'),
            'create' => Pages\CreateWork::route('/create'),
            'edit' => Pages\EditWork::route('/{record}/edit'),
        ];
    }
}
