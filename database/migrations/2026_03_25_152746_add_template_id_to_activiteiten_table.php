<?php

use App\Models\ActiviteitTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activiteiten', function (Blueprint $table) {
            $table->foreignId('template_id')
                ->nullable()
                ->after('id')
                ->constrained('activiteit_templates')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activiteiten', function (Blueprint $table) {
            $table->dropForeignIdFor(ActiviteitTemplate::class);
            $table->dropColumn('template_id');
        });
    }
};
