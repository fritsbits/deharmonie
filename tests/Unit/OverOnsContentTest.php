<?php

namespace Tests\Unit;

use App\Models\OverOnsContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OverOnsContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_creates_singleton_when_missing(): void
    {
        $first = OverOnsContent::current();
        $second = OverOnsContent::current();

        $this->assertSame(1, $first->id);
        $this->assertSame(1, $second->id);
        $this->assertSame(1, OverOnsContent::count());
    }

    public function test_impact_omschrijving_returns_locale_specific_value(): void
    {
        $content = OverOnsContent::factory()->create([
            'impact_1_omschrijving_nl' => 'wekelijks bij ons',
            'impact_1_omschrijving_fr' => 'chaque semaine',
        ]);

        app()->setLocale('nl');
        $this->assertSame('wekelijks bij ons', $content->impactOmschrijving(1));

        app()->setLocale('fr');
        $this->assertSame('chaque semaine', $content->impactOmschrijving(1));
    }

    public function test_jaarverslag_url_is_null_without_media(): void
    {
        $content = OverOnsContent::factory()->create();

        $this->assertNull($content->getJaarverslagUrl());
        $this->assertNull($content->getJaarverslagSize());
    }
}
