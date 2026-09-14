<?php

namespace Tests\Feature\Migrations;

use App\Models\Activiteit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RepairEmptyActiviteitSlugsTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_and_suffix_only_slugs_are_repaired(): void
    {
        $healthy = Activiteit::factory()->create(['slug' => 'expo-kheops-2']);

        // Rows as the old slug generator left them: bypass the model hook.
        $emptyId = $this->insertRaw('', '🚩 ⛃ 🖌️', 'Jeux de société');
        $suffixOnlyId = $this->insertRaw('-2', '🚩 ⛃ 🖌️', '🚩 ⛃ 🖌️');

        (require database_path('migrations/2026_09_14_093748_repair_empty_activiteit_slugs.php'))->up();

        $this->assertSame('jeux-de-societe', Activiteit::find($emptyId)->slug);
        $this->assertSame('activiteit', Activiteit::find($suffixOnlyId)->slug);
        $this->assertSame('expo-kheops-2', $healthy->fresh()->slug);

        foreach (['/nl/activiteiten/agenda?week=0', '/fr/activites/agenda?week=0'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    private function insertRaw(string $slug, string $titelNl, string $titelFr): int
    {
        $attributes = Activiteit::factory()->raw([
            'slug' => $slug,
            'titel_nl' => $titelNl,
            'titel_fr' => $titelFr,
            'datum' => now()->toDateString(),
        ]);

        return DB::table('activiteiten')->insertGetId([
            ...collect($attributes)->map(fn ($value) => $value instanceof \BackedEnum ? $value->value : $value)->all(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
