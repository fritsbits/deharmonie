<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activiteiten', function (Blueprint $table): void {
            $table->index('datum');
        });
    }

    public function down(): void
    {
        Schema::table('activiteiten', function (Blueprint $table): void {
            $table->dropIndex(['datum']);
        });
    }
};
