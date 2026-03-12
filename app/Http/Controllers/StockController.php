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

    public function index()
    {
        $inventories = $this->stockService->getAllInventories();

        return view('stocks.index', [
            'inventories' => $inventories ?? []
        ]);
    }

    public function create()
    {
        return view('stocks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'filmId'  => 'required|integer|min:1',
            'storeId' => 'required|in:1,2',
        ]);

        $success = $this->stockService->createInventory($validated);

        if ($success) {
            return redirect()->route('stocks.index')
                ->with('success', 'L\'exemplaire a été créé avec succès.');
        }

        return back()
            ->with('error', 'Une erreur est survenue lors de la création de l\'exemplaire.')
            ->withInput();
    }

    public function edit($id)
    {
        $inventory = $this->stockService->getInventoryById($id);

        if (!$inventory) {
            abort(404, 'Inventaire non trouvé');
        }

        return view('stocks.edit', ['inventory' => $inventory]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'filmId'  => 'required|integer|min:1',
            'storeId' => 'required|in:1,2',
        ]);

        $success = $this->stockService->updateInventory($id, $validated);

        if ($success) {
            return redirect()->route('stocks.index')
                ->with('success', 'L\'exemplaire a été modifié avec succès.');
        }

        return back()
            ->with('error', 'Une erreur est survenue lors de la modification de l\'exemplaire.')
            ->withInput();
    }

    public function destroy($id)
    {
        $success = $this->stockService->deleteInventory($id);

        if ($success) {
            return redirect()->route('stocks.index')
                ->with('success', 'L\'exemplaire a été supprimé avec succès.');
        }

        return redirect()->route('stocks.index')
            ->with('error', 'Une erreur est survenue lors de la suppression de l\'exemplaire.');
    }

    /**
     * Récupère les films disponibles dans un store spécifique (API interne)
     */
    public function getStoreInventory($storeId)
    {
        $inventory = $this->stockService->getStoreInventory($storeId);

        if ($inventory !== null) {
            $mappedInventory = array_map(function ($item) {
                return [
                    'inventory_id' => $item['inventoryId'] ?? null,
                    'film_id'      => $item['filmId'] ?? null,
                    'store_id'     => $item['storeId'] ?? null,
                    'title'        => $item['title'] ?? '',
                    'is_rented'    => $item['isRented'] ?? 0,
                ];
            }, $inventory);

            return response()->json(['success' => true, 'data' => $mappedInventory]);
        }

        return response()->json(['success' => false, 'message' => 'Erreur lors de la récupération'], 500);
    }

    /**
     * Transfère tous les films disponibles d'un store vers un autre
     */
    public function transferInventory(Request $request)
    {
        $request->validate([
            'origin_store_id'      => 'required|integer',
            'destination_store_id' => 'required|integer|different:origin_store_id',
        ]);

        $result = $this->stockService->transferInventory(
            $request->origin_store_id,
            $request->destination_store_id
        );

        if ($result !== null) {
            return response()->json($result);
        }

        return response()->json(['success' => false, 'message' => 'Erreur lors du transfert'], 500);
    }
}
