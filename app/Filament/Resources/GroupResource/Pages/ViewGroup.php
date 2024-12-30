<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewGroup extends ViewRecord
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make('edit')
                ->label('Editar Grupo')
                ->modalWidth('2xl'),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return "{$this->getRecord()->title} - {$this->getRecord()->period}";
    }
}
