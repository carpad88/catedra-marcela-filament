<?php

namespace App\Filament\Admin\Resources\WorkResource\Pages;

use App\Filament\Admin\Resources\WorkResource;
use App\Models\Work;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditWork extends EditRecord
{
    protected static string $resource = WorkResource::class;

    public string|int|null|Model|Work $record;

    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->formId('form'),
            Actions\DeleteAction::make(),
        ];
    }
}
