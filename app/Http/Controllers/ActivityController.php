<?php

namespace App\Http\Controllers;

use App\Models\Activiteit;
use App\Models\ActiviteitTemplate;
use App\Models\WeekMenuDag;
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
        $reeksen = ActiviteitTemplate::orderBy('dag_van_de_week')
            ->orderBy('startuur')
            ->get()
            ->keyBy('id');

        $bijzondereActiviteiten = Activiteit::whereNull('template_id')
            ->where('datum', '>=', today())
            ->where('status', 'gepubliceerd')
            ->orderBy('datum')
            ->limit(2)
            ->get();

        $nextActiviteiten = Activiteit::whereNotNull('template_id')
            ->where('datum', '>=', today())
            ->where('status', 'gepubliceerd')
            ->orderBy('datum')
            ->get()
            ->groupBy('template_id')
            ->map(fn ($group) => $group->first());

        return view('activiteiten.overzicht', compact('reeksen', 'bijzondereActiviteiten', 'nextActiviteiten'));
    }

    public function agenda(): View
    {
        return view('activiteiten.agenda');
    }

    public function show(string $slug)
    {
        $activiteit = Activiteit::where('slug', $slug)->firstOrFail();

        return view('activiteiten.show', compact('activiteit'));
    }
}
