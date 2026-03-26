<?php

namespace Tests\Feature;

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

    public function test_overview_page_shows_reeksen(): void
    {
        $reeks = ActiviteitTemplate::factory()->create([
            'titel_nl' => 'Yoga op dinsdag',
            'dag_van_de_week' => 2,
            'startuur' => '10:00:00',
        ]);

        $response = $this->get('/activiteiten');
        $response->assertStatus(200);
        $response->assertSee('Yoga op dinsdag');
    }

    public function test_overview_page_loads_for_fr(): void
    {
        $response = $this->get('/fr/activites');
        $response->assertStatus(200);
    }

    public function test_overview_page_passes_bijzondere_activiteiten_to_view(): void
    {
        // A special activiteit (template_id IS NULL, future date, published)
        $special = \App\Models\Activiteit::factory()->create([
            'template_id' => null,
            'datum' => now()->addDays(5)->format('Y-m-d'),
            'status' => 'gepubliceerd',
            'titel_nl' => 'Speciale uitstap',
        ]);

        // A recurring activiteit (template_id IS NOT NULL) — should NOT appear
        $template = \App\Models\ActiviteitTemplate::factory()->create();
        $recurring = \App\Models\Activiteit::factory()->create([
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
}
