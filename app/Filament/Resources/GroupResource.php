<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers\UsersRelationManager;
use App\Models\Group;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Grupo';

    protected static ?string $pluralLabel = 'Grupos';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(5)
            ->schema([
                Components\TextInput::make('title')
                    ->label('Descripción')
                    ->columnSpan(3)
                    ->required(),
                Components\TextInput::make('year')
                    ->label('Año')
                    ->default(date('Y'))
                    ->numeric()
                    ->length(4)
                    ->required(),
                Components\ToggleButtons::make('cycle')
                    ->label('Ciclo')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                    ])
                    ->default('A')
                    ->grouped()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->owned())
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
                Tables\Columns\TextColumn::make('active')
                    ->label('Estado')
                    ->formatStateUsing(fn (string $state): string => $state === '1' ? 'Activo' : 'Archivado')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        '1' => 'heroicon-m-check-badge',
                        '0' => 'heroicon-o-archive-box'
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '1' => 'primary',
                        '0' => 'gray'
                    }),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Propietario')
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),
                Tables\Columns\TextColumn::make('students_count')
                    ->counts('students')
                    ->label('Estudiantes')
                    ->badge()
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('2xl')
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
        ];
    }
}
