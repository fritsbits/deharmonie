<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ActiviteitStatus: string implements HasColor, HasLabel
{
    case Concept = 'concept';
    case Gepubliceerd = 'gepubliceerd';
    case Geannuleerd = 'geannuleerd';

    public function getLabel(): string
    {
        return match ($this) {
            self::Concept => 'Concept',
            self::Gepubliceerd => 'Gepubliceerd',
            self::Geannuleerd => 'Geannuleerd',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Concept => 'gray',
            self::Gepubliceerd => 'success',
            self::Geannuleerd => 'danger',
        };
    }
}
