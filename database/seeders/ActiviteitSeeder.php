<?php

namespace Database\Seeders;

use App\Models\Activiteit;
use Illuminate\Database\Seeder;

class ActiviteitSeeder extends Seeder
{
    public function run(): void
    {
        $activiteiten = [
            [
                'slug' => 'engelse-conversatietafel-2026-04',
                'titel_nl' => 'Engelse conversatietafel',
                'titel_fr' => 'Table de conversation anglaise',
                'beschrijving_nl' => '<p>Oefen je Engels in een gezellige groep. Voor alle niveaus.</p>',
                'beschrijving_fr' => '<p>Pratiquez votre anglais dans un groupe convivial. Tous niveaux.</p>',
                'datum' => '2026-04-07',
                'startuur' => '10:30:00',
                'einduur' => '12:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'status' => 'gepubliceerd',
            ],
            [
                'slug' => 'spaanse-conversatietafel-2026-04',
                'titel_nl' => 'Spaanse conversatietafel',
                'titel_fr' => 'Table de conversation espagnole',
                'beschrijving_nl' => '<p>Oefen je Spaans met andere enthousiastelingen.</p>',
                'beschrijving_fr' => '<p>Pratiquez votre espagnol avec d\'autres passionnés.</p>',
                'datum' => '2026-04-09',
                'startuur' => '10:00:00',
                'einduur' => '12:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'status' => 'gepubliceerd',
            ],
            [
                'slug' => 'country-line-dance-2026-04',
                'titel_nl' => 'Country Line Dance',
                'titel_fr' => 'Country Line Dance',
                'beschrijving_nl' => '<p>Dansen voor iedereen! Geen ervaring nodig.</p>',
                'beschrijving_fr' => '<p>La danse pour tous ! Aucune expérience requise.</p>',
                'datum' => '2026-04-09',
                'startuur' => '14:00:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'status' => 'gepubliceerd',
            ],
            [
                'slug' => 'naaiworkshop-2026-04',
                'titel_nl' => 'Naaiworkshop',
                'titel_fr' => 'Atelier couture',
                'beschrijving_nl' => '<p>Creatief naaien voor beginners en gevorderden.</p>',
                'beschrijving_fr' => '<p>Couture créative pour débutants et confirmés.</p>',
                'datum' => '2026-04-15',
                'startuur' => '13:30:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'status' => 'gepubliceerd',
            ],
            [
                'slug' => 'zumba-2026-04',
                'titel_nl' => 'Zumba',
                'titel_fr' => 'Zumba',
                'beschrijving_nl' => '<p>Beweeg mee op Latijns-Amerikaanse muziek. Lekker energiek!</p>',
                'beschrijving_fr' => '<p>Bougez sur des rythmes latino-américains. Super énergisant!</p>',
                'datum' => '2026-04-17',
                'startuur' => '14:00:00',
                'einduur' => '15:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'status' => 'gepubliceerd',
            ],
            [
                'slug' => 'sociaal-infopunt-2026-04',
                'titel_nl' => 'Sociaal infopunt',
                'titel_fr' => 'Point d\'information social',
                'beschrijving_nl' => '<p>Vragen over rechten, uitkeringen of administratie? Kom langs!</p>',
                'beschrijving_fr' => '<p>Questions sur vos droits, allocations ou démarches administratives ? Venez!</p>',
                'datum' => '2026-04-22',
                'startuur' => '11:00:00',
                'einduur' => '14:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'status' => 'gepubliceerd',
            ],
            [
                'slug' => 'diamantschilderen-2026-04',
                'titel_nl' => 'Diamantschilderen',
                'titel_fr' => 'Diamond Painting',
                'beschrijving_nl' => '<p>Ontspannend en creatief werken met glinsterende steentjes.</p>',
                'beschrijving_fr' => '<p>Travail créatif et relaxant avec des petites pierres brillantes.</p>',
                'datum' => '2026-04-24',
                'startuur' => '14:00:00',
                'einduur' => null,
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'status' => 'gepubliceerd',
            ],
            [
                'slug' => 'italiaanstalige-conversatietafel-2026-04',
                'titel_nl' => 'Italiaanse conversatietafel',
                'titel_fr' => 'Table de conversation italienne',
                'beschrijving_nl' => '<p>Oefen je Italiaans in een leuke, informele sfeer.</p>',
                'beschrijving_fr' => '<p>Pratiquez votre italien dans une ambiance détendue.</p>',
                'datum' => '2026-04-28',
                'startuur' => '11:30:00',
                'einduur' => '12:30:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'status' => 'gepubliceerd',
            ],
        ];

        foreach ($activiteiten as $data) {
            Activiteit::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
