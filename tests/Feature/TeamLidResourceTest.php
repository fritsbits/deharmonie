<?php

namespace Tests\Feature;

use App\Models\TeamCategorie;
use App\Models\TeamLid;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamLidResourceTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::where('email', 'admin@deharmonie.be')->firstOrFail();
    }

    public function test_teamleden_index_page_renders(): void
    {
        $this->seed(AdminUserSeeder::class);

        $response = $this->actingAs($this->adminUser())->get('/admin/teamleden');

        $response->assertStatus(200);
    }

    public function test_teamleden_index_shows_team_member(): void
    {
        $this->seed(AdminUserSeeder::class);

        $categorie = TeamCategorie::factory()->create(['naam_nl' => 'Onthaal', 'volgorde' => 1]);
        TeamLid::factory()->create([
            'team_categorie_id' => $categorie->id,
            'naam' => 'Deborah Monfils',
            'volgorde' => 1,
        ]);

        $response = $this->actingAs($this->adminUser())->get('/admin/teamleden');

        $response->assertStatus(200);
        $response->assertSee('Deborah Monfils');
    }

    public function test_create_team_lid_page_renders(): void
    {
        $this->seed(AdminUserSeeder::class);

        $response = $this->actingAs($this->adminUser())->get('/admin/teamleden/create');

        $response->assertStatus(200);
    }

    public function test_edit_team_lid_page_renders(): void
    {
        $this->seed(AdminUserSeeder::class);

        $categorie = TeamCategorie::factory()->create(['volgorde' => 1]);
        $lid = TeamLid::factory()->create(['team_categorie_id' => $categorie->id]);

        $response = $this->actingAs($this->adminUser())->get("/admin/teamleden/{$lid->id}/edit");

        $response->assertStatus(200);
    }
}
