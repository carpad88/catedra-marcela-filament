<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Visibility: string implements HasColor, HasLabel
{
    case Public = 'public';
    case Private = 'private';
    case Group = 'group';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Public => 'Público',
            self::Private => 'Privado',
            self::Group => 'Grupo',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Public => 'primary',
            self::Private => 'gray',
            self::Group => 'info',
        };
    }
}
