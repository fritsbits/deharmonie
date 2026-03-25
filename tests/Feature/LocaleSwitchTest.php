<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    public function test_set_locale_redirects_and_sets_cookie(): void
    {
        $response = $this->get('/set-locale/fr?redirect=/fr');

        $response->assertRedirect('/fr');
        $response->assertCookie('preferred_locale', 'fr');
    }

    public function test_set_locale_nl_sets_cookie(): void
    {
        $response = $this->get('/set-locale/nl?redirect=/');

        $response->assertRedirect('/');
        $response->assertCookie('preferred_locale', 'nl');
    }

    public function test_set_locale_rejects_invalid_locale(): void
    {
        // Route constraint 'nl|fr' means any other value returns 404
        $response = $this->get('/set-locale/de?redirect=/');
        $response->assertStatus(404);
    }

    public function test_set_locale_rejects_external_redirect(): void
    {
        $response = $this->get('/set-locale/fr?redirect=https://evil.com');

        // Should redirect to '/' not to evil.com
        $response->assertRedirect('/');
    }

    public function test_set_locale_allows_relative_redirect(): void
    {
        $response = $this->get('/set-locale/fr?redirect=/fr/services');

        $response->assertRedirect('/fr/services');
    }
}
