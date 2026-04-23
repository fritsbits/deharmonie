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

class ActiviteitBulkEditTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::where('email', config('auth.admin_email'))->firstOrFail();
    }

    public function test_bulk_publish_updates_selected_records(): void
    {
        $this->seed(AdminUserSeeder::class);

        $concepts = Activiteit::factory()->count(3)->vast()->create([
            'status' => ActiviteitStatus::Concept,
        ]);

        Livewire::actingAs($this->adminUser())
            ->test(ListActiviteiten::class)
            ->callTableBulkAction('publish', $concepts->pluck('id')->all());

        foreach ($concepts as $c) {
            $this->assertSame(ActiviteitStatus::Gepubliceerd, $c->fresh()->status);
        }
    }

    public function test_bulk_cancel_sets_geannuleerd(): void
    {
        $this->seed(AdminUserSeeder::class);

        $rows = Activiteit::factory()->count(2)->vast()->create([
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);

        Livewire::actingAs($this->adminUser())
            ->test(ListActiviteiten::class)
            ->callTableBulkAction('cancel', $rows->pluck('id')->all());

        foreach ($rows as $r) {
            $this->assertSame(ActiviteitStatus::Geannuleerd, $r->fresh()->status);
        }
    }

    public function test_bulk_edit_updates_only_filled_fields_on_selected_records(): void
    {
        $this->seed(AdminUserSeeder::class);

        $zumbas = Activiteit::factory()->count(3)->vast()->create([
            'titel_nl' => 'Zumba',
            'categorie' => Categorie::SportBeweging,
            'beschrijving_nl' => 'oude tekst',
        ]);
        $other = Activiteit::factory()->vast()->create([
            'titel_nl' => 'Bingo',
            'categorie' => Categorie::Spelletjes,
            'beschrijving_nl' => 'bingo blijft',
        ]);

        Livewire::actingAs($this->adminUser())
            ->test(ListActiviteiten::class)
            ->callTableBulkAction('bulk_edit', $zumbas->pluck('id')->all(), [
                'beschrijving_nl' => 'nieuwe tekst',
                'beschrijving_fr' => '',
                'locatie' => '',
                'prijs' => null,
            ]);

        foreach ($zumbas as $z) {
            $this->assertSame('nieuwe tekst', $z->fresh()->beschrijving_nl);
        }
        $this->assertSame('bingo blijft', $other->fresh()->beschrijving_nl);
    }
}
