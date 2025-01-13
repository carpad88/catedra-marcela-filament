<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.admin.resource.user.pages.view-record';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make('edit')
                ->label('Editar usuario')
                ->modalWidth('xl')
                ->slideOver(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return new HtmlString(
            '<div class="flex items-center">
                <div class="">
                    <p class="text-base font-light text-gray-500 mb-2">Código: <span class="font-bold">'.$this->record->code.'</span></p>
                    <h1 class="text-3xl font-bold text-gray-900">'.$this->record->name.'</h1>
                    <p class="text-base font-light text-gray-500">'.$this->record->email.'</p>
                </div>
            </div>'
        );
    }
}
