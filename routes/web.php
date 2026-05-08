<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PageController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/set-locale/{locale}', [LocaleController::class, 'switch'])
    ->name('set-locale')
    ->where('locale', 'nl|fr');

// Bare root: detect preferred locale and redirect to /nl or /fr
Route::get('/', [LocaleController::class, 'detect'])->name('root');

// Legacy URL redirects from the previous site (www.deharmonie.be)
Route::redirect('/activiteiten', '/nl/activiteiten', 301);
Route::redirect('/diensten', '/nl/over-ons', 301);
Route::redirect('/wie-is-wie', '/nl/wie-is-wie', 301);

// NL routes
Route::prefix('nl')->middleware(SetLocale::class)->group(function () {
    Route::get('/', [ActivityController::class, 'home'])->name('nl.home');
    Route::get('/activiteiten', [ActivityController::class, 'index'])->name('nl.activiteiten.index');
    Route::get('/activiteiten/agenda', [ActivityController::class, 'agenda'])->name('nl.activiteiten.agenda');
    Route::get('/activiteiten/{slug}', [ActivityController::class, 'show'])->name('nl.activiteiten.show');
    Route::get('/restaurant-menu', [PageController::class, 'weekmenu'])->name('nl.weekmenu');
    Route::get('/restaurant-menu/print', [PageController::class, 'weekmenuPrint'])->name('nl.weekmenu.print');
    Route::get('/over-ons', [PageController::class, 'overOns'])->name('nl.over-ons');
    Route::get('/contact', [PageController::class, 'contact'])->name('nl.contact');
    Route::get('/vrijwilligers', [PageController::class, 'vrijwilligers'])->name('nl.vrijwilligers');
    Route::get('/wie-is-wie', [PageController::class, 'wieIsWie'])->name('nl.wie-is-wie');
});

// FR routes
Route::prefix('fr')->middleware(SetLocale::class)->group(function () {
    Route::get('/', [ActivityController::class, 'home'])->name('fr.home');
    Route::get('/activites', [ActivityController::class, 'index'])->name('fr.activiteiten.index');
    Route::get('/activites/agenda', [ActivityController::class, 'agenda'])->name('fr.activiteiten.agenda');
    Route::get('/activites/{slug}', [ActivityController::class, 'show'])->name('fr.activiteiten.show');
    Route::get('/restaurant-menu', [PageController::class, 'weekmenu'])->name('fr.weekmenu');
    Route::get('/restaurant-menu/print', [PageController::class, 'weekmenuPrint'])->name('fr.weekmenu.print');
    Route::get('/a-propos', [PageController::class, 'overOns'])->name('fr.over-ons');
    Route::get('/contact', [PageController::class, 'contact'])->name('fr.contact');
    Route::get('/benevoles', [PageController::class, 'vrijwilligers'])->name('fr.vrijwilligers');
    Route::get('/qui-est-qui', [PageController::class, 'wieIsWie'])->name('fr.wie-is-wie');
});

// Stijlgids (internal design system reference — auth required, NL only)
Route::prefix('nl')->middleware([SetLocale::class, 'auth'])
    ->get('/stijlgids', fn () => view('stijlgids'))
    ->name('stijlgids');

// Categorie icon variants preview — temporary, for icon selection
Route::get('/_dev/icon-preview', fn () => view('dev.icon-preview'));
