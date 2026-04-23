<?php

namespace Tests\Feature\Filament;

use App\Enums\ActiviteitStatus;
use App\Enums\Categorie;
use App\Enums\Soort;
use App\Filament\Resources\ActiviteitResource\Pages\CreateActiviteit;
use App\Models\Activiteit;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ActiviteitCreateVastTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::where('email', config('auth.admin_email'))->firstOrFail();
    }

    public function test_create_vast_with_weekly_recurrence_creates_n_rows(): void
    {
        $this->seed(AdminUserSeeder::class);

        $start = now()->next('Tuesday')->startOfDay();
        $end = $start->copy()->addWeeks(4); // 5 sessions including the start

        Livewire::actingAs($this->adminUser())
            ->withQueryParams(['soort' => 'vast'])
            ->test(CreateActiviteit::class)
            ->fillForm([
                'titel_nl' => 'Zumba',
                'titel_fr' => 'Zumba',
                'datum' => $start->toDateString(),
                'startuur' => '14:00',
                'einduur' => '15:00',
                'locatie' => 'De Harmonie',
                'categorie' => Categorie::SportBeweging->value,
                'status' => ActiviteitStatus::Gepubliceerd->value,
                'herhaal_wekelijks' => true,
                'herhaal_t_m' => $end->toDateString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $zumbas = Activiteit::where('titel_nl', 'Zumba')->orderBy('datum')->get();
        $this->assertCount(5, $zumbas);
        foreach ($zumbas as $z) {
            $this->assertSame(Soort::Vast, $z->soort);
            $this->assertSame(Categorie::SportBeweging, $z->categorie);
        }
        // The first row keeps the chosen status; subsequent rows are concept.
        $this->assertSame(ActiviteitStatus::Gepubliceerd, $zumbas->first()->status);
        $this->assertSame(ActiviteitStatus::Concept, $zumbas->last()->status);
    }

    public function test_create_vast_without_recurrence_creates_only_one(): void
    {
        $this->seed(AdminUserSeeder::class);

        Livewire::actingAs($this->adminUser())
            ->withQueryParams(['soort' => 'vast'])
            ->test(CreateActiviteit::class)
            ->fillForm([
                'titel_nl' => 'Eenmalige Bingo',
                'titel_fr' => 'Bingo unique',
                'datum' => now()->next('Wednesday')->toDateString(),
                'startuur' => '14:00',
                'einduur' => '16:00',
                'locatie' => 'De Harmonie',
                'categorie' => Categorie::Spelletjes->value,
                'status' => ActiviteitStatus::Gepubliceerd->value,
                'herhaal_wekelijks' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(1, Activiteit::where('titel_nl', 'Eenmalige Bingo')->count());
    }
}
