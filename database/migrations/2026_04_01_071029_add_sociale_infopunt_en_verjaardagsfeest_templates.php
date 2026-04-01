<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('activiteit_templates')->insert([
            [
                'titel_nl'       => 'Sociale infopunt',
                'titel_fr'       => 'Point d\'info sociale',
                'dag_van_de_week' => 3,
                'startuur'       => '11:00:00',
                'einduur'        => '14:00:00',
                'locatie'        => 'De Harmonie',
                'reeks_start'    => '2026-03-31',
                'reeks_einde'    => '2026-07-01',
                'interesse'      => 'activiteiten',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'titel_nl'       => 'Maandelijks verjaardagsfeest',
                'titel_fr'       => 'Fête d\'anniversaire mensuelle',
                'dag_van_de_week' => 3,
                'startuur'       => '14:00:00',
                'einduur'        => null,
                'locatie'        => 'De Harmonie',
                'reeks_start'    => '2026-03-31',
                'reeks_einde'    => '2026-07-01',
                'interesse'      => 'activiteiten',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('activiteit_templates')->whereIn('titel_nl', [
            'Sociale infopunt',
            'Maandelijks verjaardagsfeest',
        ])->delete();
    }
};
