@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">

            @if (session('status'))
                <div class="alert alert-success mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card text-center shadow-sm">
                        <div class="card-body py-4">
                            <div class="display-4 fw-bold text-primary">
                                {{ $filmCount ?? '—' }}
                            </div>
                            <div class="mt-2 text-muted fs-5">Films disponibles</div>
                            <a href="{{ route('films.index') }}" class="btn btn-primary mt-3">Voir les films</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card text-center shadow-sm">
                        <div class="card-body py-4">
                            <div class="display-4 fw-bold text-warning">
                                {{ $rentalCount ?? '—' }}
                            </div>
                            <div class="mt-2 text-muted fs-5">Réservations</div>
                            <a href="{{ route('rentals.index') }}" class="btn btn-warning mt-3">Voir les réservations</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-md-6">
                    <div class="card text-center shadow-sm">
                        <div class="card-body py-4">
                            <div class="display-4 fw-bold text-success">
                                {{ $customerCount ?? '—' }}
                            </div>
                            <div class="mt-2 text-muted fs-5">Clients</div>
                            <a href="{{ route('customers.index') }}" class="btn btn-success mt-3">Voir les clients</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
