<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToadRentalService
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

    public function getAllRentals(): ?array
    {
        $url = $this->baseUrl . '/rentals';
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
            Log::warning('Rentals API KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Rentals', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    public function getRentalCount(): ?int
    {
        $url = $this->baseUrl . '/rentals';
        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }
            $response = Http::withHeaders($headers)->timeout(10)->get($url);
            if ($response->successful()) {
                $data = $response->json();
                return $data['totalElements'] ?? count($data['content'] ?? $data);
            }
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur getRentalCount', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    public function updateRental(int $id, array $data): bool
    {
        $url = $this->baseUrl . '/rentals/' . $id;
        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }
            $response = Http::withHeaders($headers)->timeout(10)->put($url, $data);
            if ($response->successful()) {
                return true;
            }
            Log::warning('Échec mise à jour rental', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Erreur mise à jour rental', ['msg' => $e->getMessage()]);
            return false;
        }
    }

    private function getUserToken(): ?string
    {
        $userData = session('toad_user');
        if (!empty($userData['token'])) {
            return $userData['token'];
        }
        $serverToken = session('toad_server_token');
        if (!empty($serverToken)) {
            return $serverToken;
        }
        $configToken = config('services.toad.token');
        if (!empty($configToken)) {
            return $configToken;
        }
        return null;
    }
}
