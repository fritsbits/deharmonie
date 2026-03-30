<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PageController;
use App\Http\Middleware\DetectPreferredLocale;
use Illuminate\Support\Facades\Route;

Route::get('/set-locale/{locale}', [LocaleController::class, 'switch'])
    ->name('set-locale')
    ->where('locale', 'nl|fr');

// NL routes (default, no prefix)
Route::middleware(['locale:nl', DetectPreferredLocale::class])->group(function () {
    Route::get('/', [ActivityController::class, 'home'])->name('nl.home');
    Route::get('/activiteiten', [ActivityController::class, 'index'])->name('nl.activiteiten.index');
    Route::get('/activiteiten/agenda', [ActivityController::class, 'agenda'])->name('nl.activiteiten.agenda');
    Route::get('/activiteiten/{slug}', [ActivityController::class, 'show'])->name('nl.activiteiten.show');
    Route::get('/restaurant-menu', [PageController::class, 'weekmenu'])->name('nl.weekmenu');
    Route::get('/restaurant-menu/print', [PageController::class, 'weekmenuPrint'])->name('nl.weekmenu.print');
    Route::get('/diensten', [PageController::class, 'diensten'])->name('nl.diensten');
    Route::get('/over-ons', [PageController::class, 'overOns'])->name('nl.over-ons');
    Route::get('/contact', [PageController::class, 'contact'])->name('nl.contact');
    Route::get('/wie-is-wie', [PageController::class, 'wieIsWie'])->name('nl.wie-is-wie');
});

// FR routes
Route::prefix('fr')->middleware('locale:fr')->group(function () {
    Route::get('/', [ActivityController::class, 'home'])->name('fr.home');
    Route::get('/activites', [ActivityController::class, 'index'])->name('fr.activiteiten.index');
    Route::get('/activites/agenda', [ActivityController::class, 'agenda'])->name('fr.activiteiten.agenda');
    Route::get('/activites/{slug}', [ActivityController::class, 'show'])->name('fr.activiteiten.show');
    Route::get('/restaurant-menu', [PageController::class, 'weekmenu'])->name('fr.weekmenu');
    Route::get('/restaurant-menu/print', [PageController::class, 'weekmenuPrint'])->name('fr.weekmenu.print');
    Route::get('/services', [PageController::class, 'diensten'])->name('fr.diensten');
    Route::get('/a-propos', [PageController::class, 'overOns'])->name('fr.over-ons');
    Route::get('/contact', [PageController::class, 'contact'])->name('fr.contact');
    Route::get('/qui-est-qui', [PageController::class, 'wieIsWie'])->name('fr.wie-is-wie');
});

// Stijlgids (internal design system reference — not linked publicly)
Route::middleware('locale:nl')->get('/stijlgids', fn () => view('stijlgids'))->name('stijlgids');
