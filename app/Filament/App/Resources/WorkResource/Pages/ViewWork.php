<?php

namespace App\Filament\App\Resources\WorkResource\Pages;

use App\Filament\App\Resources\WorkResource;
use Filament\Resources\Pages\ViewRecord;

class ViewWork extends ViewRecord
{
    protected static string $resource = WorkResource::class;

    protected static string $view = 'filament.app.resources.work.pages.view-work';
}
