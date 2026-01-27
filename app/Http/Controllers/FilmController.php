<?php

namespace App\Http\Controllers;

use App\Services\ToadFilmService;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    private ToadFilmService $filmService;

    public function __construct(ToadFilmService $filmService)
    {
        $this->middleware('auth');
        $this->filmService = $filmService;
    }

    public function index()
    {
        $films = $this->filmService->getAllFilms();

        return view('films.index', [
            'films' => $films ?? []
        ]);
    }

    public function show($id)
    {
        $film = $this->filmService->getFilmById($id);

        if (!$film) {
            abort(404, 'Film non trouvé');
        }

        return view('films.show', [
            'film' => $film
        ]);
    }

    public function edit($id)
    {
        $film = $this->filmService->getFilmById($id);

        if (!$film) {
            abort(404, 'Film non trouvé');
        }

        return view('films.edit', [
            'film' => $film
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'releaseYear' => 'nullable|integer|min:1800|max:' . (date('Y') + 5),
            'length' => 'nullable|integer|min:1',
            'rating' => 'nullable|string',
            'specialFeatures' => 'nullable|string',
        ]);

        $success = $this->filmService->updateFilm($id, $validated);

        if ($success) {
            return redirect()->route('films.index')
                ->with('success', 'Le film a été modifié avec succès.');
        }

        return back()
            ->with('error', 'Une erreur est survenue lors de la modification du film.')
            ->withInput();
    }

    public function create()
    {
        return view('films.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'releaseYear' => 'nullable|integer|min:1800|max:' . (date('Y') + 5),
            'length' => 'nullable|integer|min:1',
            'rating' => 'nullable|string',
            'specialFeatures' => 'nullable|string',
        ]);

        $success = $this->filmService->createFilm($validated);

        if ($success) {
            return redirect()->route('films.index')
                ->with('success', 'Le film a été créé avec succès.');
        }

        return back()
            ->with('error', 'Une erreur est survenue lors de la création du film.')
            ->withInput();
    }
}