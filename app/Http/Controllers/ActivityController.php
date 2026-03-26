<?php

namespace App\Http\Controllers;

use App\Models\Activiteit;

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

    public function index()
    {
        return view('activiteiten.overzicht');
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
