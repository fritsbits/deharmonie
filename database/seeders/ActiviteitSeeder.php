<?php

namespace Database\Seeders;

use App\Enums\ActiviteitStatus;
use App\Models\Activiteit;
use App\Models\ActiviteitTemplate;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ActiviteitSeeder extends Seeder
{
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
                'locatie' => $data['Location'] !== '' ? $data['Location'] : 'De Harmonie',
                'prijs' => $prijs,
                'status' => $status->value,
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

        $this->linkTemplateSessions();
    }

    private function linkTemplateSessions(): void
    {
        $templates = ActiviteitTemplate::all();

        if ($templates->isEmpty()) {
            return;
        }

        // Build a map of normalized template title → template id
        $templateMap = $templates->mapWithKeys(fn (ActiviteitTemplate $t) => [
            $this->normalizeTitle($t->titel_nl) => $t->id,
        ]);

        $linked = 0;

        Activiteit::whereNull('template_id')->chunkById(200, function ($activiteiten) use ($templateMap, &$linked) {
            foreach ($activiteiten as $activiteit) {
                $normalized = $this->normalizeTitle($activiteit->titel_nl);
                if (isset($templateMap[$normalized])) {
                    $activiteit->update(['template_id' => $templateMap[$normalized]]);
                    $linked++;
                }
            }
        });

        $this->command?->info("Linked {$linked} activities to templates.");
    }

    private function stripEmoji(string $title): string
    {
        $title = preg_replace('/[\x{1F000}-\x{1FFFF}]|[\x{2600}-\x{27BF}]|[\x{FE00}-\x{FEFF}]|\x{200D}/u', '', $title);

        return trim(preg_replace('/\s+/', ' ', $title));
    }

    private function normalizeTitle(string $title): string
    {
        $title = $this->stripEmoji($title);
        // Strip "NIEUW : " prefix
        $title = preg_replace('/^nieuw\s*:\s*/i', '', $title);
        // Strip " Copy N" or " copie N" suffixes
        $title = preg_replace('/\s+(copy|copie)\s*\d*\s*$/i', '', $title);
        // Strip trailing punctuation like "!"
        $title = preg_replace('/[!?]+/', '', $title);

        return strtolower(trim(preg_replace('/\s+/', ' ', $title)));
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
