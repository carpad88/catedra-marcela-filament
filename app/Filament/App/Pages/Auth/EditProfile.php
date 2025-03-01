<?php

namespace App\Filament\App\Pages\Auth;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Contracts\Support\Htmlable;

class EditProfile extends BaseEditProfile
{
    public function getTitle(): string|Htmlable
    {
        return 'Configuración de la cuenta';
    }

    public function form(Form $form): Form
    {
        return $form
            ->inlineLabel(false)
            ->schema([
                Section::make('Información personal')
                    ->description('La información que se utiliza para identificarte dentro de la cátedra.')
                    ->aside()
                    ->schema([
                        TextInput::make('first_name')
                            ->label('Nombre(s)')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('Apellidos')
                            ->required()
                            ->maxLength(255),
                        $this->getEmailFormComponent(),
                        TextInput::make('code')
                            ->label('Código de estudiante')
                            ->numeric()
                            ->required(fn () => auth()->user()->hasRole('student')),
                    ]),

                Section::make('Contraseña')
                    ->description('Aquí puede actualizar tu contraseña de inicio de sesión.')
                    ->aside()
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ]),
            ]);
    }
}
