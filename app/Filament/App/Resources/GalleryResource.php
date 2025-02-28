<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\GalleryResource\Pages;
use App\Filament\App\Resources\GalleryResource\RelationManagers;
use App\Models\Tag;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class GalleryResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'phosphor-images-duotone';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Galería';

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\WorksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleries::route('/'),
            'view' => Pages\ViewGallery::route('/{record}'),
        ];
    }
}
