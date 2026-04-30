@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Gestion des utilisateurs</h5>
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
                                    <th>ID</th>
                                    <th>Prénom</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Store</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $index => $customer)
                                    @php
                                        $customerId = $customer['customerId'] ?? $customer['customer_id'] ?? null;
                                        $firstName  = $customer['firstName']  ?? $customer['first_name']  ?? '—';
                                        $lastName   = $customer['lastName']   ?? $customer['last_name']   ?? '—';
                                        $email      = $customer['email']      ?? '—';
                                        $storeId    = $customer['storeId']    ?? $customer['store_id']    ?? '—';
                                        $active     = $customer['active']     ?? 0;
                                    @endphp
                                    <tr>
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td><strong>{{ $customerId }}</strong></td>
                                        <td>{{ $firstName }}</td>
                                        <td>{{ $lastName }}</td>
                                        <td>{{ $email }}</td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                <i class="bi bi-shop"></i> Store {{ $storeId }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-secondary">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('customers.edit', $customerId) }}" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i> Modifier
                                                </a>
                                                <form action="{{ route('customers.destroy', $customerId) }}" method="POST"
                                                      onsubmit="return confirm('Supprimer ce client ? Cette action est irréversible.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i> Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">Aucun client trouvé</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="text-muted small mt-2">
                        {{ count($customers) }} client(s) au total
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
