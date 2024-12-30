<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->title;
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->formId('form'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', ['record' => $this->getRecord()]);
    }
}
