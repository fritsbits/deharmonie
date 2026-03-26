<?php

namespace Tests\Feature;

use App\Models\Activiteit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BilingualRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_in_nl(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Activiteiten');
    }

    public function test_homepage_loads_in_fr(): void
    {
        $response = $this->get('/fr/activites');
        $response->assertStatus(200);
        $response->assertSee('Activités');
    }

    public function test_activity_detail_resolves_by_slug(): void
    {
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);
        $this->get('/activiteiten/'.$activiteit->slug)->assertStatus(200);
        $this->get('/fr/activites/'.$activiteit->slug)->assertStatus(200);
    }

    public function test_nl_locale_set_on_default_routes(): void
    {
        $this->get('/');
        $this->assertEquals('nl', app()->getLocale());
    }

    public function test_fr_locale_set_on_fr_routes(): void
    {
        $this->get('/fr/activites');
        $this->assertEquals('fr', app()->getLocale());
    }

    public function test_nav_shows_fr_labels_on_fr_routes(): void
    {
        $response = $this->get('/fr');
        $response->assertSee('Activités');
        $response->assertSee('Services');
    }

    public function test_nav_shows_nl_labels_on_nl_routes(): void
    {
        $response = $this->get('/');
        $response->assertSee('Activiteiten');
        $response->assertSee('Diensten');
    }

    public function test_nl_nav_shows_fr_as_link(): void
    {
        $response = $this->get('/');
        // FR is the clickable "other" language on the NL site
        $response->assertSee('set-locale', false);
        $response->assertSee('>FR<', false);
        // NL appears as a non-linked span
        $response->assertSee('>NL<', false);
    }

    public function test_fr_nav_shows_nl_as_link(): void
    {
        $response = $this->get('/fr');
        $response->assertSee('set-locale', false);
        $response->assertSee('>NL<', false);
        $response->assertSee('>FR<', false);
    }

    public function test_footer_shows_fr_labels_on_fr_routes(): void
    {
        $response = $this->get('/fr');
        $response->assertSee('Avec le soutien de');
        $response->assertSee('Suivez De Harmonie sur Facebook');
    }
}
