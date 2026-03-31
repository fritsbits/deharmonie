<?php

namespace Tests\Feature;

use App\Models\TeamCategorie;
use App\Models\TeamLid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WieIsWiePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_nl_wie_is_wie_page_renders(): void
    {
        $categorie = TeamCategorie::factory()->create([
            'naam_nl' => 'Onthaal & Animatie',
            'naam_fr' => 'Accueil & Animation',
            'volgorde' => 1,
        ]);

        TeamLid::factory()->create([
            'team_categorie_id' => $categorie->id,
            'naam' => 'Deborah Monfils',
            'volgorde' => 1,
        ]);

        $response = $this->get(route('nl.wie-is-wie'));

        $response->assertStatus(200);
        $response->assertSee('Onthaal & Animatie');
        $response->assertSee('Deborah Monfils');
    }

    public function test_fr_wie_is_wie_page_shows_french_category_names(): void
    {
        $categorie = TeamCategorie::factory()->create([
            'naam_nl' => 'Onthaal & Animatie',
            'naam_fr' => 'Accueil & Animation',
            'volgorde' => 1,
        ]);

        TeamLid::factory()->create([
            'team_categorie_id' => $categorie->id,
            'naam' => 'Deborah Monfils',
            'volgorde' => 1,
        ]);

        $response = $this->get(route('fr.wie-is-wie'));

        $response->assertStatus(200);
        $response->assertSee('Accueil & Animation');
        $response->assertDontSee('Onthaal & Animatie');
        $response->assertSee('Deborah Monfils');
    }

    public function test_team_lid_with_nl_title_shows_title_on_nl_page(): void
    {
        $categorie = TeamCategorie::factory()->create(['volgorde' => 1]);

        TeamLid::factory()->create([
            'team_categorie_id' => $categorie->id,
            'naam' => 'Cynthia Spijker',
            'titel_nl' => 'Coördinator',
            'titel_fr' => 'Coordinatrice',
            'volgorde' => 1,
        ]);

        $response = $this->get(route('nl.wie-is-wie'));

        $response->assertSee('Cynthia Spijker');
        $response->assertSee('Coördinator');
        $response->assertDontSee('Coordinatrice');
    }

    public function test_team_lid_with_fr_title_shows_fr_title_on_fr_page(): void
    {
        $categorie = TeamCategorie::factory()->create(['volgorde' => 1]);

        TeamLid::factory()->create([
            'team_categorie_id' => $categorie->id,
            'naam' => 'Cynthia Spijker',
            'titel_nl' => 'Coördinator',
            'titel_fr' => 'Coordinatrice',
            'volgorde' => 1,
        ]);

        $response = $this->get(route('fr.wie-is-wie'));

        $response->assertSee('Cynthia Spijker');
        $response->assertSee('Coordinatrice');
        $response->assertDontSee('Coördinator');
    }

    public function test_team_lid_without_title_shows_no_title(): void
    {
        $categorie = TeamCategorie::factory()->create(['volgorde' => 1]);

        TeamLid::factory()->create([
            'team_categorie_id' => $categorie->id,
            'naam' => 'Jan Janssen',
            'titel_nl' => null,
            'titel_fr' => null,
            'volgorde' => 1,
        ]);

        $response = $this->get(route('nl.wie-is-wie'));

        $response->assertSee('Jan Janssen');
        $response->assertDontSee('Jan Janssen —');
    }

    public function test_categories_are_shown_in_volgorde_order(): void
    {
        $tweede = TeamCategorie::factory()->create(['naam_nl' => 'Tweede', 'naam_fr' => 'Deuxième', 'volgorde' => 2]);
        $eerste = TeamCategorie::factory()->create(['naam_nl' => 'Eerste', 'naam_fr' => 'Premier', 'volgorde' => 1]);

        TeamLid::factory()->create(['team_categorie_id' => $eerste->id, 'naam' => 'Lid A', 'volgorde' => 1]);
        TeamLid::factory()->create(['team_categorie_id' => $tweede->id, 'naam' => 'Lid B', 'volgorde' => 1]);

        $response = $this->get(route('nl.wie-is-wie'));

        $content = $response->getContent();
        $this->assertLessThan(
            strpos($content, 'Tweede'),
            strpos($content, 'Eerste'),
            'Eerste categorie moet vóór Tweede categorie verschijnen'
        );
    }
}
