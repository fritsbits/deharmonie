<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_nl_homepage_returns_a_successful_response(): void
    {
        $response = $this->get('/nl');

        $response->assertStatus(200);
    }
}
