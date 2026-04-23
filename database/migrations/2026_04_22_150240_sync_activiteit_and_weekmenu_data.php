<?php

use Database\Seeders\ActiviteitSeeder;
use Database\Seeders\WeekMenuDagSeeder;
use Illuminate\Database\Migrations\Migration;

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
        (new ActiviteitSeeder)->run();
        (new WeekMenuDagSeeder)->run();
    }

    public function down(): void
    {
        // Data migration — not reversible.
    }
};
