@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    @php
                        $customerId = $customer['customerId'] ?? $customer['customer_id'] ?? null;
                        $firstName  = $customer['firstName']  ?? $customer['first_name']  ?? '';
                        $lastName   = $customer['lastName']   ?? $customer['last_name']   ?? '';
                        $email      = $customer['email']      ?? '';
                        $storeId    = $customer['storeId']    ?? $customer['store_id']    ?? '';
                        $active     = $customer['active']     ?? 0;
                    @endphp
                    <h5 class="mb-0">Modifier le client #{{ $customerId }}</h5>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">
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

                    <form action="{{ route('customers.update', $customerId) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label fw-bold">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('firstName') is-invalid @enderror"
                                       id="firstName" name="firstName"
                                       value="{{ old('firstName', $firstName) }}" required>
                                @error('firstName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('lastName') is-invalid @enderror"
                                       id="lastName" name="lastName"
                                       value="{{ old('lastName', $lastName) }}" required>
                                @error('lastName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email"
                                   value="{{ old('email', $email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
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
                            <div class="col-md-6 mb-3">
                                <label for="active" class="form-label fw-bold">Statut <span class="text-danger">*</span></label>
                                <select class="form-select @error('active') is-invalid @enderror"
                                        id="active" name="active" required>
                                    <option value="1" {{ old('active', $active) == '1' ? 'selected' : '' }}>Actif</option>
                                    <option value="0" {{ old('active', $active) == '0' ? 'selected' : '' }}>Inactif</option>
                                </select>
                                @error('active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-muted small mb-3">
                            <i class="bi bi-info-circle"></i> ID client : <strong>{{ $customerId }}</strong>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Enregistrer les modifications
                            </button>
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary">
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
