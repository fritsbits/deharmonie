<?php

namespace Tests\Feature;

use App\Enums\ActiviteitStatus;
use App\Models\Activiteit;
use App\Models\ActiviteitTemplate;
use App\Models\Deelnameverzoek;
use App\Services\ActiviteitTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiviteitTemplateServiceTest extends TestCase
{
    use RefreshDatabase;

    private ActiviteitTemplateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ActiviteitTemplateService;
    }

    public function test_generates_sessions_for_every_matching_weekday(): void
    {
        // Mondays from 2026-04-06 to 2026-04-27 = 4 Mondays
        $template = ActiviteitTemplate::factory()->monday()->create([
            'reeks_start' => '2026-04-06',
            'reeks_einde' => '2026-04-27',
        ]);

        $count = $this->service->generateSessions($template);

        $this->assertSame(4, $count);
        $this->assertSame(4, Activiteit::where('template_id', $template->id)->count());
    }

    public function test_generated_sessions_have_correct_fields(): void
    {
        $template = ActiviteitTemplate::factory()->create([
            'titel_nl' => 'Zumba',
            'dag_van_de_week' => 5, // Friday
            'reeks_start' => '2026-04-03',
            'reeks_einde' => '2026-04-03',
            'startuur' => '10:00:00',
            'locatie' => 'De Harmonie',
        ]);

        $this->service->generateSessions($template);

        $session = Activiteit::where('template_id', $template->id)->first();
        $this->assertNotNull($session);
        $this->assertSame('2026-04-03', $session->datum->format('Y-m-d'));
        $this->assertSame('10:00:00', $session->startuur);
        $this->assertSame(ActiviteitStatus::Concept, $session->status);
        $this->assertStringContainsString('2026-04-03', $session->slug);
    }

    public function test_does_not_duplicate_existing_sessions(): void
    {
        $template = ActiviteitTemplate::factory()->create([
            'dag_van_de_week' => 1,
            'reeks_start' => '2026-04-06',
            'reeks_einde' => '2026-04-06',
        ]);

        $this->service->generateSessions($template);
        $count = $this->service->generateSessions($template); // run again

        $this->assertSame(0, $count);
        $this->assertSame(1, Activiteit::where('template_id', $template->id)->count());
    }

    public function test_generates_unique_slugs_for_title_collisions(): void
    {
        // Pre-existing activiteit with the same slug pattern
        Activiteit::factory()->create(['slug' => 'zumba-2026-04-06']);

        $template = ActiviteitTemplate::factory()->create([
            'titel_nl' => 'Zumba',
            'dag_van_de_week' => 1,
            'reeks_start' => '2026-04-06',
            'reeks_einde' => '2026-04-06',
        ]);

        $this->service->generateSessions($template);

        $session = Activiteit::where('template_id', $template->id)->first();
        $this->assertNotSame('zumba-2026-04-06', $session->slug);
    }

    public function test_propagate_updates_future_eligible_sessions(): void
    {
        $template = ActiviteitTemplate::factory()->create([
            'dag_van_de_week' => 1,
            'reeks_start' => now()->subWeeks(2)->startOfWeek(),
            'reeks_einde' => now()->addWeeks(4)->startOfWeek(),
        ]);
        $this->service->generateSessions($template);

        $template->update(['titel_nl' => 'Zumba Updated', 'titel_fr' => 'Zumba Mise à Jour']);
        $updated = $this->service->propagateToFutureSessions($template);

        $this->assertGreaterThan(0, $updated);

        // Future sessions get new title
        $futureSessions = Activiteit::where('template_id', $template->id)
            ->where('datum', '>=', today())
            ->get();
        foreach ($futureSessions as $session) {
            $this->assertSame('Zumba Updated', $session->titel_nl);
        }

        // Past sessions are untouched
        $pastSessions = Activiteit::where('template_id', $template->id)
            ->where('datum', '<', today())
            ->get();
        foreach ($pastSessions as $session) {
            $this->assertNotSame('Zumba Updated', $session->titel_nl);
        }
    }

    public function test_propagate_skips_cancelled_sessions(): void
    {
        $template = ActiviteitTemplate::factory()->create([
            'dag_van_de_week' => 1,
            'reeks_start' => now()->addWeek()->startOfWeek(),
            'reeks_einde' => now()->addWeeks(2)->startOfWeek(),
        ]);
        $this->service->generateSessions($template);

        // Cancel the first future session
        $session = Activiteit::where('template_id', $template->id)->orderBy('datum')->first();
        $session->update(['status' => ActiviteitStatus::Geannuleerd, 'titel_nl' => 'Original']);

        $template->update(['titel_nl' => 'Changed']);
        $this->service->propagateToFutureSessions($template);

        $session->refresh();
        $this->assertSame('Original', $session->titel_nl);
    }

    public function test_propagate_skips_sessions_with_active_registrations(): void
    {
        $template = ActiviteitTemplate::factory()->create([
            'dag_van_de_week' => 1,
            'reeks_start' => now()->addWeek()->startOfWeek(),
            'reeks_einde' => now()->addWeek()->startOfWeek(),
        ]);
        $this->service->generateSessions($template);

        $session = Activiteit::where('template_id', $template->id)->first();
        $session->update(['titel_nl' => 'Original']);
        Deelnameverzoek::factory()->create([
            'activiteit_id' => $session->id,
            'status' => 'te_contacteren',
        ]);

        $template->update(['titel_nl' => 'Changed']);
        $this->service->propagateToFutureSessions($template);

        $session->refresh();
        $this->assertSame('Original', $session->titel_nl);
    }

    public function test_slugs_are_never_changed_during_propagation(): void
    {
        $template = ActiviteitTemplate::factory()->create([
            'titel_nl' => 'Zumba',
            'dag_van_de_week' => 1,
            'reeks_start' => now()->addWeek()->startOfWeek(),
            'reeks_einde' => now()->addWeek()->startOfWeek(),
        ]);
        $this->service->generateSessions($template);

        $session = Activiteit::where('template_id', $template->id)->first();
        $originalSlug = $session->slug;

        $template->update(['titel_nl' => 'Renamed Activity']);
        $this->service->propagateToFutureSessions($template);

        $session->refresh();
        $this->assertSame($originalSlug, $session->slug);
    }
}
