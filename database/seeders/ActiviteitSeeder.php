<?php

namespace Database\Seeders;

use App\Enums\ActiviteitStatus;
use App\Models\Activiteit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ActiviteitSeeder extends Seeder
{
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

    public function run(): void
    {
        $csvPath = database_path('seeders/data/activities.csv');

        $handle = fopen($csvPath, 'r');
        $headers = array_map('trim', fgetcsv($handle, 0, ',', '"', ''));

        $rows = [];

        while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
            if (count($row) !== count($headers)) {
                continue;
            }

            $data = array_combine($headers, $row);

            if (($data['Draft'] ?? '') === 'true' || ($data['Archived'] ?? '') === 'true') {
                continue;
            }

            $rawDate = $data['Date/Time'] ?? '';
            if (empty($rawDate)) {
                continue;
            }

            try {
                $cleanDate = preg_replace('/\s*\(.*?\)/', '', $rawDate);
                $datum = Carbon::parse(trim($cleanDate))->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }

            $startuur = $this->parseTime($data['TimeStart'] ?? '') ?? '00:00:00';
            $einduur = $this->parseTime($data['TimeEnd'] ?? '');

            $prijs = $data['Price'] ?? '';
            $prijs = ($prijs === '' || $prijs === '0') ? null : (float) $prijs;

            $statusRaw = strtolower(trim($data['Status'] ?? ''));
            $status = match ($statusRaw) {
                'gepubliceerd' => ActiviteitStatus::Gepubliceerd,
                'geannuleerd' => ActiviteitStatus::Geannuleerd,
                default => ActiviteitStatus::Concept,
            };

            $titelNl = $this->stripEmoji(trim($data['Name'] ?? ''));
            $titelFr = $this->stripEmoji(trim($data['Name FR'] ?? ''));
            if ($titelFr === '') {
                $titelFr = $titelNl;
            }

            $rows[] = [
                'slug' => trim($data['Slug'] ?? ''),
                'titel_nl' => $titelNl,
                'titel_fr' => $titelFr,
                'beschrijving_nl' => $data['Description'] !== '' ? $data['Description'] : null,
                'beschrijving_fr' => $data['Description FR'] !== '' ? $data['Description FR'] : null,
                'notice_nl' => $data['Notice'] !== '' ? $data['Notice'] : null,
                'notice_fr' => $data['Notice FR'] !== '' ? $data['Notice FR'] : null,
                'datum' => $datum,
                'startuur' => $startuur,
                'einduur' => $einduur,
                'locatie_nl' => $data['Location'] !== '' ? $data['Location'] : 'De Harmonie',
                'locatie_fr' => $data['Location'] !== '' ? $data['Location'] : 'De Harmonie',
                'prijs' => $prijs,
                'status' => $status->value,
                'soort' => $this->resolveSoort($titelNl),
                'categorie' => $this->resolveCategorie($titelNl),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        fclose($handle);

        $inserted = 0;
        foreach (array_chunk($rows, 100) as $chunk) {
            $inserted += Activiteit::insertOrIgnore($chunk);
        }

        $skipped = count($rows) - $inserted;
        $this->command?->info("Imported {$inserted} new activities from CSV ({$skipped} already existed, skipped).");
    }

    private function resolveSoort(string $titleNl): string
    {
        return array_key_exists($titleNl, self::TEMPLATE_TO_CAT) ? 'vast' : 'speciaal';
    }

    private function resolveCategorie(string $titleNl): string
    {
        if (isset(self::TEMPLATE_TO_CAT[$titleNl])) {
            return self::TEMPLATE_TO_CAT[$titleNl];
        }

        $haystack = Str::lower($titleNl);
        foreach (self::KEYWORD_TO_CAT as $rule) {
            foreach ($rule['needles'] as $needle) {
                if (str_contains($haystack, $needle)) {
                    return $rule['cat'];
                }
            }
        }

        return self::FALLBACK_CAT;
    }

    private function stripEmoji(string $title): string
    {
        $title = preg_replace('/[\x{1F000}-\x{1FFFF}]|[\x{2600}-\x{27BF}]|[\x{FE00}-\x{FEFF}]|\x{200D}/u', '', $title);

        return trim(preg_replace('/\s+/', ' ', $title));
    }

    private function parseTime(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        // Normalize Dutch format: "14u00" or "14u" → "14:00"
        $value = preg_replace('/(\d{1,2})u(\d{2})/i', '$1:$2', $value);
        $value = preg_replace('/(\d{1,2})u$/i', '$1:00', $value);
        // Ensure HH:MM:SS format
        if (preg_match('/^\d{1,2}:\d{2}$/', $value)) {
            return $value.':00';
        }
        // If it already has seconds
        if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $value)) {
            return $value;
        }

        return null;
    }
}
