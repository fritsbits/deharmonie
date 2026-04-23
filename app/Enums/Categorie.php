<?php

namespace App\Enums;

use App\Support\CategorieIcons;
use Filament\Support\Contracts\HasLabel;

enum Categorie: string implements HasLabel
{
    case SportBeweging = 'sport_beweging';
    case Creatief = 'creatief';
    case Bijleren = 'bijleren';
    case Ontmoeting = 'ontmoeting';
    case Spelletjes = 'spelletjes';
    case Culinair = 'culinair';
    case FilmMuziek = 'film_muziek';
    case OpUitstap = 'op_uitstap';

    public function getLabel(): string
    {
        return match ($this) {
            self::SportBeweging => 'Sport & beweging',
            self::Creatief => 'Creatief',
            self::Bijleren => 'Bijleren',
            self::Ontmoeting => 'Ontmoeting',
            self::Spelletjes => 'Spelletjes',
            self::Culinair => 'Culinair',
            self::FilmMuziek => 'Film & muziek',
            self::OpUitstap => 'Op uitstap',
        };
    }

    public function labelFr(): string
    {
        return match ($this) {
            self::SportBeweging => 'Sport & mouvement',
            self::Creatief => 'Créatif',
            self::Bijleren => 'Apprendre',
            self::Ontmoeting => 'Rencontre',
            self::Spelletjes => 'Jeux',
            self::Culinair => 'Culinaire',
            self::FilmMuziek => 'Cinéma & musique',
            self::OpUitstap => 'En sortie',
        };
    }

    /**
     * Section identifier used for grouping on the public overzicht page.
     */
    public function section(): string
    {
        return match ($this) {
            self::SportBeweging => 'beweeg',
            self::Creatief, self::Bijleren => 'maak_leer',
            self::Ontmoeting, self::Spelletjes, self::Culinair, self::FilmMuziek, self::OpUitstap => 'ontmoet_beleef',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::SportBeweging => CategorieIcons::sportBeweging(),
            self::Creatief => CategorieIcons::creatief(),
            self::Bijleren => CategorieIcons::bijleren(),
            self::Ontmoeting => CategorieIcons::ontmoeting(),
            self::Spelletjes => CategorieIcons::spelletjes(),
            self::Culinair => CategorieIcons::culinair(),
            self::FilmMuziek => CategorieIcons::filmMuziek(),
            self::OpUitstap => CategorieIcons::opUitstap(),
        };
    }
}
