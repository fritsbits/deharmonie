<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deelnameverzoeken', function (Blueprint $table): void {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('deelnameverzoeken', function (Blueprint $table): void {
            $table->enum('status', ['te_contacteren', 'afgehandeld'])->default('te_contacteren')->after('bericht');
        });
    }
};
