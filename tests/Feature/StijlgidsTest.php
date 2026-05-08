<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class StijlgidsTest extends TestCase
{
    public function test_stijlgids_returns_200(): void
    {
        $response = $this->actingAs(User::factory()->make())->get('/nl/stijlgids');
        $response->assertStatus(200);
    }

    public function test_stijlgids_redirects_guests(): void
    {
        $this->get('/nl/stijlgids')->assertStatus(302);
    }

    public function test_stijlgids_has_all_section_anchors(): void
    {
        $response = $this->actingAs(User::factory()->make())->get('/nl/stijlgids');

        foreach ([
            'kleurenpalet', 'typografie', 'knoppen', 'formulieren', 'badges',
            'navigatie', 'hero', 'activiteitenlijst', 'activiteit-detail',
            'registratieformulier', 'diensten', 'voettekst',
        ] as $anchor) {
            $response->assertSee('id="'.$anchor.'"', false);
        }
    }

    public function test_stijlgids_is_noindex(): void
    {
        $response = $this->actingAs(User::factory()->make())->get('/nl/stijlgids');
        $response->assertSee('noindex', false);
    }
}
