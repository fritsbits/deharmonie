<?php

namespace Tests\Feature;

use App\Enums\Categorie;
use App\Enums\Soort;
use App\Models\Activiteit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiviteitSlugGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_is_generated_from_title_when_empty(): void
    {
        $activiteit = Activiteit::create([
            'titel_nl' => 'Expo Khéops',
            'titel_fr' => 'Expo Khéops',
            'datum' => '2026-04-25',
            'startuur' => '14:30:00',
            'locatie' => 'Tour et Taxis',
            'status' => 'gepubliceerd',
            'soort' => Soort::Speciaal,
            'categorie' => Categorie::OpUitstap,
        ]);

        $this->assertSame('expo-kheops', $activiteit->slug);
    }

    public function test_duplicate_title_gets_incremented_suffix(): void
    {
        Activiteit::create([
            'titel_nl' => 'Expo Khéops',
            'titel_fr' => 'Expo Khéops',
            'datum' => '2026-04-25',
            'startuur' => '14:30:00',
            'locatie' => 'Tour et Taxis',
            'status' => 'gepubliceerd',
            'soort' => Soort::Speciaal,
            'categorie' => Categorie::OpUitstap,
        ]);

        $second = Activiteit::create([
            'titel_nl' => 'Expo Khéops',
            'titel_fr' => 'Expo Khéops',
            'datum' => '2027-04-25',
            'startuur' => '14:30:00',
            'locatie' => 'Tour et Taxis',
            'status' => 'gepubliceerd',
            'soort' => Soort::Speciaal,
            'categorie' => Categorie::OpUitstap,
        ]);

        $third = Activiteit::create([
            'titel_nl' => 'Expo Khéops',
            'titel_fr' => 'Expo Khéops',
            'datum' => '2028-04-25',
            'startuur' => '14:30:00',
            'locatie' => 'Tour et Taxis',
            'status' => 'gepubliceerd',
            'soort' => Soort::Speciaal,
            'categorie' => Categorie::OpUitstap,
        ]);

        $this->assertSame('expo-kheops-2', $second->slug);
        $this->assertSame('expo-kheops-3', $third->slug);
    }

    public function test_explicit_slug_is_preserved(): void
    {
        $activiteit = Activiteit::create([
            'slug' => 'mijn-eigen-slug',
            'titel_nl' => 'Andere titel',
            'titel_fr' => 'Autre titre',
            'datum' => '2026-05-01',
            'startuur' => '10:00:00',
            'locatie' => 'De Harmonie',
            'status' => 'gepubliceerd',
            'soort' => Soort::Speciaal,
            'categorie' => Categorie::Ontmoeting,
        ]);

        $this->assertSame('mijn-eigen-slug', $activiteit->slug);
    }
}
