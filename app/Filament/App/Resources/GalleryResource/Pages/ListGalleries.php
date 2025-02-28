<?php

namespace App\Filament\App\Resources\GalleryResource\Pages;

use App\Filament\App\Resources\GalleryResource;
use App\Models\Tag;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Collection;

class ListGalleries extends ListRecords
{
    protected static string $resource = GalleryResource::class;

    public static string $view = 'filament.app.resources.gallery.pages.list-galleries';

    public Collection|array $categories = [];

    protected static ?string $title = 'Galería';

    public function mount(): void
    {
        $this->categories = Tag::with(['projects' => fn ($query) => $query->inRandomOrder()->limit(1)])
            ->withType('proyectos')
            ->get();
    }
}
