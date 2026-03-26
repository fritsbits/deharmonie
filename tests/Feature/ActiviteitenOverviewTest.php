<?php

namespace Tests\Feature;

use App\Models\Activiteit;
use App\Models\ActiviteitTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiviteitenOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_agenda_page_loads_for_nl(): void
    {
        $response = $this->get('/activiteiten/agenda');
        $response->assertStatus(200);
    }

    public function test_agenda_page_loads_for_fr(): void
    {
        $response = $this->get('/fr/activites/agenda');
        $response->assertStatus(200);
    }

    public function test_overview_page_shows_theme_names(): void
    {
        $response = $this->get('/activiteiten');
        $response->assertStatus(200);
        $response->assertSee('Beweeg mee');
        $response->assertSee('Maak iets');
        $response->assertSeeText('Praat & leer');
        $response->assertSee('Vier mee');
    }

    public function test_overview_page_loads_for_fr(): void
    {
        $response = $this->get('/fr/activites');
        $response->assertStatus(200);
    }

    public function test_overview_page_passes_bijzondere_activiteiten_to_view(): void
    {
        // A special activiteit (template_id IS NULL, future date, published)
        $special = Activiteit::factory()->create([
            'template_id' => null,
            'datum' => now()->addDays(5)->format('Y-m-d'),
            'status' => 'gepubliceerd',
            'titel_nl' => 'Speciale uitstap',
        ]);

        // A recurring activiteit (template_id IS NOT NULL) — should NOT appear
        $template = ActiviteitTemplate::factory()->create();
        $recurring = Activiteit::factory()->create([
            'template_id' => $template->id,
            'datum' => now()->addDays(3)->format('Y-m-d'),
            'status' => 'gepubliceerd',
            'titel_nl' => 'Herhalende activiteit',
        ]);

        $response = $this->get('/activiteiten');
        $response->assertStatus(200);
        $response->assertViewHas('bijzondereActiviteiten');

        $bijzondere = $response->viewData('bijzondereActiviteiten');
        $this->assertTrue($bijzondere->contains('id', $special->id));
        $this->assertFalse($bijzondere->contains('id', $recurring->id));
    }

    public function test_bijzondere_momenten_shows_upcoming_special_activities(): void
    {
        $special = \App\Models\Activiteit::factory()->create([
            'template_id' => null,
            'datum' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'gepubliceerd',
            'titel_nl' => 'Zomerfeest',
        ]);

        // Past activity — should NOT appear
        \App\Models\Activiteit::factory()->create([
            'template_id' => null,
            'datum' => now()->subDay()->format('Y-m-d'),
            'status' => 'gepubliceerd',
            'titel_nl' => 'Oud evenement',
        ]);

        // Draft — should NOT appear
        \App\Models\Activiteit::factory()->create([
            'template_id' => null,
            'datum' => now()->addDays(3)->format('Y-m-d'),
            'status' => 'concept',
            'titel_nl' => 'Ongepubliceerd',
        ]);

        $response = $this->get('/activiteiten');
        $response->assertStatus(200);
        $response->assertSee('Zomerfeest');
        $response->assertDontSee('Oud evenement');
        $response->assertDontSee('Ongepubliceerd');
    }

    public function test_agenda_cta_link_correct_for_nl(): void
    {
        $response = $this->get('/activiteiten');
        $response->assertStatus(200);
        $response->assertSee('/activiteiten/agenda');
    }

    public function test_agenda_cta_link_correct_for_fr(): void
    {
        $response = $this->get('/fr/activites');
        $response->assertStatus(200);
        $response->assertSee('/fr/activites/agenda');
    }
}
