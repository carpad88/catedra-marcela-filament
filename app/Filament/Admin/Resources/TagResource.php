<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TagResource\Pages;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'phosphor-tag-duotone';

    protected static ?int $navigationSort = 5;

    protected static ?string $label = 'Etiqueta';

    protected static ?string $pluralLabel = 'Etiquetas';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Colección')
                    ->options([
                        'Proyectos' => 'Proyectos',
                        'Recursos' => 'Recursos',
                        'Artículos' => 'Artículos',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('name.es')
                    ->label('Name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('type')
            ->groupingSettingsHidden()
            ->groups([
                Group::make('type')
                    ->titlePrefixedWithLabel(false),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('sm')
                    ->mutateFormDataUsing(function ($data) {
                        $data['name']['es'] = str($data['name']['es'])->title();

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageTags::route('/'),
        ];
    }
}
