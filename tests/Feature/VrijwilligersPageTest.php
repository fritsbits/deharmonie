<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VrijwilligersPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_nl_volunteer_page_loads(): void
    {
        $response = $this->get('/vrijwilligers');

        $response->assertStatus(200);
        $response->assertSee('Word deel van ons team');
        $response->assertSee('Jij kent de buurt het best');
        $response->assertSee('info@deharmonie.be');
    }

    public function test_fr_volunteer_page_loads(): void
    {
        $response = $this->get('/fr/benevoles');

        $response->assertStatus(200);
        $response->assertSee('Rejoignez notre équipe');
        $response->assertSee('Vous connaissez mieux votre quartier');
        $response->assertSee('info@deharmonie.be');
    }

    public function test_nl_volunteer_page_shows_activity_roles(): void
    {
        $response = $this->get('/vrijwilligers');

        $response->assertSee('Ciné-Club');
        $response->assertSee('Conversatietafel');
        $response->assertSee('Activiteitsdagen');
    }

    public function test_footer_contains_volunteer_link(): void
    {
        $response = $this->get('/');

        $response->assertSee('vrijwilligers');
    }

    public function test_vrijwilligers_page_shows_section_nav(): void
    {
        $response = $this->get(route('nl.vrijwilligers'));

        $response->assertSee('#3a68a8');
    }

    public function test_over_ons_page_shows_section_nav(): void
    {
        $response = $this->get(route('nl.over-ons'));

        $response->assertSee('#3a68a8');
    }

    public function test_homepage_does_not_show_section_nav(): void
    {
        $response = $this->get('/');

        $response->assertDontSee('#3a68a8');
    }
}
