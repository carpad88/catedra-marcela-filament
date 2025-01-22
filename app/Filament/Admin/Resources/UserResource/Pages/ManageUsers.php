<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Actions\Auth\SendWelcomeEmail;
use App\Filament\Admin\Resources\UserResource;
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
                ->visible(fn () => auth()->user()->hasRole('super_admin'))
                ->slideOver()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['first_name'] = str($data['first_name'])->title();
                    $data['last_name'] = str($data['last_name'])->title();

                    return $data;
                })
                ->after(function ($record) {
                    (new SendWelcomeEmail)->handle($record);
                    $record->roles()->count() === 0 && $record->assignRole('student');
                }),
        ];
    }
}
