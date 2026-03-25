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
        Schema::create('activiteit_templates', function (Blueprint $table) {
            $table->id();
            $table->string('titel_nl');
            $table->string('titel_fr');
            $table->text('beschrijving_nl')->nullable();
            $table->text('beschrijving_fr')->nullable();
            $table->text('notice_nl')->nullable();
            $table->text('notice_fr')->nullable();
            $table->time('startuur');
            $table->time('einduur')->nullable();
            $table->string('locatie')->default('De Harmonie');
            $table->decimal('prijs', 8, 2)->nullable();
            $table->integer('max_deelnemers')->nullable();
            $table->string('interesse')->nullable();
            $table->unsignedTinyInteger('dag_van_de_week'); // 0=Sun, 1=Mon, ..., 6=Sat (Carbon convention)
            $table->date('reeks_start');
            $table->date('reeks_einde');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activiteit_templates');
    }
};
