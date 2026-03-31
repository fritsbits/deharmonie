<?php

namespace Tests\Feature;

use Tests\TestCase;

class DienstenPageTest extends TestCase
{
    public function test_nl_diensten_page_renders(): void
    {
        $response = $this->get(route('nl.diensten'));

        $response->assertStatus(200);
        $response->assertSee('Eten');
        $response->assertSee('Begeleiding');
        $response->assertSee('Thuis');
        $response->assertSee('Sociaal restaurant');
        $response->assertSee('Boodschappendienst');
    }

    public function test_fr_diensten_page_renders(): void
    {
        $response = $this->get(route('fr.diensten'));

        $response->assertStatus(200);
        $response->assertSee('Repas');
        $response->assertSee('Accompagnement');
        $response->assertSee('domicile');
        $response->assertSee('Restaurant social');
        $response->assertSee('courses');
    }
}
