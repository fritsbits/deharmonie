<?php

use App\Models\Activiteit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Repair slugs left behind by the old generator: titles without sluggable
     * characters (emoji only) produced "" and, for copies, "-2", "-3", ...
     * An empty slug crashes every page that links to the activity.
     */
    public function up(): void
    {
        DB::table('activiteiten')
            ->where('slug', '')
            ->orWhere('slug', 'like', '-%')
            ->orderBy('id')
            ->get(['id', 'slug', 'titel_nl', 'titel_fr'])
            ->filter(fn (object $row): bool => preg_match('/^(-\d+)?$/', $row->slug) === 1)
            ->each(fn (object $row) => DB::table('activiteiten')
                ->where('id', $row->id)
                ->update(['slug' => Activiteit::generateUniqueSlug($row->titel_nl, $row->titel_fr)]));
    }

    public function down(): void
    {
        // Data migration — not reversible.
    }
};
