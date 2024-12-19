<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasColor, HasIcon, HasLabel
{
    case Active = 'active';
    case Archived = 'archived';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => 'Activo',
            self::Archived => 'Archivado',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Active => 'primary',
            self::Archived => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Active => 'heroicon-o-check-circle',
            self::Archived => 'heroicon-o-archive-box',
        };
    }
}
