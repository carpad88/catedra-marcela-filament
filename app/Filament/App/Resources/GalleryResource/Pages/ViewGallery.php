<?php

namespace App\Filament\App\Resources\GalleryResource\Pages;

use App\Filament\App\Resources\GalleryResource;
use Filament\Resources\Pages\ViewRecord;

class ViewGallery extends ViewRecord
{
    protected static string $resource = GalleryResource::class;

    public static string $view = 'filament.app.resources.gallery.pages.view-gallery';

    public function getRecord(): \Illuminate\Database\Eloquent\Model
    {
        return parent::getRecord()->load(['works']);
    }
}
