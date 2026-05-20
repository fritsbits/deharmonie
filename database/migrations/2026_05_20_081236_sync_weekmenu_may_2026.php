<?php

use Database\Seeders\WeekMenuDagSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Data migration — skip in unit tests so feature tests can start from a
        // clean slate. The seeder uses firstOrCreate keyed on date, so re-running
        // is safe and won't overwrite rows the CMS has since edited.
        if (app()->runningUnitTests()) {
            return;
        }

        (new WeekMenuDagSeeder)->run();
    }

    public function down(): void
    {
        // Data migration — not reversible.
    }
};
