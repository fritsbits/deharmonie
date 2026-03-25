<?php

namespace Tests\Feature;

use App\Enums\Interesse;
use App\Models\Activiteit;
use App\Models\ActiviteitTemplate;
use Database\Seeders\ActiviteitTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiviteitTemplateSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_fifteen_templates(): void
    {
        $this->seed(ActiviteitTemplateSeeder::class);

        $this->assertSame(15, ActiviteitTemplate::count());
    }

    public function test_country_line_dance_template_has_correct_data(): void
    {
        $this->seed(ActiviteitTemplateSeeder::class);

        $template = ActiviteitTemplate::where('titel_nl', 'Country Line Dance')->first();

        $this->assertNotNull($template);
        $this->assertSame(4, $template->dag_van_de_week); // Donderdag
        $this->assertSame('14:00:00', $template->startuur);
        $this->assertSame('16:00:00', $template->einduur);
        $this->assertSame('De Harmonie', $template->locatie);
        $this->assertSame('2.00', $template->prijs);
        $this->assertSame(Interesse::Activiteiten, $template->interesse);
    }

    public function test_boodschappendienst_has_diensten_interesse(): void
    {
        $this->seed(ActiviteitTemplateSeeder::class);

        $template = ActiviteitTemplate::where('titel_nl', 'Boodschappendienst')->first();

        $this->assertNotNull($template);
        $this->assertSame(Interesse::Diensten, $template->interesse);
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(ActiviteitTemplateSeeder::class);
        $this->seed(ActiviteitTemplateSeeder::class);

        $this->assertSame(15, ActiviteitTemplate::count());
    }

    public function test_no_existing_activities_are_linked_to_templates(): void
    {
        $this->seed(ActiviteitTemplateSeeder::class);

        $this->assertSame(0, Activiteit::whereNotNull('template_id')->count());
    }
}
