<?php

namespace Tests\Feature;

use App\Models\OverOnsContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_jaarverslag_card_is_hidden_when_no_pdf_is_uploaded(): void
    {
        OverOnsContent::factory()->create(['jaarverslag_jaar' => 2025]);

        $response = $this->get(route('nl.over-ons'));

        $response->assertStatus(200);
        $response->assertDontSee('class="over-ons-jaarverslag-link"', false);
        $response->assertDontSee('Jaarverslag 2025');
    }

    public function test_jaarverslag_card_renders_year_and_pdf_link_when_uploaded(): void
    {
        Storage::fake('public');

        $content = OverOnsContent::factory()->create(['jaarverslag_jaar' => 2026]);
        $content->addMedia(UploadedFile::fake()->createWithContent('report.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\nxref\n0 1\n0000000000 65535 f \ntrailer\n<< /Size 1 >>\nstartxref\n9\n%%EOF"))
            ->toMediaCollection('jaarverslag');

        $response = $this->get(route('nl.over-ons'));

        $response->assertStatus(200);
        $response->assertSee('Jaarverslag 2026');
        $response->assertSee('class="over-ons-jaarverslag-link"', false);
        $response->assertSee($content->fresh()->getJaarverslagUrl(), false);
    }

    public function test_jaarverslag_card_uses_french_label_in_fr_locale(): void
    {
        Storage::fake('public');

        $content = OverOnsContent::factory()->create(['jaarverslag_jaar' => 2026]);
        $content->addMedia(UploadedFile::fake()->createWithContent('report.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\nxref\n0 1\n0000000000 65535 f \ntrailer\n<< /Size 1 >>\nstartxref\n9\n%%EOF"))
            ->toMediaCollection('jaarverslag');

        $response = $this->get(route('fr.over-ons'));

        $response->assertStatus(200);
        $response->assertSee('Rapport annuel 2026');
    }
}
