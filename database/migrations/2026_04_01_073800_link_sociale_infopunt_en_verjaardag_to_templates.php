<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('activiteiten')
            ->whereIn('id', [575, 576, 577, 578, 579, 580, 581])
            ->update(['template_id' => 16]);

        DB::table('activiteiten')
            ->whereIn('id', [236, 622, 623, 624, 625, 626, 627, 628])
            ->update(['template_id' => 17]);
    }

    public function down(): void
    {
        DB::table('activiteiten')
            ->whereIn('id', [575, 576, 577, 578, 579, 580, 581])
            ->update(['template_id' => null]);

        DB::table('activiteiten')
            ->whereIn('id', [236, 622, 623, 624, 625, 626, 627, 628])
            ->update(['template_id' => null]);
    }
};
