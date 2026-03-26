<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function diensten()
    {
        return view('pages.diensten');
    }

    public function weekmenu()
    {
        $data = json_decode(file_get_contents(resource_path('data/weekmenu.json')), true);

        $now = now();
        $candidate = $now->hour >= 14 ? $now->copy()->addDay() : $now->copy();

        $highlightedDate = null;
        foreach ($data['days'] as $day) {
            if ($day['date'] >= $candidate->toDateString() && ! $day['closed']) {
                $highlightedDate = $day['date'];
                break;
            }
        }

        return view('pages.weekmenu', [
            'week' => $data['week'],
            'days' => $data['days'],
            'highlightedDate' => $highlightedDate,
        ]);
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
