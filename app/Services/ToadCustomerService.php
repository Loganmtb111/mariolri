<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToadCustomerService
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

    public function getAllCustomers(): ?array
    {
        $url = $this->baseUrl . '/customers';
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
            Log::warning('Customers API KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Customers', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    public function getCustomerCount(): ?int
    {
        $url = $this->baseUrl . '/customers';
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
            Log::error('Erreur getCustomerCount', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    public function getCustomerById(int $id): ?array
    {
        $url = $this->baseUrl . '/customers/' . $id;
        try {
            $headers = ['Accept' => 'application/json'];
            $token = $this->getUserToken();
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }
            $response = Http::withHeaders($headers)->timeout(10)->get($url);
            return $response->successful() ? $response->json() : null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Customer', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    public function updateCustomer(int $id, array $data): bool
    {
        $url = $this->baseUrl . '/customers/' . $id;
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
            Log::warning('Échec modification customer', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Erreur modification customer', ['msg' => $e->getMessage()]);
            return false;
        }
    }

    public function deleteCustomer(int $id): bool
    {
        $url = $this->baseUrl . '/customers/' . $id;
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
            Log::warning('Échec suppression customer', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Erreur suppression customer', ['msg' => $e->getMessage()]);
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
