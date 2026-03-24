<?php

namespace Tests\Unit;

use App\Models\Activiteit;
use App\Models\Deelnameverzoek;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiviteitTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_beschikbaar_when_no_max(): void
    {
        $activiteit = Activiteit::factory()->create(['max_deelnemers' => null]);
        $this->assertTrue($activiteit->isBeschikbaar());
    }

    public function test_is_beschikbaar_when_under_max(): void
    {
        $activiteit = Activiteit::factory()->create(['max_deelnemers' => 5]);
        Deelnameverzoek::factory()->count(3)->create(['activiteit_id' => $activiteit->id]);
        $this->assertTrue($activiteit->isBeschikbaar());
    }

    public function test_is_not_beschikbaar_when_at_max(): void
    {
        $activiteit = Activiteit::factory()->create(['max_deelnemers' => 2]);
        Deelnameverzoek::factory()->count(2)->create(['activiteit_id' => $activiteit->id]);
        $this->assertFalse($activiteit->isBeschikbaar());
    }

    public function test_prijs_label_free_when_null(): void
    {
        $activiteit = Activiteit::factory()->make(['prijs' => null]);
        $this->assertEquals('Gratis', $activiteit->getPrijsLabel('nl'));
        $this->assertEquals('Gratuit', $activiteit->getPrijsLabel('fr'));
    }

    public function test_prijs_label_free_when_zero(): void
    {
        $activiteit = Activiteit::factory()->make(['prijs' => 0.00]);
        $this->assertEquals('Gratis', $activiteit->getPrijsLabel('nl'));
    }

    public function test_prijs_label_formatted_when_paid(): void
    {
        $activiteit = Activiteit::factory()->make(['prijs' => 5.00]);
        $this->assertEquals('€ 5,00', $activiteit->getPrijsLabel('nl'));
    }
}
