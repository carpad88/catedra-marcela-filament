<?php

namespace App\Filament\Resources\WorkResource\Pages;

use App\Enums\Status;
use App\Filament\Resources\WorkResource;
use App\Models\Work;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListWorks extends ListRecords
{
    protected static string $resource = WorkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(fn ($data) => static::getModel()::updateOrCreate(
                    [
                        'group_id' => $data['group_id'],
                        'project_id' => $data['project_id'],
                        'user_id' => $data['user_id'],
                    ], $data)
                )
                ->successRedirectUrl(fn (Work $record) => WorkResource::getUrl('edit', ['record' => $record])),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Activos')
                ->icon('phosphor-check-circle-duotone')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereRelation('group', 'status', '=', Status::Active)
                ),
            'archived' => Tab::make('Archivados')
                ->icon('phosphor-archive-duotone')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereRelation('group', 'status', '=', Status::Archived)
                ),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'active';
    }
}
