<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DagVanDeWeek: int implements HasLabel
{
    case Zondag = 0;
    case Maandag = 1;
    case Dinsdag = 2;
    case Woensdag = 3;
    case Donderdag = 4;
    case Vrijdag = 5;
    case Zaterdag = 6;

    public function getLabel(): string
    {
        return match ($this) {
            self::Zondag => 'Zondag',
            self::Maandag => 'Maandag',
            self::Dinsdag => 'Dinsdag',
            self::Woensdag => 'Woensdag',
            self::Donderdag => 'Donderdag',
            self::Vrijdag => 'Vrijdag',
            self::Zaterdag => 'Zaterdag',
        };
    }
}
