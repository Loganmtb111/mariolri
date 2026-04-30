<?php

namespace App\Http\Controllers;

use App\Services\ToadRentalService;
use App\Services\ToadFilmService;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    private ToadRentalService $rentalService;
    private ToadFilmService $filmService;

    public function __construct(ToadRentalService $rentalService, ToadFilmService $filmService)
    {
        $this->middleware('auth');
        $this->rentalService = $rentalService;
        $this->filmService = $filmService;
    }

    public function index()
    {
        $rentals = $this->rentalService->getAllRentals() ?? [];
        $filmsRentalCount = $this->filmService->getFilmsWithRentalCount() ?? [];

        return view('rentals.index', [
            'rentals'          => $rentals,
            'filmsRentalCount' => $filmsRentalCount,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = [
            'inventoryId' => (int) $request->input('inventoryId'),
            'customerId'  => (int) $request->input('customerId'),
            'staffId'     => (int) $request->input('staffId'),
            'statusId'    => 1,
            'rentalDate'  => $request->input('rentalDate'),
            'returnDate'  => now()->format('Y-m-d\TH:i:s'),
        ];

        $success = $this->rentalService->updateRental((int) $id, $data);

        if ($success) {
            return redirect()->route('rentals.index')
                ->with('success', 'Réservation #' . $id . ' marquée comme terminée.');
        }

        return redirect()->route('rentals.index')
            ->with('error', 'Erreur lors de la mise à jour de la réservation.');
    }
}
