<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToadFilmService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.toad.url', 'http://localhost:8180'), '/');
    }

    public function getAllFilms(): ?array
    {
        $url = $this->baseUrl . '/films';

        try {
            $headers = ['Accept' => 'application/json'];
            
            // Récupère le token JWT depuis la session
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            Log::info('Appel API Films', ['url' => $url, 'has_token' => !empty($token)]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                // Si l'API retourne un objet paginé Spring, extraire le tableau "content"
                return $data['content'] ?? $data;
            }

            Log::warning('Films API KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Films', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    public function getFilmById(int $id): ?array
    {
        $url = $this->baseUrl . '/films/' . $id;

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Film', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    public function createFilm(array $data): bool
    {
        $url = $this->baseUrl . '/films';

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            // Préparer les données pour l'API avec TOUS les champs nécessaires
            $apiData = [
                'title' => $data['title'], // Obligatoire
                'description' => $data['description'] ?? null,
                'releaseYear' => !empty($data['releaseYear']) ? (int) $data['releaseYear'] : null,
                'languageId' => 1, // Valeur par défaut (Anglais)
                'originalLanguageId' => null, // Langue originale (optionnel)
                'rentalDuration' => 3, // Durée de location par défaut (3 jours)
                'rentalRate' => 4.99, // Prix de location par défaut
                'length' => !empty($data['length']) ? (int) $data['length'] : null,
                'replacementCost' => 19.99, // Coût de remplacement par défaut
                'rating' => $data['rating'] ?? 'G', // Note par défaut
                'specialFeatures' => $data['specialFeatures'] ?? null,
            ];

            // Retirer uniquement les valeurs null (mais garder les autres valeurs par défaut)
            $apiData = array_filter($apiData, fn($value) => $value !== null);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post($url, $apiData);

            if ($response->successful()) {
                return true;
            }

            Log::warning('Échec création film', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Erreur création film', ['msg' => $e->getMessage()]);
            return false;
        }
    }

    public function updateFilm(int $id, array $data): bool
    {
        $url = $this->baseUrl . '/films/' . $id;

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            Log::info('Données reçues du formulaire', ['data' => $data]);

            // Préparer les données pour l'API (l'API Toad attend du camelCase)
            $apiData = [
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'releaseYear' => !empty($data['releaseYear']) ? (int) $data['releaseYear'] : null,
                'languageId' => 1, // Valeur par défaut (Anglais généralement)
                'length' => !empty($data['length']) ? (int) $data['length'] : null,
                'rating' => $data['rating'] ?? null,
                'specialFeatures' => $data['specialFeatures'] ?? null,
            ];

            // Retirer les valeurs null et vides (mais garder languageId)
            $apiData = array_filter($apiData, fn($value, $key) =>
                $key === 'languageId' || ($value !== null && $value !== ''),
                ARRAY_FILTER_USE_BOTH
            );

            Log::info('Mise à jour film', ['id' => $id, 'data' => $apiData]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->put($url, $apiData);

            if ($response->successful()) {
                Log::info('Film mis à jour avec succès', ['id' => $id]);
                return true;
            }

            Log::warning('Échec mise à jour film', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Erreur mise à jour film', ['msg' => $e->getMessage()]);
            return false;
        }
    }

    public function deleteFilm(int $id): bool
    {
        $url = $this->baseUrl . '/films/' . $id;

        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            Log::info('Suppression film', ['id' => $id]);

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->delete($url);

            if ($response->successful()) {
                Log::info('Film supprimé avec succès', ['id' => $id]);
                return true;
            }

            Log::warning('Échec suppression film', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Erreur suppression film', ['msg' => $e->getMessage()]);
            return false;
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