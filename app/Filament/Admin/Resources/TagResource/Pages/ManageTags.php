<?php

namespace App\Filament\Admin\Resources\TagResource\Pages;

use App\Enums\RootTagsEnum;
use App\Filament\Admin\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;

class ManageTags extends ManageRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('sm'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];

        foreach (RootTagsEnum::cases() as $tag) {
            $tabs[$tag->value] = Tab::make($tag->getLabel())
                ->modifyQueryUsing(fn ($query) => $query->where('type', $tag->getLabel()));
        }

        return $tabs;
    }
}
