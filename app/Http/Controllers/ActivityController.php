<?php

namespace App\Http\Controllers;

use App\Models\Activiteit;
use App\Models\WeekMenuDag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function home()
    {
        $activiteiten = Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', now()->startOfDay())
            ->orderBy('datum')
            ->orderBy('startuur')
            ->limit(3)
            ->get();

        $menuVandaag = WeekMenuDag::whereDate('date', today())
            ->where('closed', false)
            ->first();

        $menuMorgen = WeekMenuDag::whereDate('date', today()->addDay())
            ->where('closed', false)
            ->first();

        return view('activiteiten.index', compact('activiteiten', 'menuVandaag', 'menuMorgen'));
    }

    public function index(): View
    {
        // TODO: Task 11 — Update this method to work without ActiviteitTemplate
        // For now, return minimal data. The overview page will be refactored.
        $reeksen = collect();

        $bijzondereActiviteiten = Activiteit::where('datum', '>=', today())
            ->where('status', 'gepubliceerd')
            ->orderBy('datum')
            ->limit(2)
            ->get();

        $nextActiviteiten = collect();

        return view('activiteiten.overzicht', compact('reeksen', 'bijzondereActiviteiten', 'nextActiviteiten'));
    }

    public function agenda(Request $request): View
    {
        $currentWeekStart = Carbon::now()->startOfWeek();
        $weekOffset = max(0, (int) $request->query('week', 0));

        // On initial load (no week param), jump to the first upcoming activity's week
        if (! $request->has('week')) {
            $first = Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
                ->where('datum', '>=', now()->toDateString())
                ->orderBy('datum')
                ->first();

            if ($first) {
                $weekOffset = (int) $currentWeekStart->diffInWeeks(
                    $first->datum->copy()->startOfWeek()
                );
            }
        }

        $weekStart = $currentWeekStart->copy()->addWeeks($weekOffset);
        $weekEnd = $weekStart->copy()->endOfWeek();

        $activiteiten = Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->whereBetween('datum', [$weekStart, $weekEnd])
            ->orderBy('datum')
            ->orderBy('startuur')
            ->get()
            ->groupBy(fn (Activiteit $a) => $a->datum->toDateString());

        // Prev: nearest earlier week that has an activity (from current week onward)
        $hasPrev = false;
        $prevWeek = 0;
        if ($weekOffset > 0) {
            $prev = Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
                ->where('datum', '>=', $currentWeekStart)
                ->where('datum', '<', $weekStart)
                ->orderByDesc('datum')
                ->first();

            if ($prev) {
                $hasPrev = true;
                $prevWeek = (int) $currentWeekStart->diffInWeeks(
                    $prev->datum->copy()->startOfWeek()
                );
            }
        }

        // Next: nearest later week that has an activity
        $hasNext = false;
        $nextWeek = $weekOffset;
        $next = Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', $weekStart->copy()->addWeek())
            ->orderBy('datum')
            ->first();

        if ($next) {
            $hasNext = true;
            $nextWeek = (int) $currentWeekStart->diffInWeeks(
                $next->datum->copy()->startOfWeek()
            );
        }

        $locale = app()->getLocale();
        $weekHeading = $weekStart->month === $weekEnd->month
            ? "{$weekStart->day}–{$weekEnd->day} ".$weekStart->locale($locale)->isoFormat('MMMM YYYY')
            : $weekStart->locale($locale)->isoFormat('D MMMM').' – '.$weekEnd->locale($locale)->isoFormat('D MMMM YYYY');

        return view('activiteiten.agenda', compact(
            'activiteiten', 'weekStart', 'weekHeading',
            'hasPrev', 'prevWeek', 'hasNext', 'nextWeek',
        ));
    }

    public function show(string $slug)
    {
        $activiteit = Activiteit::where('slug', $slug)->firstOrFail();

        return view('activiteiten.show', compact('activiteit'));
    }
}
