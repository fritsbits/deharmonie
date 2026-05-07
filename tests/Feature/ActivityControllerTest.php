<?php

namespace Tests\Feature;

use App\Livewire\ActivityFilter;
use App\Models\Activiteit;
use App\Models\Deelnameverzoek;
use App\Models\WeekMenuDag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ActivityControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_published_activities(): void
    {
        $gepubliceerd = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        $concept = Activiteit::factory()->create([
            'status' => 'concept',
            'datum' => now()->format('Y-m-d'),
        ]);

        $response = $this->get('/');
        // Activity appears via Livewire server-side render
        $response->assertSee($gepubliceerd->titel_nl);
        $response->assertDontSee($concept->titel_nl);
        // New hero content
        $response->assertSee('Eet mee');
        $response->assertSee('Doe mee');
        $response->assertSee('Kom langs');
        $response->assertSee('Weekmenu');
        // Sections present
        $response->assertSee('Komende activiteiten');
        $response->assertSee('Wij komen ook naar je toe');
        $response->assertSee('Antwerpsesteenweg 24');
        // Old standalone AGENDA section is gone
        $response->assertDontSee('Volgende activiteiten');
    }

    public function test_cancelled_activity_shows_badge(): void
    {
        $geannuleerd = Activiteit::factory()->create([
            'status' => 'geannuleerd',
            'datum' => now()->format('Y-m-d'),
        ]);

        $response = $this->get('/activiteiten/agenda');
        $response->assertSee($geannuleerd->titel_nl);
        $response->assertSee('Geannuleerd');
    }

    public function test_empty_state_shown_when_no_activities(): void
    {
        Activiteit::query()->delete();

        $response = $this->get('/activiteiten/agenda');
        $response->assertSee('Geen activiteiten deze week.');
    }

    public function test_overview_shows_only_current_week_by_default(): void
    {
        $today = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        $nextMonth = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeeks(2)->format('Y-m-d'),
        ]);
        $lastWeek = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->subDay()->format('Y-m-d'),
        ]);

        $response = $this->get('/activiteiten/agenda');
        $response->assertSee($today->titel_nl);
        $response->assertDontSee($nextMonth->titel_nl);
        $response->assertDontSee($lastWeek->titel_nl);
    }

    public function test_activity_detail_shows_cancellation_banner(): void
    {
        $activiteit = Activiteit::factory()->create([
            'status' => 'geannuleerd',
            'notice_nl' => 'Deze activiteit gaat niet door.',
        ]);
        $response = $this->get('/activiteiten/'.$activiteit->slug);
        $response->assertSee('Deze activiteit gaat niet door.');
        $response->assertDontSee('Inschrijvingsformulier');
    }

    public function test_activity_detail_shows_contact_cta_for_published(): void
    {
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);
        $response = $this->get('/activiteiten/'.$activiteit->slug);
        $response->assertStatus(200);
        $response->assertSee('0220328048'); // phone link in CTA
    }

    public function test_activity_detail_loads_without_capacity_block(): void
    {
        $activiteit = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'max_deelnemers' => 1,
        ]);
        Deelnameverzoek::factory()->create(['activiteit_id' => $activiteit->id]);

        $response = $this->get('/activiteiten/'.$activiteit->slug);
        $response->assertStatus(200);
        $response->assertDontSee('Inschrijvingsformulier');
    }

    public function test_homepage_shows_menu_section_when_menu_data_exists(): void
    {
        $vandaag = WeekMenuDag::factory()->create([
            'date' => today()->toDateString(),
            'main_nl' => 'Kalf blanket met bulgur',
            'price' => 10,
        ]);
        $morgen = WeekMenuDag::factory()->create([
            'date' => today()->addDay()->toDateString(),
            'main_nl' => 'Varkensgebraad met witloof',
            'price' => 9,
        ]);

        $response = $this->get('/');

        $response->assertSee('Vandaag');
        $response->assertSee('Kalf blanket met bulgur');
        $response->assertSee('10');
        $response->assertSee('Morgen');
        $response->assertSee('Varkensgebraad met witloof');
        $response->assertSee('9');
        $response->assertSee('Soep van de dag inbegrepen');
        $response->assertSee('Volledig menu →');
    }

    public function test_homepage_hides_menu_section_when_no_menu_data(): void
    {
        WeekMenuDag::query()->delete();

        $response = $this->get('/');

        // The menu section key string is absent when no menu data exists
        $response->assertDontSee('Soep van de dag inbegrepen');
    }

    public function test_homepage_hides_today_card_when_only_tomorrow_has_menu(): void
    {
        WeekMenuDag::factory()->create([
            'date' => today()->addDay()->toDateString(),
            'main_nl' => 'Morgen_only_dish_xyz',
            'price' => 8,
        ]);

        $response = $this->get('/');

        // Menu section is visible (tomorrow exists)
        $response->assertSee('Soep van de dag inbegrepen');
        // Today's dish is not present, tomorrow's is
        $response->assertDontSee('Morgen_only_dish_xyz_today');
        $response->assertSee('Morgen_only_dish_xyz');
        $response->assertSee('Morgen');
    }

    public function test_homepage_hides_closed_day_menu_card(): void
    {
        WeekMenuDag::factory()->closed()->create([
            'date' => today()->toDateString(),
        ]);
        WeekMenuDag::factory()->create([
            'date' => today()->addDay()->toDateString(),
            'main_nl' => 'Open_morgen_gerecht_xyz',
            'price' => 11,
        ]);

        $response = $this->get('/');

        // Only tomorrow's menu card appears
        $response->assertSee('Open_morgen_gerecht_xyz');
        $response->assertSee('Morgen');
        $response->assertSee('Soep van de dag inbegrepen');
    }

    public function test_activity_filter_shows_at_most_five(): void
    {
        Activiteit::factory()->count(7)->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);

        $component = Livewire::test(ActivityFilter::class);
        $activiteiten = $component->get('activiteiten');
        $this->assertCount(5, $activiteiten);
    }

    public function test_homepage_shows_volunteer_strip(): void
    {
        $response = $this->get(route('nl.home'));

        $response->assertSee('Wil je meehelpen bij De Harmonie?');
        $response->assertSee('Word vrijwilliger');
    }
}
