<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('over_ons_content', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('jaarverslag_jaar')->nullable();
            $table->string('impact_1_aantal', 20);
            $table->string('impact_1_omschrijving_nl', 120);
            $table->string('impact_1_omschrijving_fr', 120);
            $table->string('impact_2_aantal', 20);
            $table->string('impact_2_omschrijving_nl', 120);
            $table->string('impact_2_omschrijving_fr', 120);
            $table->string('impact_3_aantal', 20);
            $table->string('impact_3_omschrijving_nl', 120);
            $table->string('impact_3_omschrijving_fr', 120);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('over_ons_content');
    }
};
