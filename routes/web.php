<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\DarazController;
use App\Http\Controllers\NaheedController;

// ── Main pages ────────────────────────────────────────────────────────────────
Route::get('/',                fn() => view('home'));         // OmniPrice homepage
Route::get('/about',           fn() => view('about'));
Route::get('/contact',         fn() => view('contact'));
Route::get('/privacy',         fn() => view('privacy'));
Route::get('/category/{slug}', fn($slug) => view('category', ['slug' => $slug]))->name('category');

// ── Unified search (used by homepage + category pages) ───────────────────────
Route::get('/search', [SearchController::class, 'search']);

// ── Daraz standalone debug routes (keep for your teammate's testing) ──────────
Route::get('/daraz',       [DarazController::class, 'index']);   // standalone Daraz UI
Route::post('/run-scraper',[DarazController::class, 'fetch']);
Route::get('/debug-data',  [DarazController::class, 'debug']);   // test Daraz scraper alone

// ── PriceOye debug (remove in production) ────────────────────────────────────
Route::get('/debug-priceoye', [\App\Http\Controllers\PriceoyeDebugController::class, 'run']);

// Debugging ke liye:
Route::get('/debug-data', [DarazController::class, 'debug']);
Route::get('/naheed', [NaheedController::class, 'fetchProducts']);

Route::get('/dvago', [\App\Http\Controllers\DvagoController::class, 'fetchProducts']);
Route::get('/sehat', [\App\Http\Controllers\SehatController::class, 'fetchProducts']);