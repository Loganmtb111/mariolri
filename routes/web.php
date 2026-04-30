<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\CustomerController;

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
    Route::delete('/films/{id}', [FilmController::class, 'destroy'])->name('films.destroy');

    // Routes gestion de stocks (inventaire)
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/create', [StockController::class, 'create'])->name('stocks.create');
    Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');
    Route::get('/stocks/{id}/edit', [StockController::class, 'edit'])->name('stocks.edit');
    Route::put('/stocks/{id}', [StockController::class, 'update'])->name('stocks.update');
    Route::delete('/stocks/{id}', [StockController::class, 'destroy'])->name('stocks.destroy');

    // Routes réservations
    Route::get('/rentals', [RentalController::class, 'index'])->name('rentals.index');
    Route::put('/rentals/{id}', [RentalController::class, 'update'])->name('rentals.update');

    // Routes clients
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // API pour récupérer les films d'un store
    Route::get('/api/stores/{storeId}/inventory', [StockController::class, 'getStoreInventory'])
        ->name('api.store.inventory');

    // API pour transférer les films d'un store à un autre
    Route::post('/api/stores/transfer', [StockController::class, 'transferInventory'])
        ->name('api.store.transfer');
});
