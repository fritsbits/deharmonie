<?php

namespace Database\Seeders;

use App\Models\TeamCategorie;
use App\Models\TeamLid;
use Illuminate\Database\Seeder;

class TeamCategorieSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            [
                'naam_nl' => 'Onthaal & Animatie',
                'naam_fr' => 'Accueil & Animation',
                'volgorde' => 1,
                'leden' => ['Deborah Monfils', 'Arnaud Petit', 'Nicolas Van den Eede', 'Peter Kern'],
            ],
            [
                'naam_nl' => 'Keuken – Chefs & Instructeurs',
                'naam_fr' => 'Cuisine – Chefs & Instructeurs',
                'volgorde' => 2,
                'leden' => ['Claude Muaka', 'Pernelle Mbawu'],
            ],
            [
                'naam_nl' => 'Zaal – Instructeur',
                'naam_fr' => 'Salle – Instructeur',
                'volgorde' => 3,
                'leden' => ['Gonard Matondo'],
            ],
            [
                'naam_nl' => 'Keuken- & Zaalassistenten',
                'naam_fr' => 'Assistants Cuisine & Salle',
                'volgorde' => 4,
                'leden' => [
                    'Agnes Kalonda-Mbiye', 'Hassna Boumediane', 'Japhet Mawanda Nzukum',
                    'Mohamed Dahmani', 'Mohammad Malikzai Lal', 'Rapten Tenzin',
                    'Sahara Ahmed', 'Shafahat Mallakhel', 'Tarakhel Kefayatullah',
                ],
            ],
            [
                'naam_nl' => 'Transport & Onderhoud',
                'naam_fr' => 'Transport & Entretien',
                'volgorde' => 5,
                'leden' => ['Omid Arabzai', 'Eduardo Manzoangani'],
            ],
            [
                'naam_nl' => 'Poetsdienst',
                'naam_fr' => 'Service de nettoyage',
                'volgorde' => 6,
                'leden' => ['Nadine Abeng Evouna', 'John Saquee'],
            ],
            [
                'naam_nl' => 'Boekhouding & Administratie',
                'naam_fr' => 'Comptabilité & Administration',
                'volgorde' => 7,
                'leden' => ['Nancy Jacobs'],
            ],
            [
                'naam_nl' => 'Coördinatie',
                'naam_fr' => 'Coordination',
                'volgorde' => 8,
                'leden' => ['Cynthia Spijker'],
            ],
            [
                'naam_nl' => 'Bestuursorgaan',
                'naam_fr' => "Organe d'administration",
                'volgorde' => 9,
                'leden' => [
                    'Jan Vandekerckhove', 'Maarten Janssens', 'Sebastiano Cincinnato',
                    'Isabelle De Meyere', 'Relinde Raeymakers', 'Linda Struelens', 'Inge Verhaegen',
                ],
            ],
            [
                'naam_nl' => 'Wijkraad',
                'naam_fr' => 'Conseil de quartier',
                'volgorde' => 10,
                'leden' => [
                    'Jan Vandekerckhove', 'Maarten Janssens', 'Karen De Cooman',
                    'Mohamed El Morabit', 'Carine Haelemeersch', 'Bianca Laurino',
                    'Peter Vandenbempt', 'Léopold Vodak',
                ],
            ],
        ];

        foreach ($teams as $team) {
            $categorie = TeamCategorie::create([
                'naam_nl' => $team['naam_nl'],
                'naam_fr' => $team['naam_fr'],
                'volgorde' => $team['volgorde'],
            ]);

            foreach ($team['leden'] as $volgorde => $naam) {
                TeamLid::create([
                    'team_categorie_id' => $categorie->id,
                    'naam' => $naam,
                    'volgorde' => $volgorde,
                ]);
            }
        }
    }
}
