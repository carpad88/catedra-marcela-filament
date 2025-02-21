<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum RootTagsEnum: string implements HasLabel
{
    case Projects = 'projects';
    case Resources = 'resources';
    case Articles = 'articles';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Projects => 'Proyectos',
            self::Resources => 'Recursos',
            self::Articles => 'Artículos',
        };
    }
}
