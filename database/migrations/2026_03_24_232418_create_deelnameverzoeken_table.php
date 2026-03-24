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
        Schema::create('deelnameverzoeken', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activiteit_id')->constrained('activiteiten')->cascadeOnDelete();
            $table->string('naam');
            $table->string('email');
            $table->string('telefoon')->nullable();
            $table->text('bericht')->nullable();
            $table->enum('status', ['te_contacteren', 'afgehandeld'])->default('te_contacteren');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deelnameverzoeken');
    }
};
