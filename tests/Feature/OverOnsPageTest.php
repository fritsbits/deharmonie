<?php

namespace Tests\Feature;

use App\Models\OverOnsContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OverOnsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_nl_over_ons_page_renders(): void
    {
        $response = $this->get(route('nl.over-ons'));

        $response->assertStatus(200);
        $response->assertSee('Vijftig jaar hart voor de Noordwijk');
        $response->assertSee('Een buurtplek in Brussel');
        $response->assertSee('Ons verhaal');
        $response->assertSee('Een thuis in de Noordwijk');
        $response->assertSee('piliers du quartier');
        $response->assertSee('Josiane C.');
        $response->assertSee('Ontmoet het team');
        $response->assertSee('Benieuwd hoe het eruitziet');
    }

    public function test_fr_over_ons_page_renders(): void
    {
        $response = $this->get(route('fr.over-ons'));

        $response->assertStatus(200);
        $response->assertSee('Cinquante ans au cœur du Noordwijk');
        $response->assertSee('Un lieu de rencontre');
        $response->assertSee('Notre histoire');
        $response->assertSee('Un chez-soi dans le Noordwijk');
        $response->assertSee('piliers du quartier');
        $response->assertSee('Josiane C.');
        $response->assertSee('Rencontrez l\'équipe');
        $response->assertSee('Curieux de voir');
    }

    public function test_over_ons_links_to_wie_is_wie(): void
    {
        $response = $this->get(route('nl.over-ons'));

        $response->assertStatus(200);
        $response->assertSee(route('nl.wie-is-wie'), false);
    }

    public function test_over_ons_fr_links_to_wie_is_wie(): void
    {
        $response = $this->get(route('fr.over-ons'));

        $response->assertStatus(200);
        $response->assertSee(route('fr.wie-is-wie'), false);
    }

    public function test_over_ons_shows_volunteer_section(): void
    {
        $response = $this->get(route('nl.over-ons'));

        $response->assertSee('Word vrijwilliger bij De Harmonie');
        $response->assertSee('Meer over vrijwilligerswerk');
    }

    public function test_fr_over_ons_shows_volunteer_section(): void
    {
        $response = $this->get(route('fr.over-ons'));

        $response->assertSee('Devenez bénévole à De Harmonie');
        $response->assertSee('En savoir plus');
    }

    public function test_impact_stats_render_from_database_in_nl(): void
    {
        OverOnsContent::factory()->create([
            'impact_1_aantal' => '777',
            'impact_1_omschrijving_nl' => 'mijn unieke NL omschrijving',
            'impact_2_aantal' => '888',
            'impact_3_aantal' => '999',
        ]);

        $response = $this->get(route('nl.over-ons'));

        $response->assertStatus(200);
        $response->assertSee('777');
        $response->assertSee('888');
        $response->assertSee('999');
        $response->assertSee('mijn unieke NL omschrijving');
    }

    public function test_impact_stats_render_locale_specific_descriptions_in_fr(): void
    {
        OverOnsContent::factory()->create([
            'impact_1_omschrijving_fr' => 'ma description FR unique',
        ]);

        $response = $this->get(route('fr.over-ons'));

        $response->assertStatus(200);
        $response->assertSee('ma description FR unique');
    }
}
