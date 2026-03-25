<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Interesse: string implements HasLabel
{
    case Activiteiten = 'activiteiten';
    case Diensten = 'diensten';
    case LevenInDeNoorwijk = 'leven_in_de_noordwijk';
    case SociaalRestaurant = 'sociaal_restaurant';
    case UitstappenEnVakanties = 'uitstappen_en_vakanties';

    public function getLabel(): string
    {
        return match ($this) {
            self::Activiteiten => 'Activiteiten',
            self::Diensten => 'Diensten',
            self::LevenInDeNoorwijk => 'Leven in de Noordwijk',
            self::SociaalRestaurant => 'Sociaal restaurant',
            self::UitstappenEnVakanties => 'Uitstappen en vakanties',
        };
    }

    public function labelFr(): string
    {
        return match ($this) {
            self::Activiteiten => 'Activités à De Harmonie et dans Bruxelles',
            self::Diensten => 'Centre de Services à De Harmonie et à Domicile',
            self::LevenInDeNoorwijk => 'La Vie dans le Quartier Nord',
            self::SociaalRestaurant => 'Restaurant Social (Menu, Fêtes, Evénements,..)',
            self::UitstappenEnVakanties => 'Excursions et vacances',
        };
    }

    public function thumbnail(): string
    {
        return 'images/interesses/'.$this->value.'.png';
    }

    public function thumbnailUrl(): string
    {
        return asset($this->thumbnail());
    }
}
