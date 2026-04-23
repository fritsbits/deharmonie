<?php

namespace Tests\Feature\Filament;

use App\Enums\ActiviteitStatus;
use App\Enums\Categorie;
use App\Enums\Soort;
use App\Filament\Resources\ActiviteitResource\Pages\ListActiviteiten;
use App\Models\Activiteit;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class ActiviteitKopieerTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::where('email', config('auth.admin_email'))->firstOrFail();
    }

    public function test_kopieer_weekly_creates_n_rows_with_concept_status(): void
    {
        $this->seed(AdminUserSeeder::class);

        $original = Activiteit::factory()->vast()->create([
            'titel_nl' => 'Zumba',
            'categorie' => Categorie::SportBeweging,
            'datum' => now()->next('Tuesday')->toDateString(),
            'startuur' => '14:00:00',
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);

        $start = now()->addWeeks(2)->toDateString();
        $einde = now()->addWeeks(5)->toDateString();

        Livewire::actingAs($this->adminUser())
            ->test(ListActiviteiten::class)
            ->callTableAction('kopieer', $original, [
                'mode' => 'wekelijks',
                'start' => $start,
                'einde' => $einde,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertGreaterThanOrEqual(3, Activiteit::where('titel_nl', 'Zumba')->count());

        $copies = Activiteit::where('titel_nl', 'Zumba')
            ->where('id', '!=', $original->id)
            ->get();
        foreach ($copies as $c) {
            $this->assertSame(Categorie::SportBeweging, $c->categorie);
            $this->assertSame(Soort::Vast, $c->soort);
            $this->assertSame(ActiviteitStatus::Concept, $c->status);
        }
    }

    public function test_kopieer_specific_dates_creates_those_dates(): void
    {
        $this->seed(AdminUserSeeder::class);

        $original = Activiteit::factory()->speciaal()->create([
            'titel_nl' => 'Museumbezoek',
            'categorie' => Categorie::OpUitstap,
            'datum' => now()->addWeek()->toDateString(),
            'startuur' => '13:00:00',
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);

        $datum1 = now()->addWeeks(2)->toDateString();
        $datum2 = now()->addWeeks(4)->toDateString();

        Livewire::actingAs($this->adminUser())
            ->test(ListActiviteiten::class)
            ->callTableAction('kopieer', $original, [
                'mode' => 'specifiek',
                'datums' => [
                    (string) Str::uuid() => ['datum' => $datum1],
                    (string) Str::uuid() => ['datum' => $datum2],
                ],
            ])
            ->assertHasNoTableActionErrors();

        $copies = Activiteit::where('titel_nl', 'Museumbezoek')
            ->where('id', '!=', $original->id)
            ->orderBy('datum')
            ->get();
        $this->assertCount(2, $copies);
        $this->assertSame($datum1, $copies[0]->datum->toDateString());
        $this->assertSame($datum2, $copies[1]->datum->toDateString());
        $this->assertSame(Soort::Speciaal, $copies[0]->soort);
        $this->assertSame(Categorie::OpUitstap, $copies[0]->categorie);
    }
}
