
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestion du catalogue de films</h5>
                    <a href="{{ route('films.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Ajouter un film
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (empty($films))
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Aucun film disponible ou erreur lors de la récupération des données de l'API.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Titre</th>
                                        <th>Description</th>
                                        <th>Année</th>
                                        <th>Durée</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($films as $film)
                                        <tr>
                                            <td>{{ $film['filmId'] ?? $film['id'] ?? 'N/A' }}</td>
                                            <td><strong>{{ $film['title'] ?? 'Sans titre' }}</strong></td>
                                            <td>{{ Str::limit($film['description'] ?? 'Aucune description', 80) }}</td>
                                            <td>{{ $film['releaseYear'] ?? 'N/A' }}</td>
                                            <td>{{ $film['length'] ?? 'N/A' }} min</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('films.show', $film['filmId'] ?? $film['id']) }}"
                                                       class="btn btn-sm btn-info" title="Voir">
                                                        <i class="bi bi-eye"></i> Voir
                                                    </a>
                                                    <a href="{{ route('films.edit', $film['filmId'] ?? $film['id']) }}"
                                                       class="btn btn-sm btn-warning" title="Modifier">
                                                        <i class="bi bi-pencil"></i> Modifier
                                                    </a>
                                                    <form action="{{ route('films.destroy', $film['filmId'] ?? $film['id']) }}"
                                                          method="POST"
                                                          style="display: inline;"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce film ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                            <i class="bi bi-trash"></i> Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection