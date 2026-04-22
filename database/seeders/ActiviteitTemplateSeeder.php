<?php

namespace Database\Seeders;

use App\Enums\Interesse;
use App\Models\ActiviteitTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActiviteitTemplateSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        ActiviteitTemplate::truncate();

        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $start = today();
        $einde = today()->addMonths(3);

        $templates = [
            [
                'titel_nl' => 'Conversatietafel Spaans',
                'titel_fr' => 'Table de conversation Espagnole',
                'dag_van_de_week' => 4,
                'startuur' => '10:00:00',
                'einduur' => '12:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Conversatietafel Engels',
                'titel_fr' => 'Table de Conversation Anglais',
                'dag_van_de_week' => 2,
                'startuur' => '10:30:00',
                'einduur' => null,
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Conversatietafel Italiaans',
                'titel_fr' => 'Table de Conversation Italien',
                'dag_van_de_week' => 1,
                'startuur' => '11:30:00',
                'einduur' => '12:30:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Nederlandse conversatietafel',
                'titel_fr' => 'Table de Conversation Néerlandais',
                'dag_van_de_week' => 5,
                'startuur' => '10:30:00',
                'einduur' => '11:30:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Country Line Dance',
                'titel_fr' => 'Country Dance en Ligne',
                'dag_van_de_week' => 4,
                'startuur' => '14:00:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => 2.00,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Geheugenatelier',
                'titel_fr' => 'Atelier de Mémoire',
                'dag_van_de_week' => 1,
                'startuur' => '13:30:00',
                'einduur' => '15:15:00',
                'locatie' => 'De Harmonie',
                'prijs' => 1.00,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Stoel-gym met Nicole',
                'titel_fr' => 'Gym sur chaise avec Nicole',
                'dag_van_de_week' => 1,
                'startuur' => '11:00:00',
                'einduur' => null,
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Digitale workshop',
                'titel_fr' => 'Atelier Numérique',
                'dag_van_de_week' => 3,
                'startuur' => '14:00:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Bingo',
                'titel_fr' => 'Bingo',
                'dag_van_de_week' => 3,
                'startuur' => '13:30:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => 1.00,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Creativiteit workshop',
                'titel_fr' => 'Atelier de Créativité',
                'dag_van_de_week' => 1,
                'startuur' => '14:00:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Zumba',
                'titel_fr' => 'Zumba',
                'dag_van_de_week' => 5,
                'startuur' => '14:00:00',
                'einduur' => '15:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => 1.00,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Diamond Painting Workshop met Nadia',
                'titel_fr' => 'Atelier de Diamond Painting avec Nadia',
                'dag_van_de_week' => 5,
                'startuur' => '14:00:00',
                'einduur' => null,
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Naaiworkshop',
                'titel_fr' => 'Atelier de Couture',
                'dag_van_de_week' => 3,
                'startuur' => '13:30:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => 1.00,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Boodschappendienst',
                'titel_fr' => 'Service de Courses',
                'dag_van_de_week' => 1,
                'startuur' => '14:00:00',
                'einduur' => null,
                'locatie' => 'De Harmonie',
                'prijs' => 2.50,
                'interesse' => Interesse::Diensten,
            ],
            [
                'titel_nl' => 'Pilates & Fitness',
                'titel_fr' => 'Pilates & Fitness',
                'dag_van_de_week' => 5,
                'startuur' => '11:00:00',
                'einduur' => null,
                'locatie' => 'Pôle Nord',
                'prijs' => 1.00,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Sociale infopunt',
                'titel_fr' => 'Point d\'info sociale',
                'dag_van_de_week' => 3,
                'startuur' => '11:00:00',
                'einduur' => '14:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Diensten,
            ],
            [
                'titel_nl' => 'Jeu de Tables: Dominos',
                'titel_fr' => 'Jeu de Tables: Dominos',
                'dag_van_de_week' => 5,
                'startuur' => '13:30:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Jeu de Tables: Jacquet',
                'titel_fr' => 'Jeu de Tables: Jacquet',
                'dag_van_de_week' => 1,
                'startuur' => '13:30:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
        ];

        foreach ($templates as $data) {
            ActiviteitTemplate::create(array_merge($data, [
                'reeks_start' => $start,
                'reeks_einde' => $einde,
            ]));
        }

        $this->command->info('Seeded '.count($templates).' activity templates.');
    }
}
