<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\EditProfile;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
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

    public function test_admin_can_change_password_with_valid_current_password(): void
    {
        $this->seed(AdminUserSeeder::class);
        $admin = $this->adminUser();
        $admin->password = Hash::make('old-password-123');
        $admin->save();

        Livewire::actingAs($admin)
            ->test(EditProfile::class)
            ->fillForm([
                'currentPassword' => 'old-password-123',
                'password' => 'NewStrongPass!99',
                'passwordConfirmation' => 'NewStrongPass!99',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $admin->refresh();
        $this->assertTrue(Hash::check('NewStrongPass!99', $admin->password));
    }

    public function test_wrong_current_password_blocks_change(): void
    {
        $this->seed(AdminUserSeeder::class);
        $admin = $this->adminUser();
        $admin->password = Hash::make('old-password-123');
        $admin->save();
        $originalHash = $admin->password;

        Livewire::actingAs($admin)
            ->test(EditProfile::class)
            ->fillForm([
                'currentPassword' => 'wrong-password',
                'password' => 'NewStrongPass!99',
                'passwordConfirmation' => 'NewStrongPass!99',
            ])
            ->call('save')
            ->assertHasFormErrors(['currentPassword']);

        $this->assertSame($originalHash, $admin->refresh()->password);
    }
}
