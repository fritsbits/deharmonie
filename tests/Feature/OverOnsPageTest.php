<?php

namespace Tests\Feature;

use Tests\TestCase;

class OverOnsPageTest extends TestCase
{
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
}
