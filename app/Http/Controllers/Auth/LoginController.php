<?php

namespace App\Http\Controllers\Auth;

use App\Auth\ToadUser;
use App\Http\Controllers\Controller;
use App\Services\ToadAuthService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/films';

    private ToadAuthService $authService;

    public function __construct(ToadAuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'email';
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'server' => 'required|in:local,distant',
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        $email    = $request->input($this->username());
        $password = $request->input('password');
        $server   = $request->input('server', 'local');

        // Résoudre l'URL du serveur choisi
        $serverConfig = $server === 'distant'
            ? config('services.toad_distant')
            : config('services.toad');

        $serverUrl   = rtrim((string) $serverConfig['url'], '/');
        $serverToken = $serverConfig['token'];

        // 1. Vérifier les credentials via l'API TOAD et récupérer les données du staff
        $staffData = $this->authService->verify($email, $password, $serverUrl);
        if (!$staffData) {
            return false;
        }

        // 2. Obtenir le JWT via /api/auth/login
        $token = $this->authService->login($email, $password, $serverUrl);

        // 3. Stocker en session (utilisé par ToadFilmService, ToadStockService, ToadUserProvider)
        $firstName = $staffData['firstName'] ?? $staffData['first_name'] ?? '';
        $lastName  = $staffData['lastName']  ?? $staffData['last_name']  ?? '';

        session([
            'toad_user' => [
                'id'         => $staffData['staffId'] ?? $staffData['staff_id'] ?? $email,
                'email'      => $staffData['email'] ?? $email,
                'token'      => $token,
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'name'       => trim("$firstName $lastName") ?: $email,
            ],
            'toad_server_url'   => $serverUrl,
            'toad_server_token' => $serverToken,
            'toad_server'       => $server,
        ]);

        // 4. Connecter via ToadUserProvider (sans BDD)
        $this->guard()->login(new ToadUser(session('toad_user')), $request->filled('remember'));
        return true;
    }

    protected function guard()
    {
        return Auth::guard();
    }
}
