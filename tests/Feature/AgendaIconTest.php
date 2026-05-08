<?php

namespace Tests\Feature;

use App\Enums\ActiviteitStatus;
use App\Enums\Categorie;
use App\Models\Activiteit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaIconTest extends TestCase
{
    use RefreshDatabase;

    public function test_agenda_renders_categorie_icon_for_dans(): void
    {
        // Force the activity into a date range the agenda will display.
        Activiteit::factory()->vast()->create([
            'titel_nl' => 'Zumba',
            'categorie' => Categorie::SportBeweging,
            'datum' => now()->next('Tuesday')->toDateString(),
            'startuur' => '14:00:00',
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);

        $response = $this->get('/nl/activiteiten/agenda');

        $response->assertOk();
        // Asserts the Sport-Beweging icon's SVG path appears in the rendered HTML.
        $response->assertSee(Categorie::SportBeweging->icon(), false);
    }
}
