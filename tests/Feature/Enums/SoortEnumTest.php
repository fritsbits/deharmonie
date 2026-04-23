<?php

namespace Tests\Feature\Enums;

use App\Enums\Soort;
use Tests\TestCase;

class SoortEnumTest extends TestCase
{
    public function test_has_two_cases(): void
    {
        $this->assertCount(2, Soort::cases());
    }

    public function test_labels(): void
    {
        $this->assertSame('Vast', Soort::Vast->getLabel());
        $this->assertSame('Speciaal', Soort::Speciaal->getLabel());
    }
}
