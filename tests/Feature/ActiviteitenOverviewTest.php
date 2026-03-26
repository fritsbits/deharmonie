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
}
