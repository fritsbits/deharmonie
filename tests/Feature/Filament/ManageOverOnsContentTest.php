<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\ManageOverOnsContent;
use App\Models\OverOnsContent;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageOverOnsContentTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::where('email', config('auth.admin_email'))->firstOrFail();
    }

    public function test_page_renders_for_authenticated_admin(): void
    {
        $this->seed(AdminUserSeeder::class);

        $response = $this->actingAs($this->adminUser())->get('/admin/over-ons');

        $response->assertStatus(200);
    }

    public function test_page_redirects_guest_to_login(): void
    {
        $response = $this->get('/admin/over-ons');

        $response->assertRedirect('/admin/login');
    }

    public function test_form_is_prefilled_with_existing_record(): void
    {
        $this->seed(AdminUserSeeder::class);
        OverOnsContent::factory()->create([
            'jaarverslag_jaar' => 2026,
            'impact_1_aantal' => '321',
        ]);

        Livewire::actingAs($this->adminUser())
            ->test(ManageOverOnsContent::class)
            ->assertFormSet([
                'jaarverslag_jaar' => 2026,
                'impact_1_aantal' => '321',
            ]);
    }
}
