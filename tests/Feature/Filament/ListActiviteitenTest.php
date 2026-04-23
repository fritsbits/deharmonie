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
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$activiteit])
            ->assertSee('Zumba woensdag');
        // Group header shows the localized week range (e.g. "27 juni – 3 juli").
        $weekStart = $wednesday->copy()->startOfWeek()->locale('nl');
        $component->assertSee($weekStart->isoFormat('D MMMM'));

        // The table is configured with a week_start Group as default.
        $table = $component->instance()->getTable();
        $this->assertNotNull($table->getDefaultGroup());
        $this->assertEquals('week_start', $table->getDefaultGroup()->getId());
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
