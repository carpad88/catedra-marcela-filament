<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'phosphor-files-duotone';

    protected static ?string $navigationGroup = 'Contenido';

    protected static ?string $label = 'Apuntes';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\Section::make('Información del apunte')
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required(),
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Extracto')
                            ->rows(10)
                            ->autosize()
                            ->required(),
                        Forms\Components\DatePicker::make('created_at')
                            ->label('Fecha de creación')
                            ->native(false)
                            ->hiddenOn(['create'])
                            ->required(),
                        Forms\Components\FileUpload::make('cover')
                            ->label('Portada')
                            ->directory(fn ($record) => $record ? "posts/{$record->id}" : 'posts')
                            ->image()
                            ->optimize('webp')
                            ->maxSize(1024)
                            ->required(),
                    ]),

                Forms\Components\Section::make('Contenido del apunte')
                    ->description(fn ($operation) => $operation === 'create'
                        ? 'Crea el apunte primero para activar el editor del contenido.'
                        : null
                    )
                    ->columnSpan(2)
                    ->compact()
                    ->disabledOn(['create'])
                    ->schema([
                        Forms\Components\MarkdownEditor::make('content')
                            ->hiddenLabel()
                            ->hiddenOn(['create'])
                            ->fileAttachmentsDirectory(fn ($record) => "posts/{$record->id}")
                            ->extraAttributes(['class' => 'max-h-[calc(100vh_-_12rem)]', 'style' => 'overflow-y: scroll;'])
                            ->required(fn ($operation) => $operation === 'edit'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->latest())
            ->recordUrl(fn ($record) => Filament::getPanel('app')
                ->getResourceUrl(Post::class, 'view', ['record' => $record->id]),
                shouldOpenInNewTab: true
            )
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('Portada')
                    ->height(100)
                    ->width(200),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
                    ->date('d \d\e F, Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
