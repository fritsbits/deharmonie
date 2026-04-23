<?php

namespace Tests\Feature\Filament;

use App\Enums\ActiviteitStatus;
use App\Enums\Categorie;
use App\Filament\Resources\ActiviteitResource\Pages\ListActiviteiten;
use App\Models\Activiteit;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ListActiviteitenTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::where('email', config('auth.admin_email'))->firstOrFail();
    }

    public function test_renders_activiteiten_with_week_group_header(): void
    {
        $this->seed(AdminUserSeeder::class);

        $wednesday = now()->next('Wednesday')->startOfDay();

        $activiteit = Activiteit::factory()->vast()->create([
            'titel_nl' => 'Zumba woensdag',
            'categorie' => Categorie::SportBeweging,
            'datum' => $wednesday->toDateString(),
            'startuur' => '14:00:00',
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);

        $component = Livewire::actingAs($this->adminUser())
            ->test(ListActiviteiten::class)
            ->assertSuccessful();

        // The table has 1 record
        $component->assertCountTableRecords(1);

        // The table is configured with groupsOnly + week_start Group
        $instance = $component->instance();
        $table = $instance->getTable();
        $this->assertTrue($table->isGroupsOnly());
        $this->assertNotNull($table->getDefaultGroup());
        $this->assertEquals('week_start', $table->getDefaultGroup()->getId());

        // The group title for the activiteit starts with 'WEEK VAN'
        $group = $table->getDefaultGroup();
        $groupTitle = $group->getTitle($activiteit);
        $this->assertStringStartsWith('WEEK VAN', $groupTitle);
    }

    public function test_header_actions_link_to_create_with_soort(): void
    {
        $this->seed(AdminUserSeeder::class);

        Livewire::actingAs($this->adminUser())
            ->test(ListActiviteiten::class)
            ->assertActionExists('createVast')
            ->assertActionExists('createSpeciaal');
    }
}
