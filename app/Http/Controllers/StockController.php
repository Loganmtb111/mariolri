<?php

namespace App\Http\Controllers;

use App\Services\ToadStockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    private ToadStockService $stockService;

    public function __construct(ToadStockService $stockService)
    {
        $this->middleware('auth');
        $this->stockService = $stockService;
    }

    /**
     * Affiche la page de gestion des stocks avec les films et leur disponibilité
     */
    public function index()
    {
        $films = $this->stockService->getFilmsWithAvailability();

        // Convertir les tableaux associatifs en objets et mapper les clés camelCase vers snake_case
        if ($films) {
            $films = array_map(function($film) {
                return (object) [
                    'film_id' => $film['filmId'] ?? null,
                    'title' => $film['title'] ?? '',
                    'category' => $film['category'] ?? 'Non catégorisé',
                    'total_inventories' => $film['totalInventories'] ?? 0,
                    'available_count' => $film['availableCount'] ?? 0,
                    'status' => $film['status'] ?? 'unavailable',
                    'status_label' => $film['statusLabel'] ?? 'Non disponible',
                    'status_reason' => $film['statusReason'] ?? null,
                ];
            }, $films);
        }

        return view('stocks.index', [
            'films' => $films ?? []
        ]);
    }

    /**
     * Récupère les films disponibles dans un store spécifique
     */
    public function getStoreInventory($storeId)
    {
        $inventory = $this->stockService->getStoreInventory($storeId);

        if ($inventory !== null) {
            // Mapper les clés camelCase vers snake_case pour le JavaScript
            $mappedInventory = array_map(function($item) {
                return [
                    'inventory_id' => $item['inventoryId'] ?? null,
                    'film_id' => $item['filmId'] ?? null,
                    'store_id' => $item['storeId'] ?? null,
                    'title' => $item['title'] ?? '',
                    'description' => $item['description'] ?? '',
                    'release_year' => $item['releaseYear'] ?? null,
                    'length' => $item['length'] ?? null,
                    'category' => $item['category'] ?? null,
                    'is_rented' => $item['isRented'] ?? 0,
                ];
            }, $inventory);

            return response()->json([
                'success' => true,
                'data' => $mappedInventory
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la récupération des films'
        ], 500);
    }

    /**
     * Transfère tous les films disponibles d'un store vers un autre
     */
    public function transferInventory(Request $request)
    {
        $request->validate([
            'origin_store_id' => 'required|integer',
            'destination_store_id' => 'required|integer|different:origin_store_id'
        ]);

        $originStoreId = $request->origin_store_id;
        $destinationStoreId = $request->destination_store_id;

        $result = $this->stockService->transferInventory($originStoreId, $destinationStoreId);

        if ($result !== null) {
            return response()->json($result);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors du transfert'
        ], 500);
    }
}
