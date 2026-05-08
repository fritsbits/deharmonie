<?php

namespace Tests\Feature;

use Tests\TestCase;

class RootRedirectTest extends TestCase
{
    public function test_dutch_browser_is_redirected_to_nl_home(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'nl-BE,nl;q=0.9'])
            ->get('/');

        $response->assertRedirect('/nl');
        $response->assertCookie('preferred_locale', 'nl');
    }

    public function test_french_browser_is_redirected_to_fr_home(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'fr-BE,fr;q=0.9,nl;q=0.5'])
            ->get('/');

        $response->assertRedirect('/fr');
        $response->assertCookie('preferred_locale', 'fr');
    }

    public function test_preferred_locale_cookie_overrides_accept_language(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'fr-BE'])
            ->withCookie('preferred_locale', 'nl')
            ->get('/');

        $response->assertRedirect('/nl');
        $response->assertCookie('preferred_locale', 'nl');
    }

    public function test_unknown_accept_language_falls_back_to_nl(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'de-DE,de;q=0.9'])
            ->get('/');

        $response->assertRedirect('/nl');
        $response->assertCookie('preferred_locale', 'nl');
    }
}
