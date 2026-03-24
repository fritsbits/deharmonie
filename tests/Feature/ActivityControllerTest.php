<?php

namespace Tests\Feature;

use App\Models\Activiteit;
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

        Livewire::test(\App\Livewire\ActivityFilter::class)
            ->assertSee($gepubliceerd->titel_nl)
            ->assertDontSee($concept->titel_nl);
    }

    public function test_cancelled_activity_shows_badge(): void
    {
        $geannuleerd = Activiteit::factory()->create([
            'status' => 'geannuleerd',
            'datum' => now()->format('Y-m-d'),
        ]);

        Livewire::test(\App\Livewire\ActivityFilter::class)
            ->assertSee($geannuleerd->titel_nl)
            ->assertSee('Geannuleerd');
    }

    public function test_empty_state_shown_when_no_activities(): void
    {
        Livewire::test(\App\Livewire\ActivityFilter::class)
            ->assertSee('Geen activiteiten');
    }

    public function test_month_filter_changes_results(): void
    {
        $thisMonth = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        $nextMonth = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addMonth()->format('Y-m-d'),
        ]);

        Livewire::test(\App\Livewire\ActivityFilter::class)
            ->assertSee($thisMonth->titel_nl)
            ->assertDontSee($nextMonth->titel_nl)
            ->call('nextMonth')
            ->assertSee($nextMonth->titel_nl)
            ->assertDontSee($thisMonth->titel_nl);
    }
}
