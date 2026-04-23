<?php

namespace Tests\Feature\Enums;

use App\Enums\Categorie;
use Tests\TestCase;

class CategorieEnumTest extends TestCase
{
    public function test_has_eight_cases(): void
    {
        $this->assertCount(8, Categorie::cases());
    }

    public function test_every_case_has_a_section(): void
    {
        $valid = ['beweeg', 'maak_leer', 'ontmoet_beleef'];
        foreach (Categorie::cases() as $cat) {
            $this->assertContains($cat->section(), $valid, "Categorie {$cat->name} returned invalid section");
        }
    }

    public function test_section_grouping_counts(): void
    {
        $bySection = collect(Categorie::cases())->groupBy(fn ($c) => $c->section());

        $this->assertCount(1, $bySection['beweeg']);          // sport_beweging
        $this->assertCount(2, $bySection['maak_leer']);       // creatief, bijleren
        $this->assertCount(5, $bySection['ontmoet_beleef']);  // ontmoeting, spelletjes, culinair, film_muziek, op_uitstap
    }

    public function test_every_case_returns_a_non_empty_icon(): void
    {
        foreach (Categorie::cases() as $cat) {
            $svg = $cat->icon();
            $this->assertNotEmpty($svg);
            $this->assertStringContainsString('<path', $svg);
        }
    }

    public function test_specific_label_examples(): void
    {
        $this->assertSame('Sport & beweging', Categorie::SportBeweging->getLabel());
        $this->assertSame('Ontmoeting', Categorie::Ontmoeting->getLabel());
        $this->assertSame('Op uitstap', Categorie::OpUitstap->getLabel());
    }

    public function test_french_label_examples(): void
    {
        $this->assertSame('Sport & mouvement', Categorie::SportBeweging->labelFr());
        $this->assertSame('Rencontre', Categorie::Ontmoeting->labelFr());
        $this->assertSame('En sortie', Categorie::OpUitstap->labelFr());
    }
}
