<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add nullable columns temporarily.
        Schema::table('activiteiten', function (Blueprint $table): void {
            $table->string('locatie_nl')->nullable()->after('einduur');
            $table->string('locatie_fr')->nullable()->after('locatie_nl');
        });

        // 2. Backfill from existing locatie column.
        DB::table('activiteiten')->update([
            'locatie_nl' => DB::raw('locatie'),
            'locatie_fr' => DB::raw('locatie'),
        ]);

        // 3. Make NOT NULL with default.
        Schema::table('activiteiten', function (Blueprint $table): void {
            $table->string('locatie_nl')->nullable(false)->default('De Harmonie')->change();
            $table->string('locatie_fr')->nullable(false)->default('De Harmonie')->change();
        });

        // 4. Drop the old column.
        Schema::table('activiteiten', function (Blueprint $table): void {
            $table->dropColumn('locatie');
        });
    }

    public function down(): void
    {
        // 1. Re-add locatie column.
        Schema::table('activiteiten', function (Blueprint $table): void {
            $table->string('locatie')->nullable()->after('einduur');
        });

        // 2. Restore from locatie_nl.
        DB::table('activiteiten')->update([
            'locatie' => DB::raw('locatie_nl'),
        ]);

        // 3. Make NOT NULL with default.
        Schema::table('activiteiten', function (Blueprint $table): void {
            $table->string('locatie')->nullable(false)->default('De Harmonie')->change();
        });

        // 4. Drop the bilingual columns.
        Schema::table('activiteiten', function (Blueprint $table): void {
            $table->dropColumn(['locatie_nl', 'locatie_fr']);
        });
    }
};
