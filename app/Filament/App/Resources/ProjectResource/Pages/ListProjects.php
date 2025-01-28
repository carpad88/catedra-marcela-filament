<?php

namespace App\Filament\App\Resources\ProjectResource\Pages;

use App\Filament\App\Resources\ProjectResource;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    public static string $view = 'filament.app.resources.project.pages.list-projects';

    protected ?string $heading = 'Proyectos a desarrollar';
}
