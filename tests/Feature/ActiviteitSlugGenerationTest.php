<?php

namespace Tests\Feature;

use App\Enums\ActiviteitStatus;
use App\Enums\Categorie;
use App\Enums\Soort;
use App\Filament\Resources\ActiviteitResource\Pages\CreateActiviteit;
use App\Models\Activiteit;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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
            'locatie_nl' => 'Tour et Taxis',
            'locatie_fr' => 'Tour et Taxis',
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
            'locatie_nl' => 'Tour et Taxis',
            'locatie_fr' => 'Tour et Taxis',
            'status' => 'gepubliceerd',
            'soort' => Soort::Speciaal,
            'categorie' => Categorie::OpUitstap,
        ]);

        $second = Activiteit::create([
            'titel_nl' => 'Expo Khéops',
            'titel_fr' => 'Expo Khéops',
            'datum' => '2027-04-25',
            'startuur' => '14:30:00',
            'locatie_nl' => 'Tour et Taxis',
            'locatie_fr' => 'Tour et Taxis',
            'status' => 'gepubliceerd',
            'soort' => Soort::Speciaal,
            'categorie' => Categorie::OpUitstap,
        ]);

        $third = Activiteit::create([
            'titel_nl' => 'Expo Khéops',
            'titel_fr' => 'Expo Khéops',
            'datum' => '2028-04-25',
            'startuur' => '14:30:00',
            'locatie_nl' => 'Tour et Taxis',
            'locatie_fr' => 'Tour et Taxis',
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
            'locatie_nl' => 'De Harmonie',
            'locatie_fr' => 'De Harmonie',
            'status' => 'gepubliceerd',
            'soort' => Soort::Speciaal,
            'categorie' => Categorie::Ontmoeting,
        ]);

        $this->assertSame('mijn-eigen-slug', $activiteit->slug);
    }

    public function test_emoji_only_dutch_title_falls_back_to_french_title(): void
    {
        $activiteit = Activiteit::create($this->attributes('🚩 ⛃ 🖌️', 'Jeux de société'));

        $this->assertSame('jeux-de-societe', $activiteit->slug);
    }

    public function test_title_without_sluggable_characters_falls_back_to_generic_slug(): void
    {
        $first = Activiteit::create($this->attributes('🚩 ⛃ 🖌️', '🚩 ⛃ 🖌️'));
        $second = Activiteit::create($this->attributes('?!', '…'));

        $this->assertSame('activiteit', $first->slug);
        $this->assertSame('activiteit-2', $second->slug);
    }

    public function test_emptied_slug_is_regenerated_on_save(): void
    {
        $activiteit = Activiteit::factory()->create(['titel_nl' => 'Petanque']);

        $activiteit->update(['slug' => '']);

        $this->assertSame('petanque', $activiteit->fresh()->slug);
    }

    public function test_agenda_renders_activity_with_emoji_only_title_created_in_admin(): void
    {
        $this->seed(AdminUserSeeder::class);

        Livewire::actingAs(User::where('email', config('auth.admin_email'))->firstOrFail())
            ->test(CreateActiviteit::class)
            ->fillForm([
                'titel_nl' => '🚩 ⛃ 🖌️',
                'titel_fr' => '🚩 ⛃ 🖌️',
                'datum' => now()->toDateString(),
                'startuur' => '14:00',
                'einduur' => '16:00',
                'locatie_nl' => 'De Harmonie',
                'locatie_fr' => 'De Harmonie',
                'categorie' => Categorie::Ontmoeting->value,
                'status' => ActiviteitStatus::Gepubliceerd->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        foreach (['/nl/activiteiten/agenda?week=0', '/fr/activites/agenda?week=0', '/nl', '/fr', '/nl/activiteiten', '/fr/activites'] as $url) {
            $this->get($url)->assertOk();
        }

        $activiteit = Activiteit::where('titel_nl', '🚩 ⛃ 🖌️')->firstOrFail();
        $this->assertSame('activiteit', $activiteit->slug);
        $this->get(route('nl.activiteiten.show', $activiteit->slug))->assertOk();
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(string $titelNl, string $titelFr): array
    {
        return [
            'titel_nl' => $titelNl,
            'titel_fr' => $titelFr,
            'datum' => now()->toDateString(),
            'startuur' => '10:00:00',
            'locatie_nl' => 'De Harmonie',
            'locatie_fr' => 'De Harmonie',
            'status' => 'gepubliceerd',
            'soort' => Soort::Speciaal,
            'categorie' => Categorie::Ontmoeting,
        ];
    }
}
