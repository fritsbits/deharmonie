<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function diensten() { return view('pages.diensten'); }
    public function weekmenu() { return view('pages.weekmenu'); }
    public function contact() { return view('pages.contact'); }
}
