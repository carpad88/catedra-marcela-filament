<?php

namespace App\Filament\App\Resources\GalleryResource\RelationManagers;

use App\Enums\Visibility;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class WorksRelationManager extends RelationManager
{
    protected static string $relationship = 'works';

    protected static string $view = 'filament.app.resources.gallery.relation-managers.works-relation-manager';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['user', 'group'])->where('visibility', Visibility::Public))
            ->defaultSort('created_at', 'desc')
            ->recordTitleAttribute('id');
    }
}
