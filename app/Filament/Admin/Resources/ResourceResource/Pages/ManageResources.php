<?php

namespace App\Filament\Admin\Resources\ResourceResource\Pages;

use App\Filament\Admin\Resources\ResourceResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageResources extends ManageRecords
{
    protected static string $resource = ResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('xl')
                ->slideOver()
                ->mutateFormDataUsing(function ($data) {
                    $data['author'] = str($data['author'])->title();

                    return $data;
                }),
        ];
    }

    public function getHeading(): string|Htmlable
    {
        return 'Libros y recursos digitales';
    }

    public function getTabs(): array
    {
        return [
            'books' => Tab::make('Libros')
                ->modifyQueryUsing(fn ($query) => $query
                    ->whereHas('category', fn ($query) => $query->where('name->es', 'Libros'))),
            'digital' => Tab::make('Recursos digitales')
                ->modifyQueryUsing(fn ($query) => $query
                    ->whereHas('category', fn ($query) => $query
                        ->whereNot('name->es', 'Libros')
                        ->where('type', 'Recursos')
                    )
                ),
        ];
    }
}
