<?php

namespace App\Http\Controllers;

use App\Models\Activiteit;

class ActivityController extends Controller
{
    public function index()
    {
        return view('activiteiten.index');
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
