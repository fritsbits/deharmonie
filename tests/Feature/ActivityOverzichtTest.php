<?php

namespace Tests\Feature;

use App\Livewire\ActivityOverzicht;
use App\Models\Activiteit;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

        Livewire::test(ActivityOverzicht::class)
            ->assertSee($today->titel_nl)
            ->assertDontSee($nextWeek->titel_nl);
    }

    public function test_does_not_show_previous_week_activities(): void
    {
        $lastWeek = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->subDay()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertDontSee($lastWeek->titel_nl);
    }

    public function test_shows_full_week_including_past_days(): void
    {
        $monday = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSee($monday->titel_nl);
    }

    public function test_has_prev_false_at_initial_offset(): void
    {
        $component = Livewire::test(ActivityOverzicht::class);

        $this->assertFalse($component->get('hasPrev'));
    }

    public function test_has_prev_true_after_navigating_forward(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        $component = Livewire::test(ActivityOverzicht::class)->call('nextWeek');

        $this->assertTrue($component->get('hasPrev'));
    }

    public function test_has_next_false_when_no_future_activities_exist(): void
    {
        $component = Livewire::test(ActivityOverzicht::class);

        $this->assertFalse($component->get('hasNext'));
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

        $component = Livewire::test(ActivityOverzicht::class);

        $this->assertTrue($component->get('hasNext'));
    }

    public function test_next_week_increments_offset(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSet('weekOffset', 0)
            ->call('nextWeek')
            ->assertSet('weekOffset', 1);
    }

    public function test_prev_week_decrements_offset(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->call('nextWeek')
            ->assertSet('weekOffset', 1)
            ->call('prevWeek')
            ->assertSet('weekOffset', 0);
    }

    public function test_prev_week_does_nothing_at_zero(): void
    {
        Livewire::test(ActivityOverzicht::class)
            ->assertSet('weekOffset', 0)
            ->call('prevWeek')
            ->assertSet('weekOffset', 0);
    }

    public function test_week_heading_is_localised_for_nl(): void
    {
        app()->setLocale('nl');
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        $expected = $start->month === $end->month
            ? "{$start->day}–{$end->day} ".$start->locale('nl')->isoFormat('MMMM YYYY')
            : $start->locale('nl')->isoFormat('D MMMM').' – '.$end->locale('nl')->isoFormat('D MMMM YYYY');

        $component = Livewire::test(ActivityOverzicht::class);

        $this->assertSame($expected, $component->get('weekHeading'));
    }

    public function test_week_heading_is_localised_for_fr(): void
    {
        app()->setLocale('fr');
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        $expected = $start->month === $end->month
            ? "{$start->day}–{$end->day} ".$start->locale('fr')->isoFormat('MMMM YYYY')
            : $start->locale('fr')->isoFormat('D MMMM').' – '.$end->locale('fr')->isoFormat('D MMMM YYYY');

        $component = Livewire::test(ActivityOverzicht::class);

        $this->assertSame($expected, $component->get('weekHeading'));
    }

    public function test_cancelled_activities_appear_in_list(): void
    {
        $cancelled = Activiteit::factory()->create([
            'status' => 'geannuleerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSee($cancelled->titel_nl);
    }

    public function test_mount_jumps_to_first_week_with_activities(): void
    {
        $nextWeek = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSet('weekOffset', 1)
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

        $component = Livewire::test(ActivityOverzicht::class);
        $grouped = $component->get('activiteiten');

        $this->assertCount(2, $grouped);
        $this->assertTrue($grouped->has($monday->datum->toDateString()));
        $this->assertTrue($grouped->has($tuesday->datum->toDateString()));
    }

    public function test_activities_ordered_by_date_then_time(): void
    {
        $later = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
            'startuur' => '15:00:00',
            'titel_nl' => 'Kaartspelen',
        ]);
        $earlier = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
            'startuur' => '09:00:00',
            'titel_nl' => 'Aquafit',
        ]);

        $component = Livewire::test(ActivityOverzicht::class);
        $grouped = $component->get('activiteiten');
        $dayActivities = $grouped->first();

        $this->assertSame($earlier->id, $dayActivities->first()->id);
        $this->assertSame($later->id, $dayActivities->last()->id);
    }

    public function test_mount_skips_current_week_when_all_activities_have_passed(): void
    {
        // Activity from the start of this week (could be in the past if today is not Monday)
        // We need a datum strictly before today but in the current week.
        // Use startOfWeek() only if it's not today, otherwise skip this test setup.
        $monday = now()->startOfWeek();
        if ($monday->isToday()) {
            $this->markTestSkipped('Cannot test past-day-in-current-week when today is Monday.');
        }

        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => $monday->format('Y-m-d'), // Monday (past, since today is Tue–Sun)
        ]);

        $nextWeek = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSet('weekOffset', 1)
            ->assertSee($nextWeek->titel_nl);
    }
}
