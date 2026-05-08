<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FooterAdminLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_link_hidden_for_guests(): void
    {
        $response = $this->get('/nl');

        $response->assertStatus(200);
        $response->assertDontSee(url('/admin'));
        $response->assertDontSee('Beheer');
    }

    public function test_admin_link_visible_when_admin_logged_in_nl(): void
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::where('email', config('auth.admin_email'))->firstOrFail();

        $response = $this->actingAs($admin)->get('/nl');

        $response->assertStatus(200);
        $response->assertSee(url('/admin'));
        $response->assertSee('Beheer');
    }

    public function test_admin_link_visible_when_admin_logged_in_fr(): void
    {
        $this->seed(AdminUserSeeder::class);
        $admin = User::where('email', config('auth.admin_email'))->firstOrFail();

        $response = $this->actingAs($admin)->get('/fr/activites');

        $response->assertStatus(200);
        $response->assertSee(url('/admin'));
        $response->assertSee('Administration');
    }

    public function test_admin_link_hidden_for_non_admin_user(): void
    {
        $user = User::factory()->create(['email' => 'not-an-admin@example.com']);

        $response = $this->actingAs($user)->get('/nl');

        $response->assertStatus(200);
        $response->assertDontSee('Beheer');
    }
}
