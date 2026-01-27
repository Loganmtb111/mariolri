<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\StockController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Routes films protégées par authentification
Route::middleware('auth')->group(function () {
    Route::get('/films', [FilmController::class, 'index'])->name('films.index');
    Route::get('/films/create', [FilmController::class, 'create'])->name('films.create');
    Route::post('/films', [FilmController::class, 'store'])->name('films.store');
    Route::get('/films/{id}', [FilmController::class, 'show'])->name('films.show');
    Route::get('/films/{id}/edit', [FilmController::class, 'edit'])->name('films.edit');
    Route::put('/films/{id}', [FilmController::class, 'update'])->name('films.update');

    // Routes gestion de stocks
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');

    // API pour récupérer les films d'un store
    Route::get('/api/stores/{storeId}/inventory', [StockController::class, 'getStoreInventory'])
        ->name('api.store.inventory');

    // API pour transférer les films d'un store à un autre
    Route::post('/api/stores/transfer', [StockController::class, 'transferInventory'])
        ->name('api.store.transfer');
});
