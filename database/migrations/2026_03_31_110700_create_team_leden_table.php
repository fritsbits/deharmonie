<?php

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
        Schema::create('team_leden', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_categorie_id')->constrained('team_categorieen')->cascadeOnDelete();
            $table->string('naam');
            $table->string('titel_nl')->nullable();
            $table->string('titel_fr')->nullable();
            $table->unsignedInteger('volgorde')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_leden');
    }
};
