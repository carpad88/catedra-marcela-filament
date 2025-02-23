<?php

namespace App\Filament\Admin\Resources\ProjectResource\Pages;

use App\Enums\Status;
use App\Filament\Admin\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Activos')
                ->icon('phosphor-check-circle-duotone')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('status', '=', Status::Active)
                ),
            'archived' => Tab::make('Archivados')
                ->icon('phosphor-archive-duotone')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('status', '=', Status::Archived)
                ),
        ];
    }
}
