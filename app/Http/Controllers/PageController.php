<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function weekmenuPrint(Request $request): View
    {
        $weekOffset = (int) $request->query('week', 0);
        $data = json_decode(file_get_contents(resource_path('data/weekmenu.json')), true);
        $locale = app()->getLocale();

        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeeks($weekOffset);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $days = array_values(array_filter(
            $data['days'],
            fn ($day) => $day['date'] >= $weekStart->toDateString()
                && $day['date'] <= $weekEnd->toDateString()
        ));

        if (! empty($days)) {
            $first = Carbon::parse($days[0]['date'])->locale($locale);
            $last = Carbon::parse(end($days)['date'])->locale($locale);
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

    public function overOns()
    {
        return view('pages.over-ons');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function wieIsWie()
    {
        return view('pages.wie-is-wie');
    }
}
