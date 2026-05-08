<?php

namespace Tests\Feature;

use App\Models\Activiteit;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityOverzichtTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_activities_in_current_week(): void
    {
        $today = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        $nextWeek = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        $this->get('/nl/activiteiten/agenda?week=0')
            ->assertSee($today->titel_nl)
            ->assertDontSee($nextWeek->titel_nl);
    }

    public function test_does_not_show_previous_week_activities(): void
    {
        $lastWeek = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->subDay()->format('Y-m-d'),
        ]);

        $this->get('/nl/activiteiten/agenda?week=0')
            ->assertDontSee($lastWeek->titel_nl);
    }

    public function test_shows_full_week_including_past_days(): void
    {
        $monday = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
        ]);

        $this->get('/nl/activiteiten/agenda?week=0')
            ->assertSee($monday->titel_nl);
    }

    public function test_has_prev_false_at_initial_offset(): void
    {
        $this->get('/nl/activiteiten/agenda?week=0')
            ->assertDontSee(__('activities.previous_week'));
    }

    public function test_has_prev_true_after_navigating_forward(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        $this->get('/nl/activiteiten/agenda?week=1')
            ->assertSee(__('activities.previous_week'));
    }

    public function test_has_prev_false_after_navigating_forward_with_no_prior_activities(): void
    {
        // Only activity is next week — nothing to go back to
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        $this->get('/nl/activiteiten/agenda?week=1')
            ->assertDontSee(__('activities.previous_week'));
    }

    public function test_has_next_false_when_no_future_activities_exist(): void
    {
        $this->get('/nl/activiteiten/agenda?week=0')
            ->assertDontSee(__('activities.next_week'));
    }

    public function test_has_next_true_when_future_week_has_activities(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        $this->get('/nl/activiteiten/agenda?week=0')
            ->assertSee(__('activities.next_week'));
    }

    public function test_next_week_link_points_to_week_1(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        $this->get('/nl/activiteiten/agenda?week=0')
            ->assertSee('week=1');
    }

    public function test_next_week_skips_empty_weeks_to_nearest_activity(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        // Gap: weeks 1 and 2 are empty, activity is in week 3
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addWeeks(3)->format('Y-m-d'),
        ]);

        // On week=0, next link should point to week=3
        $this->get('/nl/activiteiten/agenda?week=0')
            ->assertSee('week=3');
    }

    public function test_has_next_true_even_with_gap_weeks(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addWeeks(3)->format('Y-m-d'),
        ]);

        $this->get('/nl/activiteiten/agenda?week=0')
            ->assertSee(__('activities.next_week'));
    }

    public function test_prev_week_navigates_to_previous_activity_week(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        // On week=1, prev link should point back to week=0
        $this->get('/nl/activiteiten/agenda?week=1')
            ->assertSee('week=0');
    }

    public function test_prev_week_skips_empty_weeks_to_nearest_activity(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        // Gap: weeks 1 and 2 are empty, activity is in week 3
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addWeeks(3)->format('Y-m-d'),
        ]);

        // On week=3, prev link should point back to week=0
        $this->get('/nl/activiteiten/agenda?week=3')
            ->assertSee('week=0');
    }

    public function test_prev_week_absent_at_zero(): void
    {
        $this->get('/nl/activiteiten/agenda?week=0')
            ->assertDontSee(__('activities.previous_week'));
    }

    public function test_week_heading_is_localised_for_nl(): void
    {
        app()->setLocale('nl');
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        $expected = $start->month === $end->month
            ? "{$start->day}–{$end->day} ".$start->locale('nl')->isoFormat('MMMM YYYY')
            : $start->locale('nl')->isoFormat('D MMMM').' – '.$end->locale('nl')->isoFormat('D MMMM YYYY');

        $this->get('/nl/activiteiten/agenda?week=0')
            ->assertSee($expected);
    }

    public function test_week_heading_is_localised_for_fr(): void
    {
        app()->setLocale('fr');
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        $expected = $start->month === $end->month
            ? "{$start->day}–{$end->day} ".$start->locale('fr')->isoFormat('MMMM YYYY')
            : $start->locale('fr')->isoFormat('D MMMM').' – '.$end->locale('fr')->isoFormat('D MMMM YYYY');

        $this->get('/fr/activites/agenda?week=0')
            ->assertSee($expected);
    }

    public function test_cancelled_activities_appear_in_list(): void
    {
        $cancelled = Activiteit::factory()->create([
            'status' => 'geannuleerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
        ]);

        $this->get('/nl/activiteiten/agenda?week=0')
            ->assertSee($cancelled->titel_nl);
    }

    public function test_mount_jumps_to_first_week_with_activities(): void
    {
        $nextWeek = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        // No week param → should auto-jump and show next week's activity
        $this->get('/nl/activiteiten/agenda')
            ->assertSee($nextWeek->titel_nl);
    }

    public function test_activities_grouped_by_date(): void
    {
        $monday = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
            'titel_nl' => 'Maandag activiteit',
        ]);
        $tuesday = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addDay()->format('Y-m-d'),
            'titel_nl' => 'Dinsdag activiteit',
        ]);

        $this->get('/nl/activiteiten/agenda?week=0')
            ->assertSee('Maandag activiteit')
            ->assertSee('Dinsdag activiteit');
    }

    public function test_activities_ordered_by_date_then_time(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
            'startuur' => '15:00:00',
            'titel_nl' => 'Kaartspelen',
        ]);
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
            'startuur' => '09:00:00',
            'titel_nl' => 'Aquafit',
        ]);

        $this->get('/nl/activiteiten/agenda?week=0')
            ->assertSeeInOrder(['Aquafit', 'Kaartspelen']);
    }

    public function test_mount_skips_current_week_when_all_activities_have_passed(): void
    {
        $monday = now()->startOfWeek();
        if ($monday->isToday()) {
            $this->markTestSkipped('Cannot test past-day-in-current-week when today is Monday.');
        }

        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => $monday->format('Y-m-d'),
        ]);

        $nextWeek = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        $this->get('/nl/activiteiten/agenda')
            ->assertSee($nextWeek->titel_nl);
    }

    public function test_visiting_specific_week_shows_that_weeks_activities(): void
    {
        $thisWeek = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        $week2 = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeeks(2)->format('Y-m-d'),
        ]);

        $this->get('/nl/activiteiten/agenda?week=2')
            ->assertSee($week2->titel_nl)
            ->assertDontSee($thisWeek->titel_nl);
    }
}
