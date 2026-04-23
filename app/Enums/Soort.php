<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Soort: string implements HasLabel
{
    case Vast = 'vast';
    case Speciaal = 'speciaal';

    public function getLabel(): string
    {
        return match ($this) {
            self::Vast => 'Vast',
            self::Speciaal => 'Speciaal',
        };
    }
}
