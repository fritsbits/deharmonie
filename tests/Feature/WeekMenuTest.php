<?php

namespace Tests\Feature;

use App\Livewire\WeekMenu;
use App\Models\WeekMenuDag;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WeekMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        app()->setLocale('nl');
        parent::tearDown();
    }

    public function test_weekmenu_page_loads_in_nl(): void
    {
        $response = $this->get('/nl/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Menu deze week');
        $response->assertSee('Openingsuren');
        $response->assertSee('Gewoon binnenlopen');
    }

    public function test_weekmenu_page_loads_in_fr(): void
    {
        $response = $this->get('/fr/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Menu de cette semaine');
        $response->assertSee("Heures d'ouverture");
        $response->assertSee('Entrez librement');
    }

    public function test_today_card_is_highlighted_before_cutoff(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        WeekMenuDag::factory()->create([
            'date' => '2026-03-23',
            'main_nl' => 'Stoofvlees met Sla en Kroketjes',
            'main_fr' => 'Carbonnades, Frites et Salade',
        ]);

        $response = $this->get('/nl/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Vandaag');
        $response->assertSee('Stoofvlees met Sla en Kroketjes');
    }

    public function test_tomorrow_card_is_highlighted_after_cutoff(): void
    {
        Carbon::setTestNow('2026-03-23 15:00:00');
        WeekMenuDag::factory()->create(['date' => '2026-03-23', 'main_nl' => 'Stoofvlees met Sla en Kroketjes']);
        WeekMenuDag::factory()->create(['date' => '2026-03-24', 'main_nl' => 'Chicon Gratin met Puree']);

        $response = $this->get('/nl/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Morgen');
        $response->assertSee('Chicon Gratin met Puree');
    }

    public function test_closed_day_is_not_shown(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        WeekMenuDag::factory()->create(['date' => '2026-03-23']);
        WeekMenuDag::factory()->closed()->create(['date' => '2026-03-28']);

        $response = $this->get('/nl/restaurant-menu');

        $response->assertStatus(200);
        $response->assertDontSee('Gesloten'); // closed days are filtered out of the live component entirely
    }

    public function test_special_event_shows_all_courses(): void
    {
        Carbon::setTestNow('2026-04-01 10:00:00');
        WeekMenuDag::factory()->create(['date' => '2026-04-01', 'main_nl' => 'Soep dag']);
        WeekMenuDag::factory()->create([
            'date' => '2026-04-02',
            'special_event' => true,
            'price' => 20,
            'main_nl' => null,
            'main_fr' => null,
            'event_label_nl' => 'Paasmenu',
            'event_label_fr' => 'Menu de Pâques',
            'courses' => [
                ['nl' => 'Kir Royal', 'fr' => 'Kir Royal'],
                ['nl' => 'Eendenborst', 'fr' => 'Magret de Canard'],
            ],
        ]);

        $response = $this->get('/nl/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Paasmenu');
        $response->assertSee('Kir Royal');
        $response->assertSee('Eendenborst');
        $response->assertSee('€ 20');
    }

    public function test_closed_day_is_skipped_when_resolving_highlighted_date(): void
    {
        Carbon::setTestNow('2026-03-27 15:00:00');
        WeekMenuDag::factory()->closed()->create(['date' => '2026-03-28']);
        WeekMenuDag::factory()->create(['date' => '2026-03-30', 'main_nl' => 'Kalf blanket met Bulgur']);

        $response = $this->get('/nl/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Kalf blanket met Bulgur');
    }

    public function test_week_menu_component_shows_print_link_in_nl(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        WeekMenuDag::factory()->create(['date' => '2026-03-23']);

        Livewire::test(WeekMenu::class)
            ->assertSee('Druk af')
            ->assertSee('restaurant-menu/print');
    }

    public function test_week_menu_component_shows_print_link_in_fr(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        app()->setLocale('fr');
        WeekMenuDag::factory()->create(['date' => '2026-03-23']);

        Livewire::test(WeekMenu::class)
            ->assertSee('Imprimer')
            ->assertSee('fr/restaurant-menu/print');
    }
}
