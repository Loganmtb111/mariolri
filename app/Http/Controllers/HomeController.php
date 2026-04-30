<?php

namespace App\Http\Controllers;

use App\Services\ToadFilmService;
use App\Services\ToadCustomerService;
use App\Services\ToadRentalService;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $filmCount     = app(ToadFilmService::class)->getFilmCount();
        $customerCount = app(ToadCustomerService::class)->getCustomerCount();
        $rentalCount   = app(ToadRentalService::class)->getRentalCount();

        return view('home', compact('filmCount', 'customerCount', 'rentalCount'));
    }
}
