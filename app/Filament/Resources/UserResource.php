<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return trans_choice('filament.user', 1);
    }

    public static function getPluralModelLabel(): string
    {
        return trans_choice('filament.user', 2);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Components\TextInput::make('first_name')
                    ->label('Nombre (s)')
                    ->required()
                    ->maxLength(255),
                Components\TextInput::make('last_name')
                    ->label('Apellidos')
                    ->required()
                    ->maxLength(255),
                Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Components\TextInput::make('code')
                    ->label('Código de estudiante')
                    ->required()
                    ->maxLength(15),
                Components\Select::make('group')
                    ->dehydrated(false) // TODO: remove when groups are implemented
                    ->options([
                        'draft' => '2025A - vespertino',
                        'reviewing' => '2025B - matutino',
                    ]),

                Components\Fieldset::make('Roles')
                    ->visible(fn () => auth()->user()->can('create_role'))
                    ->schema([
                        Components\CheckboxList::make('roles')
                            ->hiddenLabel()
                            ->columns()
                            ->relationship(
                                'roles',
                                'name',
                                modifyQueryUsing: fn ($query) => $query->where('name', '!=', 'super_admin')
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => auth()->user()->hasRole('super_admin')
                ? $query
                : $query->whereNotNull('email_verified_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->label('Ciclo')
                    ->description('Group description'), // TODO: update group description
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('last_login')
                    ->label('Último Acceso')
                    ->dateTime('d M Y H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('xl')
                    ->iconButton()
                    ->slideOver(),
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
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
