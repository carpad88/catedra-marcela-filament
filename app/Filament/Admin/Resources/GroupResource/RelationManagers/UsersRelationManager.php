<?php

namespace App\Filament\Admin\Resources\GroupResource\RelationManagers;

use App\Actions\Auth\SendWelcomeEmail;
use App\Actions\Users\CreateUserWorks;
use App\Enums\Status;
use App\Filament\Admin\Imports\UserImporter;
use App\Filament\Admin\Resources\UserResource;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Alumnos';

    protected static ?string $icon = 'phosphor-student-duotone';

    public function isReadOnly(): bool
    {
        return false;
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->students->count();
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
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('works_avg_score')
                    ->label('Promedio')
                    ->avg([
                        'works' => fn (Builder $query) => $query->where('group_id', $this->getOwnerRecord()->id),
                    ], 'score')
                    ->formatStateUsing(fn ($state) => $state ? round($state) : null)
                    ->badge()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Invitar Alumno')
                    ->icon('phosphor-envelope')
                    ->visible(fn () => $this->getOwnerRecord()->status == Status::Active)
                    ->slideOver()
                    ->modalWidth('xl')
                    ->modalHeading('Invitar Alumno')
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
                Tables\Actions\ImportAction::make()
                    ->label('Importar alumnos')
                    ->icon('phosphor-upload-duotone')
                    ->visible(fn () => $this->getOwnerRecord()->status == Status::Active)
                    ->modalHeading('Importación masiva de alumnos')
                    ->importer(UserImporter::class)
                    ->options([
                        'groupID' => $this->ownerRecord->id,
                    ]),
                Tables\Actions\AttachAction::make()
                    ->label('Vincular alumno')
                    ->icon('phosphor-user-plus-duotone')
                    ->visible(fn () => $this->getOwnerRecord()->status == Status::Active)
                    ->modalHeading('Vincular alumno')
                    ->recordSelect(fn (Components\Select $select) => $select
                        ->placeholder('Buscar por nombre o email')
                        ->allowHtml()
                    )
                    ->recordTitle(fn (User $record
                    ): string => "$record->name <span class='text-sm text-gray-400 block'>$record->email</span>")
                    ->recordSelectSearchColumns(['name', 'email'])
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query
                        ->whereHas('roles',
                            fn (Builder $query) => $query->where('name', 'student'))
                    )
                    ->after(function ($record, CreateUserWorks $createUserWorks) {
                        if ($record->email_verified_at) {
                            $createUserWorks->handle($this->getOwnerRecord(), $record);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('resend-welcome-email')
                    ->label('Reenviar')
                    ->tooltip('Reenviar correo de activación')
                    ->visible(fn (User $record) => ! $record->email_verified_at
                        && $this->getOwnerRecord()->status == Status::Active
                    )
                    ->icon('heroicon-o-envelope')
                    ->action(function (User $record) {
                        (new SendWelcomeEmail)->handle($record);
                        Notification::make()
                            ->title('Correo de activación reenviado')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DetachAction::make()
                    ->label('Desvincular')
                    ->modalIcon('phosphor-user-minus-duotone')
                    ->modalHeading('Desvincular alumno del grupo')
                    ->visible(fn () => $this->getOwnerRecord()->status == Status::Active),
            ])
            ->emptyStateHeading('No se encontraron alumnos')
            ->emptyStateDescription('Invita, vincula o importa alumnos a este grupo para que aparezcan aquí.');
    }
}
