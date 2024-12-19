<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->formId('form'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['owner_id'] = $data['owner_id'] ?? auth()->id();

        return $data;
    }
}
