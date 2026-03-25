<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/set-locale/{locale}', [LocaleController::class, 'switch'])
    ->name('set-locale')
    ->where('locale', 'nl|fr');

// NL routes (default, no prefix)
Route::middleware('locale:nl')->group(function () {
    Route::get('/', [ActivityController::class, 'home'])->name('nl.home');
    Route::get('/activiteiten', [ActivityController::class, 'index'])->name('nl.activiteiten.index');
    Route::get('/activiteiten/{slug}', [ActivityController::class, 'show'])->name('nl.activiteiten.show');
    Route::get('/activiteiten/{slug}/print', [ActivityController::class, 'print'])->name('nl.activiteiten.print');
    Route::get('/diensten', [PageController::class, 'diensten'])->name('nl.diensten');
    Route::get('/weekmenu', [PageController::class, 'weekmenu'])->name('nl.weekmenu');
    Route::get('/contact', [PageController::class, 'contact'])->name('nl.contact');
    Route::get('/wie-is-wie', [PageController::class, 'wieIsWie'])->name('nl.wie-is-wie');
});

// FR routes
Route::prefix('fr')->middleware('locale:fr')->group(function () {
    Route::get('/', [ActivityController::class, 'home'])->name('fr.home');
    Route::get('/activites', [ActivityController::class, 'index'])->name('fr.activiteiten.index');
    Route::get('/activites/{slug}', [ActivityController::class, 'show'])->name('fr.activiteiten.show');
    Route::get('/activites/{slug}/imprimer', [ActivityController::class, 'print'])->name('fr.activiteiten.print');
    Route::get('/services', [PageController::class, 'diensten'])->name('fr.diensten');
    Route::get('/menu-semaine', [PageController::class, 'weekmenu'])->name('fr.weekmenu');
    Route::get('/contact', [PageController::class, 'contact'])->name('fr.contact');
    Route::get('/qui-est-qui', [PageController::class, 'wieIsWie'])->name('fr.wie-is-wie');
});

// Stijlgids (internal design system reference — not linked publicly)
Route::middleware('locale:nl')->get('/stijlgids', fn () => view('stijlgids'))->name('stijlgids');
