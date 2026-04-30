<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToadStaffService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.toad.url', 'http://localhost:8180'), '/');
    }

    /**
     * Retourne le tableau de la réponse si succès, ou ['_error' => true, 'status' => ..., 'message' => ...] si échec.
     */
    public function createStaff(array $data): array
    {
        $url = $this->baseUrl . '/staffs';

        $payload = [
            'firstName'  => $data['first_name'],
            'lastName'   => $data['last_name'],
            'addressId'  => $data['address_id'] ?? 1,
            'email'      => $data['email'],
            'storeId'    => $data['store_id'] ?? 1,
            'active'     => true,
            'username'   => $data['username'],
            'password'   => $data['password'],
            'lastUpdate' => now()->toIso8601String(),
        ];

        try {
            Log::info('Appel API createStaff', ['url' => $url, 'payload' => $payload]);

            $response = Http::post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Erreur createStaff', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                '_error'  => true,
                'status'  => $response->status(),
                'message' => $response->body(),
            ];

        } catch (\Exception $e) {
            Log::error('Exception createStaff', ['message' => $e->getMessage()]);
            return ['_error' => true, 'status' => 0, 'message' => $e->getMessage()];
        }
    }

    public function getStaffCount(): ?int
    {
        $url = $this->baseUrl . '/staffs';

        try {
            $token = session('toad_server_token') ?? session('toad_user.token') ?? config('services.toad.token');
            $request = Http::acceptJson()->timeout(5);
            if ($token) {
                $request = $request->withToken($token);
            }

            $response = $request->get($url);

            if ($response->successful()) {
                $data = $response->json();
                return $data['totalElements'] ?? count($data['content'] ?? $data);
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur getStaffCount', ['msg' => $e->getMessage()]);
            return null;
        }
    }
}
