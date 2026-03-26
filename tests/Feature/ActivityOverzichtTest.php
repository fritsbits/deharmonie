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

    public function test_shows_activities_in_current_month(): void
    {
        $thisMonth = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        $nextMonth = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSee($thisMonth->titel_nl)
            ->assertDontSee($nextMonth->titel_nl);
    }

    public function test_does_not_show_past_activities(): void
    {
        $past = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->subDay()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertDontSee($past->titel_nl);
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
            'datum' => now()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
        ]);

        $component = Livewire::test(ActivityOverzicht::class)->call('nextMonth');

        $this->assertTrue($component->get('hasPrev'));
    }

    public function test_has_next_false_when_no_future_activities_exist(): void
    {
        $component = Livewire::test(ActivityOverzicht::class);

        $this->assertFalse($component->get('hasNext'));
    }

    public function test_has_next_true_when_future_month_has_activities(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
        ]);

        $component = Livewire::test(ActivityOverzicht::class);

        $this->assertTrue($component->get('hasNext'));
    }

    public function test_next_month_increments_offset(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSet('monthOffset', 0)
            ->call('nextMonth')
            ->assertSet('monthOffset', 1);
    }

    public function test_prev_month_decrements_offset(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->call('nextMonth')
            ->assertSet('monthOffset', 1)
            ->call('prevMonth')
            ->assertSet('monthOffset', 0);
    }

    public function test_prev_month_does_nothing_at_zero(): void
    {
        Livewire::test(ActivityOverzicht::class)
            ->assertSet('monthOffset', 0)
            ->call('prevMonth')
            ->assertSet('monthOffset', 0);
    }

    public function test_month_heading_is_localised_for_nl(): void
    {
        app()->setLocale('nl');
        $component = Livewire::test(ActivityOverzicht::class);
        $expected = ucfirst(Carbon::now()->startOfMonth()->locale('nl')->translatedFormat('F Y'));

        $this->assertSame($expected, $component->get('monthHeading'));
    }

    public function test_month_heading_is_localised_for_fr(): void
    {
        app()->setLocale('fr');
        $component = Livewire::test(ActivityOverzicht::class);
        $expected = ucfirst(Carbon::now()->startOfMonth()->locale('fr')->translatedFormat('F Y'));

        $this->assertSame($expected, $component->get('monthHeading'));
    }

    public function test_cancelled_activities_appear_in_list(): void
    {
        $cancelled = Activiteit::factory()->create([
            'status' => 'geannuleerd',
            'datum' => now()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSee($cancelled->titel_nl);
    }

    public function test_mount_jumps_to_first_month_with_activities(): void
    {
        $nextMonth = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSet('monthOffset', 1)
            ->assertSee($nextMonth->titel_nl);
    }

    public function test_activities_ordered_by_date_then_time(): void
    {
        $later = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
            'startuur' => '15:00:00',
            'titel_nl' => 'Kaartspelen',
        ]);
        $earlier = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
            'startuur' => '09:00:00',
            'titel_nl' => 'Aquafit',
        ]);

        $component = Livewire::test(ActivityOverzicht::class);
        $activiteiten = $component->get('activiteiten');

        $this->assertSame($earlier->id, $activiteiten->first()->id);
        $this->assertSame($later->id, $activiteiten->last()->id);
    }
}
