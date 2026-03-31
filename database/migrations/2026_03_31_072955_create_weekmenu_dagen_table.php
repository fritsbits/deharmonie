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
        Schema::create('weekmenu_dagen', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->boolean('closed')->default(false);
            $table->boolean('special_event')->default(false);
            $table->unsignedSmallInteger('price')->nullable();
            $table->string('main_nl')->nullable();
            $table->string('main_fr')->nullable();
            $table->string('event_label_nl')->nullable();
            $table->string('event_label_fr')->nullable();
            $table->json('courses')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekmenu_dagen');
    }
};
