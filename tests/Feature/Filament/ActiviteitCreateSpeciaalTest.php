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

class ActiviteitCreateSpeciaalTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::where('email', config('auth.admin_email'))->firstOrFail();
    }

    public function test_creating_speciaal_via_query_string_sets_soort(): void
    {
        $this->seed(AdminUserSeeder::class);

        // Simulate the request query parameter the create button passes.
        request()->query->set('soort', 'speciaal');

        Livewire::actingAs($this->adminUser())
            ->test(CreateActiviteit::class)
            ->fillForm([
                'titel_nl' => 'Eenmalige uitstap',
                'titel_fr' => 'Sortie unique',
                'datum' => now()->addWeek()->toDateString(),
                'startuur' => '14:00',
                'einduur' => '16:00',
                'locatie' => 'Brussel',
                'categorie' => Categorie::OpUitstap->value,
                'status' => ActiviteitStatus::Concept->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $created = Activiteit::where('titel_nl', 'Eenmalige uitstap')->first();
        $this->assertNotNull($created);
        $this->assertSame(Soort::Speciaal, $created->soort);
        $this->assertSame(Categorie::OpUitstap, $created->categorie);
    }

    public function test_creating_without_query_string_defaults_to_speciaal(): void
    {
        $this->seed(AdminUserSeeder::class);

        // No ?soort= in the request.
        Livewire::actingAs($this->adminUser())
            ->test(CreateActiviteit::class)
            ->fillForm([
                'titel_nl' => 'Iets eenmaligs',
                'titel_fr' => 'Quelque chose',
                'datum' => now()->addDays(5)->toDateString(),
                'startuur' => '10:00',
                'einduur' => '11:00',
                'locatie' => 'De Harmonie',
                'categorie' => Categorie::Ontmoeting->value,
                'status' => ActiviteitStatus::Concept->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $created = Activiteit::where('titel_nl', 'Iets eenmaligs')->first();
        $this->assertNotNull($created);
        $this->assertSame(Soort::Speciaal, $created->soort);
    }
}
