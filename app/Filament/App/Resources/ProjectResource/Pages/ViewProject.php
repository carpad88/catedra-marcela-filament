<?php

namespace App\Filament\App\Resources\ProjectResource\Pages;

use App\Filament\App\Resources\ProjectResource;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.app.resources.project.pages.view-project';

    public function getRecord(): \Illuminate\Database\Eloquent\Model
    {
        return parent::getRecord()->load(['category', 'groups']);
    }
}
