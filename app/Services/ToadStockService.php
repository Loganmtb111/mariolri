<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToadStockService
{
    private string $baseUrl;

    public function __construct()
    {
        $sessionUrl = session('toad_server_url');
        $this->baseUrl = rtrim(
            $sessionUrl ?: (string) config('services.toad.url', 'http://localhost:8180'),
            '/'
        );
    }

    public function getAllInventories(): ?array
    {
        $url = $this->baseUrl . '/inventories';
        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }
            $response = Http::withHeaders($headers)->timeout(10)->get($url);
            if ($response->successful()) {
                $data = $response->json();
                return $data['content'] ?? $data;
            }
            Log::warning('Inventories API KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Inventories', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    public function getInventoryById(int $id): ?array
    {
        $url = $this->baseUrl . '/inventories/' . $id;
        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }
            $response = Http::withHeaders($headers)->timeout(10)->get($url);
            return $response->successful() ? $response->json() : null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Inventory', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    public function createInventory(array $data): bool
    {
        $url = $this->baseUrl . '/inventories';
        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }
            $apiData = [
                'filmId'  => (int) $data['filmId'],
                'storeId' => (int) $data['storeId'],
            ];
            $response = Http::withHeaders($headers)->timeout(10)->post($url, $apiData);
            if ($response->successful()) {
                return true;
            }
            Log::warning('Échec création inventaire', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Erreur création inventaire', ['msg' => $e->getMessage()]);
            return false;
        }
    }

    public function updateInventory(int $id, array $data): bool
    {
        $url = $this->baseUrl . '/inventories/' . $id;
        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }
            $apiData = [
                'filmId'  => (int) $data['filmId'],
                'storeId' => (int) $data['storeId'],
            ];
            $response = Http::withHeaders($headers)->timeout(10)->put($url, $apiData);
            if ($response->successful()) {
                return true;
            }
            Log::warning('Échec modification inventaire', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Erreur modification inventaire', ['msg' => $e->getMessage()]);
            return false;
        }
    }

    public function deleteInventory(int $id): bool
    {
        $url = $this->baseUrl . '/inventories/' . $id;
        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }
            $response = Http::withHeaders($headers)->timeout(10)->delete($url);
            if ($response->successful()) {
                return true;
            }
            Log::warning('Échec suppression inventaire', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Erreur suppression inventaire', ['msg' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Récupère tous les films avec leurs statistiques de disponibilité
     *
     * @return array|null
     */
    public function getFilmsWithAvailability(): ?array
    {
        $url = $this->baseUrl . '/films/availability';

        try {
            $headers = ['Accept' => 'application/json'];

            // Récupère le token JWT depuis la session
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            Log::info('Appel API Films Availability', ['url' => $url, 'has_token' => !empty($token)]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Films Availability API KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Films Availability', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Récupère l'inventaire d'un store spécifique
     *
     * @param int $storeId
     * @return array|null
     */
    public function getStoreInventory(int $storeId): ?array
    {
        $url = $this->baseUrl . '/stores/' . $storeId . '/inventory';

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            Log::info('Appel API Store Inventory', ['url' => $url, 'storeId' => $storeId]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Store Inventory API KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Store Inventory', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Transfère l'inventaire disponible d'un store vers un autre
     *
     * @param int $originStoreId
     * @param int $destinationStoreId
     * @return array|null
     */
    public function transferInventory(int $originStoreId, int $destinationStoreId): ?array
    {
        $url = $this->baseUrl . '/stores/transfer';

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            $data = [
                'originStoreId' => $originStoreId,
                'destinationStoreId' => $destinationStoreId
            ];

            Log::info('Appel API Store Transfer', ['url' => $url, 'data' => $data]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post($url, $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Store Transfer API KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Store Transfer', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Récupère le token pour l'API
     * Essaie d'abord de récupérer le token de session, sinon utilise le token configuré
     */
    private function getUserToken(): ?string
    {
        // Essayer d'abord le token de session (authentification via API Toad)
        $userData = session('toad_user');
        if (!empty($userData['token'])) {
            Log::info('Utilisation token de session');
            return $userData['token'];
        }

        // Sinon, utiliser le token configuré dans .env
        $configToken = config('services.toad.token');
        if (!empty($configToken)) {
            Log::info('Utilisation token configuré dans .env');
            return $configToken;
        }

        Log::warning('Aucun token disponible pour l\'API');
        return null;
    }
}
