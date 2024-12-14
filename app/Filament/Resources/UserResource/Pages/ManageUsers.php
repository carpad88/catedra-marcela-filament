<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Actions\Auth\SendWelcomeEmail;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('xl')
                ->slideOver()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['first_name'] = str($data['first_name'])->title();
                    $data['last_name'] = str($data['last_name'])->title();

                    return $data;
                })
                ->after(fn ($record) => (new SendWelcomeEmail)->handle($record)),
            Actions\ImportAction::make()
                ->label('Importar usuarios')
                ->modalHeading('Importación masiva de usuarios')
                ->importer(UserImporter::class),
        ];
    }
}
