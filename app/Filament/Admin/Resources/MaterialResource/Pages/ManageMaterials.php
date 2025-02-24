<?php

namespace App\Filament\Admin\Resources\MaterialResource\Pages;

use App\Filament\Admin\Resources\MaterialResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;

class ManageMaterials extends ManageRecords
{
    protected static string $resource = MaterialResource::class;

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
