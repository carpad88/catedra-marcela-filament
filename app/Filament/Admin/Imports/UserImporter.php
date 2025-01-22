<?php

namespace App\Filament\Admin\Imports;

use App\Actions\Auth\SendWelcomeEmail;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Validation\Rule;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('first_name')
                ->label('Nombre')
                ->exampleHeader('Nombre')
                ->requiredMapping()
                ->fillRecordUsing(function (User $record, string $state): void {
                    $record->first_name = str($state)->title();
                })
                ->rules(['required', 'max:255']),
            ImportColumn::make('last_name')
                ->label('Apellido')
                ->exampleHeader('Apellido')
                ->requiredMapping()
                ->fillRecordUsing(function (User $record, string $state): void {
                    $record->last_name = str($state)->title();
                })
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->label('Email')
                ->exampleHeader('Email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255', Rule::unique('users', 'email')]),
            ImportColumn::make('code')
                ->label('Código')
                ->exampleHeader('Código')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?User
    {
        return new User;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'La importación de usuarios se completó y '.number_format($import->successful_rows).' '.str('usuario')->plural($import->successful_rows).' fueron importados.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('usuario')->plural($failedRowsCount).' no se pudieron importar.';
        }

        return $body;
    }

    protected function beforeFill(): void
    {
        $this->data['first_name'] = str($this->data['first_name'])->title();
        $this->data['last_name'] = str($this->data['last_name'])->title();
    }

    protected function afterCreate(): void
    {
        $this->record->assignRole('student');

        if ($this->options['groupID'] ?? false) {
            $this->record->groups()->attach($this->options['groupID']);
        }

        (new SendWelcomeEmail)->handle($this->record);
    }
}
