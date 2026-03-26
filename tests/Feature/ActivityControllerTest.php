<?php

namespace Tests\Feature;

use App\Livewire\ActivityFilter;
use App\Models\Activiteit;
use App\Models\Deelnameverzoek;
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
        $response->assertSee('Bekijk activiteiten');
        // Sections present
        $response->assertSee('Komende activiteiten');
        $response->assertSee('Samen aan tafel');
        $response->assertSee('Bij u thuis');
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

        $response = $this->get('/activiteiten');
        $response->assertSee($geannuleerd->titel_nl);
        $response->assertSee('Geannuleerd');
    }

    public function test_empty_state_shown_when_no_activities(): void
    {
        Activiteit::query()->delete();

        $response = $this->get('/activiteiten');
        $response->assertSee('Geen activiteiten gepland.');
    }

    public function test_upcoming_activities_shown_including_next_month(): void
    {
        $today = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        $nextMonth = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addMonth()->format('Y-m-d'),
        ]);
        $past = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->subDay()->format('Y-m-d'),
        ]);

        $response = $this->get('/activiteiten');
        $response->assertSee($today->titel_nl);
        $response->assertSee($nextMonth->titel_nl);
        $response->assertDontSee($past->titel_nl);
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

    public function test_activity_detail_shows_registration_form_for_published(): void
    {
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);
        $response = $this->get('/activiteiten/'.$activiteit->slug);
        $response->assertStatus(200);
        $response->assertSee('formulier'); // registration form is shown
    }

    public function test_activity_detail_shows_full_message_when_at_capacity(): void
    {
        $activiteit = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'max_deelnemers' => 1,
        ]);
        Deelnameverzoek::factory()->create(['activiteit_id' => $activiteit->id]);

        $response = $this->get('/activiteiten/'.$activiteit->slug);
        $response->assertSee('Volzet');
    }

    public function test_homepage_shows_menu_preview(): void
    {
        $response = $this->get('/');
        $response->assertSee('Vandaag');
        $response->assertSee('Soep van de dag inbegrepen');
        $response->assertSee('Volledig weekmenu bekijken');
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
}
