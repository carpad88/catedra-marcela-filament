<?php

namespace App\Filament\Admin\Resources\ProjectResource\RelationManagers;

use App\Actions\BulkDeleteRecords;
use App\Actions\DeleteRecord;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CriteriasRelationManager extends RelationManager
{
    protected static string $relationship = 'criterias';

    protected static ?string $title = 'Criterios de evaluación';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->default('requisitos'),
                Components\TextInput::make('weight')
                    ->label('Porcentaje')
                    ->default(7)
                    ->numeric()
                    ->required(),
                Components\Repeater::make('levels')
                    ->hiddenLabel()
                    ->relationship('levels')
                    ->columnSpanFull()
                    ->columns(5)
                    ->deletable(false)
                    ->addable(false)
                    ->default([
                        ['title' => 'Level 1', 'description' => '', 'score' => 1],
                        ['title' => 'Level 2', 'description' => '', 'score' => 3],
                        ['title' => 'Level 3', 'description' => '', 'score' => 5],
                        ['title' => 'Level 4', 'description' => '', 'score' => 7],
                    ])
                    ->schema([
                        Components\TextInput::make('title')
                            ->hiddenLabel()
                            ->columnSpan(5)
                            ->disabled()
                            ->dehydrated(),
                        Components\TextInput::make('score')
                            ->label('Puntos')
                            ->numeric()
                            ->required(),
                        Components\Textarea::make('description')
                            ->columnSpan(4)
                            ->label('Descriptor')
                            ->rows(4)
                            ->required(),
                    ]),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->reorderable('order')
            ->defaultSort('order')
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título'),
                Tables\Columns\TextColumn::make('weight')
                    ->label('Puntos')
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total')
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Agregar criterio')
                    ->modalWidth('2xl')
                    ->modalHeading('Agregar criterio'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('2xl'),
                Tables\Actions\DeleteAction::make()
                    ->action(fn ($record) => DeleteRecord::handle($record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(fn ($records) => BulkDeleteRecords::handle($records)),
                ]),
            ]);
    }
}
