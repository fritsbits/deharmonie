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

    public function test_admin_can_update_impact_stats_and_jaarverslag_year(): void
    {
        $this->seed(AdminUserSeeder::class);
        OverOnsContent::factory()->create();

        Livewire::actingAs($this->adminUser())
            ->test(ManageOverOnsContent::class)
            ->fillForm([
                'jaarverslag_jaar' => 2027,
                'impact_1_aantal' => '300',
                'impact_1_omschrijving_nl' => 'NL stat 1',
                'impact_1_omschrijving_fr' => 'FR stat 1',
                'impact_2_aantal' => '5000',
                'impact_2_omschrijving_nl' => 'NL stat 2',
                'impact_2_omschrijving_fr' => 'FR stat 2',
                'impact_3_aantal' => '70+',
                'impact_3_omschrijving_nl' => 'NL stat 3',
                'impact_3_omschrijving_fr' => 'FR stat 3',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('over_ons_content', [
            'id' => 1,
            'jaarverslag_jaar' => 2027,
            'impact_1_aantal' => '300',
            'impact_2_aantal' => '5000',
            'impact_3_aantal' => '70+',
            'impact_3_omschrijving_fr' => 'FR stat 3',
        ]);
    }

    public function test_required_fields_block_save_when_blank(): void
    {
        $this->seed(AdminUserSeeder::class);
        OverOnsContent::factory()->create();

        Livewire::actingAs($this->adminUser())
            ->test(ManageOverOnsContent::class)
            ->fillForm([
                'impact_1_aantal' => '',
                'impact_1_omschrijving_nl' => '',
            ])
            ->call('save')
            ->assertHasFormErrors([
                'impact_1_aantal' => 'required',
                'impact_1_omschrijving_nl' => 'required',
            ]);
    }
}
