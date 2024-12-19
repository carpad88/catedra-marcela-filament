<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = 'Proyectos';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->recordUrl(
                fn (Project $record): string => ProjectResource::getUrl('view', ['record' => $record]),
            )
            ->columns([
                Columns\TextColumn::make('title')
                    ->label('Título'),
                Columns\TextColumn::make('started_at')
                    ->label('Comienzo')
                    ->dateTime('M j, Y'),
                Columns\TextColumn::make('finished_at')
                    ->label('Entrega')
                    ->dateTime('M j, Y'),
                Columns\TextColumn::make('works_count')
                    ->counts('works')
                    ->label('Trabajos')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Vincular proyecto'),
            ])
            ->emptyStateActions([
                Tables\Actions\AttachAction::make()
                    ->label('Vincular proyecto'),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([

            ]);
    }
}
