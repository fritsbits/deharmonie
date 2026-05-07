<?php

use App\Models\OverOnsContent;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $this->seedContent();
    }

    public function down(): void
    {
        // Data migration: leave the row in place if rolled back.
    }

    public function seedContent(): void
    {
        $content = OverOnsContent::firstOrCreate(['id' => 1], [
            'jaarverslag_jaar' => 2025,
            'impact_1_aantal' => '250',
            'impact_1_omschrijving_nl' => 'wekelijks bij ons over de vloer',
            'impact_1_omschrijving_fr' => 'chaque semaine chez nous',
            'impact_2_aantal' => '4500',
            'impact_2_omschrijving_nl' => 'maaltijden per maand',
            'impact_2_omschrijving_fr' => 'repas par mois',
            'impact_3_aantal' => '60+',
            'impact_3_omschrijving_nl' => 'activiteiten per jaar',
            'impact_3_omschrijving_fr' => 'activités par an',
        ]);

        $sourcePdf = public_path('docs/jaarverslag-2025.pdf');

        if (file_exists($sourcePdf) && $content->getMedia('jaarverslag')->isEmpty()) {
            $content->addMedia($sourcePdf)
                ->preservingOriginal()
                ->toMediaCollection('jaarverslag');

            @unlink($sourcePdf);
        }
    }
};
