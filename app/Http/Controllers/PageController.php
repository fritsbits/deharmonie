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
        return view('pages.weekmenu');
    }

    public function wieIsWie()
    {
        return view('pages.wie-is-wie');
    }
}
