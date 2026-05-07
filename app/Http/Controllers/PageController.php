<?php

namespace App\Http\Controllers;

use App\Models\OverOnsContent;
use App\Models\TeamCategorie;
use App\Models\WeekMenuDag;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function weekmenuPrint(Request $request): View
    {
        $weekOffset = (int) $request->query('week', 0);
        $locale = app()->getLocale();

        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeeks($weekOffset);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $days = WeekMenuDag::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('date')
            ->get();

        if ($days->isNotEmpty()) {
            $first = $days->first()->date->locale($locale);
            $last = $days->last()->date->locale($locale);
            $weekLabel = $first->month === $last->month
                ? $first->isoFormat('D').' – '.$last->isoFormat('D MMMM YYYY')
                : $first->isoFormat('D MMMM').' – '.$last->isoFormat('D MMMM YYYY');
        } else {
            $weekLabel = $weekStart->locale($locale)->isoFormat('D MMM')
                .' – '
                .$weekEnd->locale($locale)->isoFormat('D MMM YYYY');
        }

        return view('pages.weekmenu-print', compact('days', 'weekLabel', 'locale'));
    }

    public function diensten()
    {
        return view('pages.diensten');
    }

    public function weekmenu(): View
    {
        return view('pages.weekmenu');
    }

    public function overOns(): View
    {
        return view('pages.over-ons', [
            'content' => OverOnsContent::current(),
        ]);
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function vrijwilligers(): View
    {
        return view('pages.vrijwilligers');
    }

    public function wieIsWie(): View
    {
        $categorieen = TeamCategorie::with(['leden' => fn ($q) => $q->orderBy('naam')])
            ->orderBy('volgorde')
            ->get();

        return view('pages.wie-is-wie', compact('categorieen'));
    }
}
