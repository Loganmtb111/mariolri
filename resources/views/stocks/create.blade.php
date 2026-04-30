@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter un exemplaire</h5>
                    <a href="{{ route('stocks.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Retour
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
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('stocks.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="filmId" class="form-label fw-bold">ID du film <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('filmId') is-invalid @enderror"
                                   id="filmId" name="filmId"
                                   value="{{ old('filmId') }}"
                                   min="1" required placeholder="Ex: 2">
                            @error('filmId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="filmPreview" class="form-text mt-1"></div>
                        </div>

                        @php
                            $filmMap = [];
                            foreach ($films as $f) {
                                $fid = $f['filmId'] ?? $f['film_id'] ?? null;
                                if ($fid !== null) $filmMap[$fid] = $f['title'] ?? '—';
                            }
                        @endphp
                        <script>
                            const filmMap = @json($filmMap);
                            document.getElementById('filmId').addEventListener('input', function () {
                                const preview = document.getElementById('filmPreview');
                                const title = filmMap[this.value];
                                if (title) {
                                    preview.innerHTML = '🎬 Vous allez ajouter le film : <strong>' + title + '</strong>';
                                    preview.className = 'form-text text-success mt-1';
                                } else if (this.value) {
                                    preview.innerHTML = 'Aucun film trouvé pour cet ID.';
                                    preview.className = 'form-text text-danger mt-1';
                                } else {
                                    preview.innerHTML = '';
                                }
                            });
                        </script>

                        <div class="mb-3">
                            <label for="storeId" class="form-label fw-bold">Store <span class="text-danger">*</span></label>
                            <select class="form-select @error('storeId') is-invalid @enderror"
                                    id="storeId" name="storeId" required>
                                <option value="">-- Sélectionner un store --</option>
                                <option value="1" {{ old('storeId') == '1' ? 'selected' : '' }}>Store 1</option>
                                <option value="2" {{ old('storeId') == '2' ? 'selected' : '' }}>Store 2</option>
                            </select>
                            @error('storeId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Ajouter
                            </button>
                            <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
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
