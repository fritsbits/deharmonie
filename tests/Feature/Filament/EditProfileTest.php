<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditProfileTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::where('email', config('auth.admin_email'))->firstOrFail();
    }

    public function test_profile_page_renders_for_authenticated_admin(): void
    {
        $this->seed(AdminUserSeeder::class);

        $response = $this->actingAs($this->adminUser())->get('/admin/profile');

        $response->assertStatus(200);
    }

    public function test_profile_page_redirects_guest_to_login(): void
    {
        $response = $this->get('/admin/profile');

        $response->assertRedirect('/admin/login');
    }
}
