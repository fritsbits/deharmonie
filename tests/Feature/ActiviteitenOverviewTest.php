<?php

namespace Tests\Feature;

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
}
