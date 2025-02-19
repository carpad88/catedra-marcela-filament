<?php

namespace App\Filament\Admin\Resources\GroupResource\Pages;

use App\Enums\Status;
use App\Filament\Admin\Resources\GroupResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;

class ManageGroups extends ManageRecords
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('2xl')
                ->mutateFormDataUsing(function ($data) {
                    $data['owner_id'] = auth()->id();

                    return $data;
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Activos')
                ->icon('phosphor-check-circle-duotone')
                ->modifyQueryUsing(fn ($query) => $query->where('status', Status::Active)),
            'archived' => Tab::make('Archivados')
                ->icon('phosphor-archive-duotone')
                ->modifyQueryUsing(fn ($query) => $query->where('status', Status::Archived)),
            'all' => Tab::make('Todos'),
        ];
    }
}
