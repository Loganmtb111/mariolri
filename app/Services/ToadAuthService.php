<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToadAuthService
{
    private ?string $token;
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.toad.url', 'http://localhost:8180'), '/');
        $this->token = config('services.toad.token');
    }

    public function login(string $email, string $password, ?string $baseUrl = null): ?string
    {
        $url = rtrim($baseUrl ?? $this->baseUrl, '/') . '/api/auth/login';

        try {
            $response = Http::acceptJson()
                ->timeout(5)
                ->post($url, ['email' => $email, 'password' => $password]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['token'] ?? $data['accessToken'] ?? null;
            }

            Log::warning('Login API KO', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur login API', ['msg' => $e->getMessage()]);
            return null;
        }
    }

    public function verify(string $email, string $password, ?string $baseUrl = null): ?array
    {
        $url = rtrim($baseUrl ?? $this->baseUrl, '/') . '/staffs/verify';
        $body = [
            'email' => $email,
            'password' => $password
        ];

        try {
            Log::info('Appel Toad /verify', [
                'url' => $url,
                'with_token' => !empty($this->token),
                'token' => $this->token,
                'body' => $body
            ]);

            $request = Http::acceptJson()
                ->timeout(5);

            // Ajoute le token Bearer si configuré
            if (!empty($this->token)) {
                $request = $request->withToken($this->token, 'Bearer'); 
            }

            $response = $request->post($url, $body);

            $status = $response->status();
            $responseBody = $response->json();

            Log::info('Réponse /verify', [
                'status' => $status,
                'body' => $responseBody
            ]);

            if ($response->successful()) {
                return $responseBody;
            }

            Log::warning('Verify KO', [
                'status' => $status,
                'body' => $responseBody
            ]);
            return null;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Erreur de connexion API Toad', [
                'msg' => $e->getMessage()
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Erreur API Toad', [
                'msg' => $e->getMessage()
            ]);
            return null;
        }
    }
}