<?php

use Database\Seeders\ActiviteitSeeder;
use Database\Seeders\WeekMenuDagSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Data migration — skip in unit tests so feature tests can start from a
        // clean slate. Seeders are idempotent, so re-running this on future
        // deploys would be safe, but it only runs once per environment.
        if (app()->runningUnitTests()) {
            return;
        }

        // ActiviteitTemplateSeeder removed in Task 7 when the template model was deleted.
        // The activiteit_templates table was dropped in Task 5.
        // Guard: only run ActiviteitSeeder once soort + categorie columns exist
        // (they are added by a later migration; on fresh installs we skip here
        // and let DatabaseSeeder handle it after all migrations have run).
        if (Schema::hasColumn('activiteiten', 'soort') && Schema::hasColumn('activiteiten', 'categorie')) {
            (new ActiviteitSeeder)->run();
        }
        (new WeekMenuDagSeeder)->run();
    }

    public function down(): void
    {
        // Data migration — not reversible.
    }
};
