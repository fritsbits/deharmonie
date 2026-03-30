<?php

namespace Tests\Feature;

use App\Livewire\WeekMenu;
use Carbon\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class WeekMenuTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        app()->setLocale('nl');
        parent::tearDown();
    }

    public function test_weekmenu_page_loads_in_nl(): void
    {
        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Weekmenu');
        $response->assertSee('Openingsuren');
        $response->assertSee('Gewoon binnenlopen');
        $response->assertSee('Allergenen');
    }

    public function test_weekmenu_page_loads_in_fr(): void
    {
        $response = $this->get('/fr/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Semaine');
        $response->assertSee("Heures d'ouverture");
        $response->assertSee('Entrez librement');
    }

    public function test_today_card_is_highlighted_before_cutoff(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00'); // Monday, before 14:00

        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Vandaag');
        $response->assertSee('Stoofvlees met Sla en Kroketjes');
    }

    public function test_tomorrow_card_is_highlighted_after_cutoff(): void
    {
        Carbon::setTestNow('2026-03-23 15:00:00'); // Monday, after 14:00

        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Morgen');
        $response->assertSee('Chicon Gratin met Puree');
    }

    public function test_closed_day_shows_gesloten(): void
    {
        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Gesloten');
    }

    public function test_special_event_shows_all_courses(): void
    {
        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Paasmenu');
        $response->assertSee('Kir Royal');
        $response->assertSee('Eendenborst');
        $response->assertSee('€ 20');
    }

    public function test_closed_day_is_skipped_when_resolving_highlighted_date(): void
    {
        Carbon::setTestNow('2026-03-27 15:00:00'); // Friday after 14:00 — Saturday is closed

        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        // Next open day after Saturday is Monday 30/03
        $response->assertSee('Kalf blanket met Bulgur');
    }

    public function test_week_menu_component_shows_print_link_in_nl(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        Livewire::test(WeekMenu::class)
            ->assertSee('Afdrukken / PDF')
            ->assertSee('restaurant-menu/print');
    }

    public function test_week_menu_component_shows_print_link_in_fr(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        app()->setLocale('fr');

        Livewire::test(WeekMenu::class)
            ->assertSee('Imprimer / PDF')
            ->assertSee('fr/restaurant-menu/print');
    }
}
