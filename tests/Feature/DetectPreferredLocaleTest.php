<?php

namespace Tests\Feature;

use App\Models\Activiteit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetectPreferredLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_french_browser_is_redirected_to_fr_on_first_visit(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'fr-BE,fr;q=0.9,nl;q=0.5'])
            ->get('/');

        $response->assertRedirect();
        $this->assertStringContainsString('/fr', $response->headers->get('Location'));
        $response->assertCookie('preferred_locale', 'fr');
    }

    public function test_dutch_browser_is_not_redirected(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'nl-BE,nl;q=0.9'])
            ->get('/');

        $response->assertStatus(200);
        $response->assertCookie('preferred_locale', 'nl');
    }

    public function test_cookie_prevents_redirect_on_subsequent_visits(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'fr-BE'])
            ->withCookie('preferred_locale', 'nl')
            ->get('/');

        $response->assertStatus(200); // No redirect despite French header
    }

    public function test_fr_routes_are_not_affected_by_middleware(): void
    {
        // Middleware only runs on NL group; visiting FR directly should always work
        $response = $this->withHeaders(['Accept-Language' => 'nl-BE'])
            ->get('/fr');

        $response->assertStatus(200);
    }

    public function test_french_browser_on_activity_page_redirects_to_fr_equivalent(): void
    {
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);

        $response = $this->withHeaders(['Accept-Language' => 'fr-BE'])
            ->get('/activiteiten/'.$activiteit->slug);

        $response->assertRedirect();
        $this->assertStringContainsString('/fr/activites/'.$activiteit->slug, $response->headers->get('Location'));
    }
}
