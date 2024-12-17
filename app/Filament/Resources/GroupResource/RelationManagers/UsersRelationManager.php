<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Actions\Auth\SendWelcomeEmail;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Alumnos';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return UserResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Último Acceso')
                    ->dateTime('d M Y H:i'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear Alumno')
                    ->slideOver()
                    ->modalWidth('xl')
                    ->form(static function (Forms\Form $form) {
                        return $form->schema([
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
                        ]);
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['first_name'] = str($data['first_name'])->title();
                        $data['last_name'] = str($data['last_name'])->title();

                        return $data;
                    })
                    ->after(function ($record) {
                        $record->assignRole('student');
                        (new SendWelcomeEmail)->handle($record);
                    }),
                Tables\Actions\AttachAction::make()
                    ->label('Vincular alumno')
                    ->multiple()
                    ->recordTitle(fn (User $record): string => "$record->name ($record->email)")
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query
                        ->whereHas('roles',
                            fn (Builder $query) => $query->where('name', 'student'))
                    )
                    ->recordSelectSearchColumns(['name', 'email']),
                Tables\Actions\ImportAction::make()
                    ->label('Importar alumnos')
                    ->modalHeading('Importación masiva de alumnos')
                    ->importer(UserImporter::class)
                    ->options([
                        'groupID' => $this->ownerRecord->id,
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->modalWidth('xl')
                    ->iconButton()
                    ->slideOver()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['first_name'] = str($data['first_name'])->title();
                        $data['last_name'] = str($data['last_name'])->title();

                        return $data;
                    }),
            ]);
    }
}
