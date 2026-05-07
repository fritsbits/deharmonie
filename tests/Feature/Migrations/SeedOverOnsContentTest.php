<?php

namespace Tests\Feature\Migrations;

use App\Models\OverOnsContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeedOverOnsContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_content_inserts_singleton_with_baseline_values(): void
    {
        Storage::fake('public');
        $migration = $this->loadMigration();

        $migration->seedContent();

        $content = OverOnsContent::current();
        $this->assertSame(2025, $content->jaarverslag_jaar);
        $this->assertSame('250', $content->impact_1_aantal);
        $this->assertSame('4500', $content->impact_2_aantal);
        $this->assertSame('60+', $content->impact_3_aantal);
    }

    public function test_seed_content_is_idempotent(): void
    {
        Storage::fake('public');
        $migration = $this->loadMigration();

        $migration->seedContent();
        $migration->seedContent();

        $this->assertSame(1, OverOnsContent::count());
    }

    public function test_seed_content_copies_existing_pdf_into_media_collection(): void
    {
        Storage::fake('public');
        $sourcePath = public_path('docs/jaarverslag-2025.pdf');
        $backupPath = sys_get_temp_dir().'/jaarverslag-2025-backup.pdf';
        $hadOriginal = file_exists($sourcePath);

        if ($hadOriginal) {
            copy($sourcePath, $backupPath);
        }

        if (! is_dir(dirname($sourcePath))) {
            mkdir(dirname($sourcePath), 0755, true);
        }
        file_put_contents($sourcePath, '%PDF-1.4 fake');

        try {
            $migration = $this->loadMigration();
            $migration->seedContent();

            $media = OverOnsContent::current()->getMedia('jaarverslag');
            $this->assertSame(1, $media->count());
            $this->assertFileDoesNotExist($sourcePath);
        } finally {
            if ($hadOriginal && file_exists($backupPath)) {
                copy($backupPath, $sourcePath);
                unlink($backupPath);
            } elseif (file_exists($sourcePath)) {
                unlink($sourcePath);
            }
        }
    }

    private function loadMigration(): object
    {
        $files = glob(database_path('migrations/*_seed_over_ons_content.php'));
        $this->assertNotEmpty($files, 'seed_over_ons_content migration not found');

        return require $files[0];
    }
}
