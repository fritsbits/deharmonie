<?php

namespace Tests\Feature;

use App\Models\Activiteit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_404_renders_in_nl_for_unknown_path(): void
    {
        $response = $this->get('/this-path-does-not-exist');

        $response->assertStatus(404);
        $response->assertSee('Pagina niet gevonden');
        $response->assertSee('We helpen je verder');
        $response->assertSee('Bekijk de agenda');
        $response->assertSee('Het weekmenu');
        $response->assertSee('Kom eens langs');
    }

    public function test_404_renders_in_fr_for_unknown_fr_path(): void
    {
        $response = $this->get('/fr/cette-page-nexiste-pas');

        $response->assertStatus(404);
        $response->assertSee('Page introuvable');
        $response->assertSee('On vous aide à retrouver votre chemin', false);
        $response->assertSee('Voir toutes les activités', false);
        $response->assertSee('Le menu de la semaine');
        $response->assertSee('Venez nous voir');
        $response->assertDontSee('Pagina niet gevonden');
    }

    public function test_404_links_to_localized_routes(): void
    {
        $nlResponse = $this->get('/missing');
        $nlResponse->assertSee('href="'.route('nl.activiteiten.index').'"', false);
        $nlResponse->assertSee('href="'.route('nl.weekmenu').'"', false);
        $nlResponse->assertSee('href="'.route('nl.contact').'"', false);

        $frResponse = $this->get('/fr/manquant');
        $frResponse->assertSee('href="'.route('fr.activiteiten.index').'"', false);
        $frResponse->assertSee('href="'.route('fr.weekmenu').'"', false);
        $frResponse->assertSee('href="'.route('fr.contact').'"', false);
    }

    public function test_404_surfaces_next_upcoming_activity_when_one_exists(): void
    {
        Activiteit::factory()->create([
            'titel_nl' => 'Bingoavond',
            'titel_fr' => 'Soirée bingo',
            'status' => 'gepubliceerd',
            'datum' => now()->addDays(3)->toDateString(),
        ]);

        $this->get('/missing')->assertSee('Bingoavond');
        $this->get('/fr/manquant')->assertSee('Soirée bingo', false);
    }

    public function test_404_falls_back_when_no_upcoming_activity(): void
    {
        $this->get('/missing')->assertSee('Cursussen, workshops en uitstappen');
        $this->get('/fr/manquant')->assertSee('Cours, ateliers et sorties', false);
    }

    public function test_500_view_renders_both_languages_with_brand_band(): void
    {
        $view = view('errors.500')->render();

        $this->assertStringContainsString('Er ging iets mis', $view);
        $this->assertStringContainsString('Quelque chose a mal tourné', $view);
        $this->assertStringContainsString('/images/logo.png', $view);
        $this->assertStringContainsString('noindex', $view);
    }

    public function test_503_view_renders_both_languages_with_brand_band(): void
    {
        $view = view('errors.503')->render();

        $this->assertStringContainsString('We zijn zo terug', $view);
        $this->assertStringContainsString('Nous revenons bientôt', $view);
        $this->assertStringContainsString('/images/logo.png', $view);
        $this->assertStringContainsString('noindex', $view);
    }
}
