<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Group;
use App\Models\User;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Components\TextInput::make('code')
                    ->label('Código de estudiante')
                    ->required()
                    ->maxLength(15),
                Components\Select::make('groups')
                    ->label('Grupos')
                    ->visibleOn('create')
                    ->relationship(
                        'groups',
                        'title',
                        modifyQueryUsing: fn (Builder $query, $operation) => $query->currentCycle()->owned()
                    )
                    ->getOptionLabelFromRecordUsing(fn (Group $record
                    ) => "$record->year$record->cycle - $record->title")
                    ->preload(fn (Builder $query, $operation) => $operation == 'create')
                    ->optionsLimit(4),

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
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rol')
                    ->visible(fn () => auth()->user()->hasRole('super_admin'))
                    ->badge(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('groups_count')
                    ->counts('groups')
                    ->label('Grupos')
                    ->badge()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('last_login_at')
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
                    ->slideOver()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['first_name'] = str($data['first_name'])->title();
                        $data['last_name'] = str($data['last_name'])->title();

                        return $data;
                    }),
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
