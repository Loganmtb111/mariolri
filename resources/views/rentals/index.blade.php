@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            {{-- Tableau films les plus loués --}}
            @if(count($filmsRentalCount) > 0)
            <div class="card mb-4" style="max-width: 600px;">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Top 10 des meilleurs locations</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>ID Film</th>
                                    <th>Titre</th>
                                    <th>Nombre de locations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($filmsRentalCount as $index => $item)
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td>{{ $item[0] }}</td>
                                    <td><strong>{{ $item[1] }}</strong></td>
                                    <td>
                                        <span class="badge bg-primary">{{ $item[2] }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Gestion des réservations</h5>
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

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>ID Réservation</th>
                                    <th>ID Inventaire</th>
                                    <th>ID Client</th>
                                    <th>Date de location</th>
                                    <th>Date de retour</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rentals as $index => $rental)
                                    @php
                                        $rentalId   = $rental['rentalId']    ?? $rental['rental_id']    ?? null;
                                        $invId      = $rental['inventoryId'] ?? $rental['inventory_id'] ?? 0;
                                        $customerId = $rental['customerId']  ?? $rental['customer_id']  ?? 0;
                                        $staffId    = $rental['staffId']     ?? $rental['staff_id']     ?? 1;
                                        $statusId   = $rental['statusId']    ?? $rental['status_id']    ?? null;
                                        $rentalDate = $rental['rentalDate']  ?? $rental['rental_date']  ?? '—';
                                        $returnDate = $rental['returnDate']  ?? $rental['return_date']  ?? null;
                                    @endphp
                                    <tr>
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td><strong>{{ $rentalId }}</strong></td>
                                        <td>{{ $invId }}</td>
                                        <td>{{ $customerId }}</td>
                                        <td class="small">{{ $rentalDate }}</td>
                                        <td class="small">{{ $returnDate ?? '—' }}</td>
                                        <td>
                                            @if($statusId == 3)
                                                <span class="badge bg-warning text-dark">En cours</span>
                                            @elseif($statusId == 2)
                                                <span class="badge bg-primary">Dans le panier</span>
                                            @else
                                                <span class="badge bg-success">Terminé</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($statusId == 3)
                                                <form action="{{ route('rentals.update', $rentalId) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="inventoryId" value="{{ $invId }}">
                                                    <input type="hidden" name="customerId"  value="{{ $customerId }}">
                                                    <input type="hidden" name="staffId"     value="{{ $staffId }}">
                                                    <input type="hidden" name="rentalDate"  value="{{ $rentalDate }}">
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle"></i> Terminé
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">Aucune réservation trouvée</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="text-muted small mt-2">
                        {{ count($rentals) }} réservation(s) au total
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
