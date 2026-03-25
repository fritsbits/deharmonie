<?php

namespace App\Http\Controllers;

use App\Models\Activiteit;

class ActivityController extends Controller
{
    public function home()
    {
        return view('activiteiten.index');
    }

    public function index()
    {
        $activiteiten = Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', now()->startOfDay())
            ->orderBy('datum')
            ->orderBy('startuur')
            ->get();
        return view('activiteiten.overzicht', compact('activiteiten'));
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
