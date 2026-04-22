<?php

namespace Tests\Feature;

use App\Models\Activiteit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaClickableRowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_agenda_row_links_to_activity_detail(): void
    {
        $published = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);

        $response = $this->get('/activiteiten/agenda?week=0');

        $response->assertOk();
        $response->assertSee(
            'href="' . route('nl.activiteiten.show', $published->slug) . '"',
            false
        );
    }

    public function test_cancelled_agenda_row_still_links_to_activity_detail(): void
    {
        $cancelled = Activiteit::factory()->create([
            'status' => 'geannuleerd',
            'datum' => now()->addDay()->format('Y-m-d'),
        ]);

        $response = $this->get('/activiteiten/agenda?week=0');

        $response->assertOk();
        $response->assertSee(
            'href="' . route('nl.activiteiten.show', $cancelled->slug) . '"',
            false
        );
    }
}
