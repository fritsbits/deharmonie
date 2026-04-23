<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\TeamCategorieResource\Pages\CreateTeamCategorie;
use App\Filament\Resources\TeamCategorieResource\Pages\EditTeamCategorie;
use App\Filament\Resources\TeamCategorieResource\Pages\ListTeamCategorieen;
use App\Models\TeamCategorie;
use App\Models\TeamLid;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TeamCategorieResourceTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::where('email', 'admin@deharmonie.be')->firstOrFail();
    }

    public function test_index_page_renders(): void
    {
        $this->seed(AdminUserSeeder::class);

        $response = $this->actingAs($this->adminUser())->get('/admin/teamcategorieen');

        $response->assertStatus(200);
    }

    public function test_create_page_renders(): void
    {
        $this->seed(AdminUserSeeder::class);

        $response = $this->actingAs($this->adminUser())->get('/admin/teamcategorieen/create');

        $response->assertStatus(200);
    }

    public function test_edit_page_renders(): void
    {
        $this->seed(AdminUserSeeder::class);

        $categorie = TeamCategorie::factory()->create(['volgorde' => 1]);

        $response = $this->actingAs($this->adminUser())->get("/admin/teamcategorieen/{$categorie->id}/edit");

        $response->assertStatus(200);
    }

    public function test_can_create_categorie_with_nl_and_fr_names(): void
    {
        $this->seed(AdminUserSeeder::class);

        Livewire::actingAs($this->adminUser())
            ->test(CreateTeamCategorie::class)
            ->fillForm([
                'naam_nl' => 'Vrijwilligers',
                'naam_fr' => 'Bénévoles',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('team_categorieen', [
            'naam_nl' => 'Vrijwilligers',
            'naam_fr' => 'Bénévoles',
        ]);
    }

    public function test_can_edit_categorie_names(): void
    {
        $this->seed(AdminUserSeeder::class);

        $categorie = TeamCategorie::factory()->create([
            'naam_nl' => 'Oude naam',
            'naam_fr' => 'Ancien nom',
            'volgorde' => 1,
        ]);

        Livewire::actingAs($this->adminUser())
            ->test(EditTeamCategorie::class, [
                'record' => $categorie->getRouteKey(),
            ])
            ->fillForm([
                'naam_nl' => 'Nieuwe naam',
                'naam_fr' => 'Nouveau nom',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $categorie->refresh();
        $this->assertSame('Nieuwe naam', $categorie->naam_nl);
        $this->assertSame('Nouveau nom', $categorie->naam_fr);
    }

    public function test_naam_nl_and_naam_fr_are_required(): void
    {
        $this->seed(AdminUserSeeder::class);

        Livewire::actingAs($this->adminUser())
            ->test(CreateTeamCategorie::class)
            ->fillForm(['naam_nl' => '', 'naam_fr' => ''])
            ->call('create')
            ->assertHasFormErrors(['naam_nl' => 'required', 'naam_fr' => 'required']);
    }

    public function test_lists_categorieen_in_volgorde_order(): void
    {
        $this->seed(AdminUserSeeder::class);

        $third = TeamCategorie::factory()->create(['naam_nl' => 'Derde', 'volgorde' => 30]);
        $first = TeamCategorie::factory()->create(['naam_nl' => 'Eerste', 'volgorde' => 10]);
        $second = TeamCategorie::factory()->create(['naam_nl' => 'Tweede', 'volgorde' => 20]);

        Livewire::actingAs($this->adminUser())
            ->test(ListTeamCategorieen::class)
            ->assertCanSeeTableRecords([$first, $second, $third], inOrder: true);
    }

    public function test_table_can_be_reordered_by_volgorde(): void
    {
        $this->seed(AdminUserSeeder::class);

        $a = TeamCategorie::factory()->create(['naam_nl' => 'A', 'volgorde' => 1]);
        $b = TeamCategorie::factory()->create(['naam_nl' => 'B', 'volgorde' => 2]);
        $c = TeamCategorie::factory()->create(['naam_nl' => 'C', 'volgorde' => 3]);

        Livewire::actingAs($this->adminUser())
            ->test(ListTeamCategorieen::class)
            ->call('reorderTable', [$c->id, $a->id, $b->id]);

        $this->assertSame(1, $c->fresh()->volgorde);
        $this->assertSame(2, $a->fresh()->volgorde);
        $this->assertSame(3, $b->fresh()->volgorde);
    }

    public function test_leden_count_column_renders_count_per_categorie(): void
    {
        $this->seed(AdminUserSeeder::class);

        $categorie = TeamCategorie::factory()->create(['volgorde' => 1]);
        TeamLid::factory()->count(3)->create(['team_categorie_id' => $categorie->id]);

        Livewire::actingAs($this->adminUser())
            ->test(ListTeamCategorieen::class)
            ->assertCanSeeTableRecords([$categorie])
            ->assertTableColumnStateSet('leden_count', 3, $categorie);
    }

    public function test_delete_blocked_when_leden_exist(): void
    {
        $this->seed(AdminUserSeeder::class);

        $categorie = TeamCategorie::factory()->create(['volgorde' => 1]);
        TeamLid::factory()->create(['team_categorie_id' => $categorie->id]);

        Livewire::actingAs($this->adminUser())
            ->test(ListTeamCategorieen::class)
            ->callTableAction('delete', $categorie);

        $this->assertDatabaseHas('team_categorieen', ['id' => $categorie->id]);
        $this->assertSame(1, TeamLid::where('team_categorie_id', $categorie->id)->count());
    }

    public function test_delete_succeeds_when_categorie_is_empty(): void
    {
        $this->seed(AdminUserSeeder::class);

        $categorie = TeamCategorie::factory()->create(['volgorde' => 1]);

        Livewire::actingAs($this->adminUser())
            ->test(ListTeamCategorieen::class)
            ->callTableAction('delete', $categorie);

        $this->assertDatabaseMissing('team_categorieen', ['id' => $categorie->id]);
    }

    public function test_bulk_delete_skips_categorieen_with_leden(): void
    {
        $this->seed(AdminUserSeeder::class);

        $empty = TeamCategorie::factory()->create(['volgorde' => 1]);
        $populated = TeamCategorie::factory()->create(['volgorde' => 2]);
        TeamLid::factory()->create(['team_categorie_id' => $populated->id]);

        Livewire::actingAs($this->adminUser())
            ->test(ListTeamCategorieen::class)
            ->callTableBulkAction('delete', [$empty->id, $populated->id]);

        $this->assertDatabaseMissing('team_categorieen', ['id' => $empty->id]);
        $this->assertDatabaseHas('team_categorieen', ['id' => $populated->id]);
    }

    public function test_create_appends_new_categorie_at_bottom(): void
    {
        $this->seed(AdminUserSeeder::class);

        TeamCategorie::factory()->create(['volgorde' => 1]);
        TeamCategorie::factory()->create(['volgorde' => 2]);
        TeamCategorie::factory()->create(['volgorde' => 7]);

        Livewire::actingAs($this->adminUser())
            ->test(CreateTeamCategorie::class)
            ->fillForm([
                'naam_nl' => 'Nieuwste',
                'naam_fr' => 'Plus récente',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(8, TeamCategorie::where('naam_nl', 'Nieuwste')->value('volgorde'));
    }
}
