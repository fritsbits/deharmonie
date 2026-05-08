<?php

namespace Tests\Feature;

use App\Enums\ActiviteitStatus;
use App\Enums\Categorie;
use App\Http\Controllers\ActivityController;
use App\Models\Activiteit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OverzichtPaginaTest extends TestCase
{
    use RefreshDatabase;

    public function test_controller_provides_vaste_aanbod_grouped_by_section(): void
    {
        Activiteit::factory()->vast()->create([
            'titel_nl' => 'Zumba',
            'categorie' => Categorie::SportBeweging,
            'datum' => now()->addDays(2),
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);
        Activiteit::factory()->vast()->create([
            'titel_nl' => 'Naaiworkshop',
            'categorie' => Categorie::Creatief,
            'datum' => now()->addDays(3),
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);
        Activiteit::factory()->vast()->create([
            'titel_nl' => 'Bingo',
            'categorie' => Categorie::Spelletjes,
            'datum' => now()->addDays(4),
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);
        Activiteit::factory()->speciaal()->create([
            'titel_nl' => 'Museumbezoek',
            'categorie' => Categorie::OpUitstap,
            'datum' => now()->addDays(5),
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);

        $response = $this->get('/nl/activiteiten');
        $response->assertOk();
        $response->assertSee('Beweeg mee');
        $response->assertSee('Maak & leer mee');
        $response->assertSee('Ontmoet & beleef mee');
        $response->assertSee('Zumba');
        $response->assertSee('Naaiworkshop');
        $response->assertSee('Bingo');

        $controller = app(ActivityController::class);
        $reflection = new \ReflectionMethod($controller, 'index');
        $view = $reflection->invoke($controller);
        $data = $view->getData();

        $this->assertArrayHasKey('vasteAanbod', $data);
        $this->assertArrayHasKey('bijzondereActiviteiten', $data);

        $sections = $data['vasteAanbod']->keys()->all();
        sort($sections);
        $this->assertEquals(['beweeg', 'maak_leer', 'ontmoet_beleef'], $sections);

        // beweeg has Zumba
        $this->assertEquals(['Zumba'], $data['vasteAanbod']['beweeg']->pluck('titel_nl')->all());
        // maak_leer has Naaiworkshop
        $this->assertEquals(['Naaiworkshop'], $data['vasteAanbod']['maak_leer']->pluck('titel_nl')->all());
        // ontmoet_beleef has Bingo
        $this->assertEquals(['Bingo'], $data['vasteAanbod']['ontmoet_beleef']->pluck('titel_nl')->all());
        // bijzondereActiviteiten has Museumbezoek
        $this->assertEquals(['Museumbezoek'], $data['bijzondereActiviteiten']->pluck('titel_nl')->all());
    }
}
