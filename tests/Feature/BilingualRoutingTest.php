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
        $response = $this->get('/nl');
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
        $this->get('/nl/activiteiten/'.$activiteit->slug)->assertStatus(200);
        $this->get('/fr/activites/'.$activiteit->slug)->assertStatus(200);
    }

    public function test_nl_locale_set_on_nl_routes(): void
    {
        $this->get('/nl');
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
        $response->assertSee('À propos');
    }

    public function test_nav_shows_nl_labels_on_nl_routes(): void
    {
        $response = $this->get('/nl');
        $response->assertSee('Activiteiten');
        $response->assertSee('Over ons');
    }

    public function test_nl_nav_shows_fr_as_link(): void
    {
        $response = $this->get('/nl');
        $response->assertSee('set-locale', false);
        $response->assertSee('>FR<', false);
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
        $response->assertSee('Facebook');
    }

    public function test_old_unprefixed_url_returns_404(): void
    {
        $this->get('/activiteiten')->assertStatus(404);
    }

    public function test_fr_prefix_with_nl_slug_returns_404(): void
    {
        // /fr/activiteiten doesn't exist — only /fr/activites does
        $this->get('/fr/activiteiten')->assertStatus(404);
    }
}
