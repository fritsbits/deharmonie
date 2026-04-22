<?php

namespace Database\Seeders;

use App\Models\WeekMenuDag;
use Illuminate\Database\Seeder;

class WeekMenuDagSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(resource_path('data/weekmenu.json')), true);

        foreach ($data['days'] as $day) {
            WeekMenuDag::firstOrCreate(
                ['date' => $day['date']],
                [
                    'closed' => $day['closed'],
                    'special_event' => $day['special_event'],
                    'price' => $day['price'],
                    'main_nl' => $day['nl']['main'] ?? null,
                    'main_fr' => $day['fr']['main'] ?? null,
                    'event_label_nl' => $day['nl']['event_label'] ?? null,
                    'event_label_fr' => $day['fr']['event_label'] ?? null,
                    'courses' => $this->buildCourses($day),
                ]
            );
        }
    }

    private function buildCourses(array $day): ?array
    {
        $nl = $day['nl']['courses'] ?? [];
        $fr = $day['fr']['courses'] ?? [];

        if (empty($nl) && empty($fr)) {
            return null;
        }

        $courses = [];
        $count = max(count($nl), count($fr));
        for ($i = 0; $i < $count; $i++) {
            $courses[] = [
                'nl' => $nl[$i] ?? '',
                'fr' => $fr[$i] ?? '',
            ];
        }

        return $courses;
    }
}
