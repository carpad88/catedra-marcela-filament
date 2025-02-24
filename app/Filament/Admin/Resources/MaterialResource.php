<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MaterialResource\Pages;
use App\Models\Material;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static ?string $navigationIcon = 'phosphor-books-duotone';

    protected static ?string $label = 'Recursos';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->default(fn ($livewire) => $livewire->activeTab === 'books'
                        ? Tag::where('name->es', 'Libros')->first()->id
                        : null
                    )
                    ->disabled(fn ($livewire) => $livewire->activeTab === 'books')
                    ->dehydrated()
                    ->preload()
                    ->options(fn ($livewire) => Tag::where('type', 'Recursos')
                        ->where(
                            'name->es',
                            $livewire->activeTab === 'books' ? '=' : '!=',
                            'Libros')
                        ->pluck('name', 'id')
                    )
                    ->live()
                    ->required(),

                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->prefixIcon('phosphor-text-h-one-duotone')
                    ->required(),
                Forms\Components\TextInput::make('author')
                    ->label('Autor(es)')
                    ->prefixIcon('phosphor-user-duotone')
                    ->required(),

                Forms\Components\Group::make()
                    ->columns(1)
                    ->statePath('data')
                    ->visible(fn ($livewire) => $livewire->activeTab === 'books')
                    ->schema([
                        Forms\Components\TextInput::make('year')
                            ->label('Año')
                            ->prefixIcon('phosphor-calendar-duotone')
                            ->numeric()
                            ->length(4)
                            ->required(),
                        Forms\Components\TextInput::make('location')
                            ->label('Lugar de publicación')
                            ->prefixIcon('phosphor-globe-hemisphere-west-duotone')
                            ->required(),
                        Forms\Components\TextInput::make('publisher')
                            ->label('Editorial')
                            ->prefixIcon('phosphor-building-office-duotone')
                            ->required(),
                    ]),

                Forms\Components\Group::make()
                    ->columns(1)
                    ->statePath('data')
                    ->visible(fn ($livewire) => $livewire->activeTab === 'digital')
                    ->schema([
                        Forms\Components\TextInput::make('link')
                            ->label('Link')
                            ->prefixIcon('phosphor-link-duotone')
                            ->url()
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(8)
                            ->autosize()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort(fn ($livewire) => $livewire->activeTab === 'books' ? 'title' : 'category.name')
            ->defaultGroup('category.name')
            ->groupingSettingsHidden()
            ->groups([
                Group::make('category.name')
                    ->label('Categoría')
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->visible(fn ($livewire) => $livewire->activeTab === 'digital')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author')
                    ->label('Autor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data.year')
                    ->label('Año')
                    ->visible(fn ($livewire) => $livewire->activeTab === 'books')
                    ->badge(),
                Tables\Columns\TextColumn::make('data.location')
                    ->label('Lugar de publicación')
                    ->visible(fn ($livewire) => $livewire->activeTab === 'books'),
                Tables\Columns\TextColumn::make('data.publisher')
                    ->label('Editorial')
                    ->visible(fn ($livewire) => $livewire->activeTab === 'books'),
                Tables\Columns\IconColumn::make('data.link')
                    ->url(fn (Material $record): string => $record->data['link'])
                    ->openUrlInNewTab()
                    ->label('Enlace')
                    ->visible(fn ($livewire) => $livewire->activeTab === 'digital')
                    ->alignCenter()
                    ->icon('phosphor-arrow-square-out-duotone'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship(
                        'category',
                        'name',
                        modifyQueryUsing: fn ($query) => $query->withType('Recursos')
                    )
                    ->label('Categoría')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('xl')
                    ->slideOver()
                    ->mutateFormDataUsing(function ($data) {
                        $data['author'] = str($data['author'])->title();

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageMaterials::route('/'),
        ];
    }
}
