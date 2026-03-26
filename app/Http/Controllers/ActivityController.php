<?php

namespace App\Http\Controllers;

use App\Models\Activiteit;
use App\Models\ActiviteitTemplate;
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

        return view('activiteiten.index', compact('activiteiten'));
    }

    public function index(): View
    {
        $reeksen = ActiviteitTemplate::orderBy('dag_van_de_week')
            ->orderBy('startuur')
            ->get();

        return view('activiteiten.overzicht', compact('reeksen'));
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

    public function print(string $slug)
    {
        $activiteit = Activiteit::where('slug', $slug)->firstOrFail();

        return view('activiteiten.print', compact('activiteit'));
    }
}
