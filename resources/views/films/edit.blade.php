@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier le film</h5>
                    <a href="{{ route('films.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Retour à la liste
                    </a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i> Veuillez corriger les erreurs suivantes :
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('films.update', $film['filmId'] ?? $film['id']) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="rentalDuration" value="{{ $film['rentalDuration'] ?? 3 }}">
                        <input type="hidden" name="rentalRate" value="{{ $film['rentalRate'] ?? 4.99 }}">
                        <input type="hidden" name="replacementCost" value="{{ $film['replacementCost'] ?? 19.99 }}">

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="title" class="form-label fw-bold">Titre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       id="title" name="title"
                                       value="{{ old('title', $film['title'] ?? '') }}"
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="rating" class="form-label fw-bold">Note</label>
                                <select class="form-select @error('rating') is-invalid @enderror"
                                        id="rating" name="rating">
                                    @foreach(['G', 'PG', 'PG-13', 'R', 'NC-17'] as $option)
                                        <option value="{{ $option }}"
                                            {{ old('rating', $film['rating'] ?? '') === $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description"
                                      rows="4">{{ old('description', $film['description'] ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="releaseYear" class="form-label fw-bold">Année de sortie</label>
                                <input type="text" class="form-control @error('releaseYear') is-invalid @enderror"
                                       id="releaseYear" name="releaseYear"
                                       value="{{ old('releaseYear', $film['releaseYear'] ?? '') }}"
                                       placeholder="Ex: 2006">
                                @error('releaseYear')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="length" class="form-label fw-bold">Durée (minutes)</label>
                                <input type="number" class="form-control @error('length') is-invalid @enderror"
                                       id="length" name="length"
                                       value="{{ old('length', $film['length'] ?? '') }}"
                                       min="1">
                                @error('length')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i>
                                ID du film : <strong>{{ $film['filmId'] ?? $film['id'] ?? 'N/A' }}</strong>
                            </small>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Enregistrer les modifications
                            </button>
                            <a href="{{ route('films.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
