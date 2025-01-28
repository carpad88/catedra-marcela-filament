<?php

namespace App\Filament\App\Resources\WorkResource\Pages;

use App\Filament\App\Resources\WorkResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditWork extends EditRecord
{
    protected static string $resource = WorkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->formId('form'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Editar '.$this->record->project->title;
    }

    protected function getRedirectUrl(): ?string
    {
        return WorkResource::getUrl();
    }
}
