<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Backfill map: existing template titel_nl -> categorie value.
     */
    private const TEMPLATE_TO_CAT = [
        'Sociale infopunt' => 'ontmoeting',
        'Maandelijks verjaardagsfeest' => 'ontmoeting',
        'Conversatietafel Spaans' => 'ontmoeting',
        'Conversatietafel Engels' => 'ontmoeting',
        'Conversatietafel Italiaans' => 'ontmoeting',
        'Nederlandse conversatietafel' => 'ontmoeting',
        'Country Line Dance' => 'sport_beweging',
        'Geheugenatelier' => 'bijleren',
        'Stoel-gym met Nicole' => 'sport_beweging',
        'Digitale workshop' => 'bijleren',
        'Bingo' => 'spelletjes',
        'Creativiteit workshop' => 'creatief',
        'Zumba' => 'sport_beweging',
        'Diamond Painting Workshop met Nadia' => 'creatief',
        'Naaiworkshop' => 'creatief',
        'Boodschappendienst' => 'ontmoeting',
        'Pilates & Fitness' => 'sport_beweging',
        'Jeu de Tables: Dominos' => 'spelletjes',
        'Jeu de Tables: Jacquet' => 'spelletjes',
    ];

    /**
     * Keyword map for speciale momenten (Activiteiten with template_id NULL).
     * First match wins; order matters (specific before broad).
     */
    private const KEYWORD_TO_CAT = [
        ['needles' => ['museum', 'musée', 'expo', 'tentoon', 'kunst'], 'cat' => 'op_uitstap'],
        ['needles' => ['wandel', 'balade', 'marche'], 'cat' => 'op_uitstap'],
        ['needles' => ['brunch', 'buffet', 'aperitief', 'apéro', 'apero', 'koffie', 'café', 'confituur', 'culinair', 'cuisine', 'gouter', 'goûter', 'ontbijt', 'lunch', 'diner', 'dîner', 'souper'], 'cat' => 'culinair'],
        ['needles' => ['festival', 'concert', 'musette', 'klassiek'], 'cat' => 'film_muziek'],
        ['needles' => ['documentaire', 'film', 'theater', 'théâtre', 'voorstelling', 'debat'], 'cat' => 'film_muziek'],
        ['needles' => ['feest', 'verjaardag', 'inhuldiging', 'fête', 'fete'], 'cat' => 'ontmoeting'],
        ['needles' => ['haken', 'naai', 'breien', 'diamond'], 'cat' => 'creatief'],
        ['needles' => ['atelier', 'workshop'], 'cat' => 'creatief'],
        ['needles' => ['woordspelletjes', 'scrabble', 'jeu', 'domino', 'jacquet', 'bingo', 'kaart'], 'cat' => 'spelletjes'],
        ['needles' => ['geheugen', 'mémoire', 'brein'], 'cat' => 'bijleren'],
        ['needles' => ['digitaal', 'numérique', 'computer', 'cursus'], 'cat' => 'bijleren'],
        ['needles' => ['conversatie', 'startbabbel', 'praat', 'tafel'], 'cat' => 'ontmoeting'],
        ['needles' => ['infopunt', 'spreekuur', 'permanentie', 'loket'], 'cat' => 'ontmoeting'],
        ['needles' => ['zumba', 'dans'], 'cat' => 'sport_beweging'],
        ['needles' => ['gym', 'pilates', 'fitness', 'yoga'], 'cat' => 'sport_beweging'],
    ];

    private const FALLBACK_CAT = 'ontmoeting';

    public function up(): void
    {
        // 1. Add columns nullable so we can backfill.
        Schema::table('activiteiten', function (Blueprint $table): void {
            $table->string('soort')->nullable()->after('status');
            $table->string('categorie')->nullable()->after('soort');
        });

        // 2. Backfill soort.
        DB::table('activiteiten')->whereNull('template_id')->update(['soort' => 'speciaal']);
        DB::table('activiteiten')->whereNotNull('template_id')->update(['soort' => 'vast']);

        // 3. Backfill categorie for vaste activiteiten via the template map.
        if (Schema::hasTable('activiteit_templates')) {
            $templates = DB::table('activiteit_templates')->get(['id', 'titel_nl']);
            foreach ($templates as $tpl) {
                $cat = self::TEMPLATE_TO_CAT[$tpl->titel_nl] ?? null;
                if ($cat === null) {
                    Log::warning('[soort-backfill] no template->cat map for "'.$tpl->titel_nl.'" (id='.$tpl->id.'), defaulting to '.self::FALLBACK_CAT);
                    $cat = self::FALLBACK_CAT;
                }
                DB::table('activiteiten')
                    ->where('template_id', $tpl->id)
                    ->update(['categorie' => $cat]);
            }
        }

        // 4. Backfill categorie for speciale momenten via keyword scan.
        $speciaal = DB::table('activiteiten')
            ->where('soort', 'speciaal')
            ->get(['id', 'titel_nl']);

        foreach ($speciaal as $row) {
            $cat = $this->matchKeyword($row->titel_nl);
            if ($cat === null) {
                Log::warning('[soort-backfill] keyword fallback for "'.$row->titel_nl.'" (id='.$row->id.')');
                $cat = self::FALLBACK_CAT;
            }
            DB::table('activiteiten')
                ->where('id', $row->id)
                ->update(['categorie' => $cat]);
        }

        // 5. Make NOT NULL (only after backfill).
        Schema::table('activiteiten', function (Blueprint $table): void {
            $table->string('soort')->nullable(false)->change();
            $table->string('categorie')->nullable(false)->change();
        });

        // 6. Drop template_id FK + column.
        Schema::table('activiteiten', function (Blueprint $table): void {
            if (Schema::hasColumn('activiteiten', 'template_id')) {
                try {
                    $table->dropForeign(['template_id']);
                } catch (Throwable) {
                    // FK may not exist if originally added without one — ignore.
                }
                $table->dropColumn('template_id');
            }
        });

        // 7. Drop the templates table.
        Schema::dropIfExists('activiteit_templates');
    }

    public function down(): void
    {
        Schema::create('activiteit_templates', function (Blueprint $table): void {
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
            $table->tinyInteger('dag_van_de_week');
            $table->date('reeks_start');
            $table->date('reeks_einde');
            $table->timestamps();
        });

        Schema::table('activiteiten', function (Blueprint $table): void {
            $table->foreignId('template_id')->nullable()->after('status')->constrained('activiteit_templates')->nullOnDelete();
            $table->dropColumn(['soort', 'categorie']);
        });
    }

    private function matchKeyword(?string $title): ?string
    {
        if ($title === null) {
            return null;
        }
        $haystack = Str::lower($title);
        foreach (self::KEYWORD_TO_CAT as $rule) {
            foreach ($rule['needles'] as $needle) {
                if (str_contains($haystack, $needle)) {
                    return $rule['cat'];
                }
            }
        }

        return null;
    }
};
