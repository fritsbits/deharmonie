<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;

class WeekMenuPrintTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_print_route_loads_in_nl(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/restaurant-menu/print?week=0');

        $response->assertStatus(200);
    }

    public function test_print_route_loads_in_fr(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/fr/restaurant-menu/print?week=0');

        $response->assertStatus(200);
    }

    public function test_print_view_shows_nl_content(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertSee('Stoofvlees met Sla en Kroketjes');
        $response->assertSee('Soep van de dag');
    }

    public function test_print_view_shows_fr_content(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/fr/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertSee('Carbonnades, Frites et Salade');
        $response->assertSee('Potage du jour');
    }

    public function test_print_view_shows_closed_day(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertSee('Gesloten'); // Saturday March 28 is closed
    }

    public function test_print_view_shows_closed_day_in_fr(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/fr/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertSee('Fermé'); // Saturday March 28 is closed
    }

    public function test_print_view_responds_for_next_week(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/restaurant-menu/print?week=1');

        $response->assertStatus(200);
        $response->assertSee('Kalf blanket met Bulgur'); // Monday March 30
    }

    public function test_print_view_does_not_contain_nav(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertDontSee('<nav', false);
    }
}
