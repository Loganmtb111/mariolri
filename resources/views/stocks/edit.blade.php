@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier l'exemplaire #{{ $inventory['inventoryId'] ?? $inventory['inventory_id'] }}</h5>
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

                    @php
                        $invId   = $inventory['inventoryId'] ?? $inventory['inventory_id'] ?? null;
                        $filmId  = $inventory['filmId']      ?? $inventory['film_id']      ?? '';
                        $storeId = $inventory['storeId']     ?? $inventory['store_id']     ?? '';
                    @endphp

                    <form action="{{ route('stocks.update', $invId) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="filmId" class="form-label fw-bold">ID du film <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('filmId') is-invalid @enderror"
                                   id="filmId" name="filmId"
                                   value="{{ old('filmId', $filmId) }}"
                                   min="1" required>
                            @error('filmId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="storeId" class="form-label fw-bold">Store <span class="text-danger">*</span></label>
                            <select class="form-select @error('storeId') is-invalid @enderror"
                                    id="storeId" name="storeId" required>
                                <option value="1" {{ old('storeId', $storeId) == '1' ? 'selected' : '' }}>Store 1</option>
                                <option value="2" {{ old('storeId', $storeId) == '2' ? 'selected' : '' }}>Store 2</option>
                            </select>
                            @error('storeId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> ID inventaire : <strong>{{ $invId }}</strong>
                            </small>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Enregistrer
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
