<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Notifications\WelcomeEmail;
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
                    $data['password'] = str()->random(10);

                    return $data;
                })
                ->after(fn ($record) => $this->sendWelcomeEmail($record)),
        ];
    }

    protected function sendWelcomeEmail(User $record): void
    {
        $notification = new WelcomeEmail($record);
        $record->notify($notification);
    }
}
