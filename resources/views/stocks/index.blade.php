@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestion de l'inventaire</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('stocks.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Ajouter un exemplaire
                        </a>
                    </div>
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
                                    <th>ID Inventaire</th>
                                    <th>ID Film</th>
                                    <th>Nom du film</th>
                                    <th>Store</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventories as $index => $inv)
                                    @php
                                        $invId   = $inv['inventoryId'] ?? $inv['inventory_id'] ?? null;
                                        $filmId  = $inv['film']['filmId'] ?? $inv['filmId'] ?? $inv['film_id'] ?? '—';
                                        $title   = $inv['film']['title'] ?? '—';
                                        $storeId = $inv['storeId'] ?? $inv['store_id'] ?? '—';
                                    @endphp
                                    <tr>
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td><strong>{{ $invId }}</strong></td>
                                        <td>{{ $filmId }}</td>
                                        <td>{{ $title }}</td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                <i class="bi bi-shop"></i> Store {{ $storeId }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('stocks.edit', $invId) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="bi bi-pencil"></i> Modifier
                                                </a>
                                                <form action="{{ route('stocks.destroy', $invId) }}" method="POST"
                                                      onsubmit="return confirm('Supprimer cet exemplaire ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                        <i class="bi bi-trash"></i> Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">Aucun exemplaire dans l'inventaire</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="text-muted small mt-2">
                        {{ count($inventories) }} exemplaire(s) au total
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal transfert de stock -->
<div class="modal fade" id="changeStoreModal" tabindex="-1" aria-labelledby="changeStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStoreModalLabel">Transférer le stock entre stores</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="transferMessage" class="alert" style="display: none;" role="alert"></div>

                <div class="mb-3">
                    <label for="storeOrigin" class="form-label">Store d'origine</label>
                    <select class="form-select" id="storeOrigin">
                        <option value="">Sélectionner un store</option>
                        <option value="1">Store 1</option>
                        <option value="2">Store 2</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="storeDestination" class="form-label">Store de destination</label>
                    <select class="form-select" id="storeDestination">
                        <option value="">Sélectionner un store</option>
                        <option value="1">Store 1</option>
                        <option value="2">Store 2</option>
                    </select>
                </div>

                <div id="filmListContainer" style="display: none;">
                    <h6 class="mb-2">DVDs dans le store d'origine</h6>
                    <div id="filmList" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.25rem; padding: 0.5rem;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <div id="errorMessage" class="text-danger small me-auto" style="display: none;">
                    Les stores d'origine et de destination doivent être différents
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" id="confirmStoreChange" disabled>
                    <i class="bi bi-check-circle"></i> Transférer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const storeOrigin = document.getElementById('storeOrigin');
    const storeDestination = document.getElementById('storeDestination');
    const confirmBtn = document.getElementById('confirmStoreChange');
    const errorMessage = document.getElementById('errorMessage');
    const filmListContainer = document.getElementById('filmListContainer');
    const filmList = document.getElementById('filmList');
    const transferMessage = document.getElementById('transferMessage');

    function validateStores() {
        const o = storeOrigin.value, d = storeDestination.value;
        transferMessage.style.display = 'none';
        if (o && d && o === d) {
            confirmBtn.disabled = true;
            errorMessage.style.display = 'block';
        } else if (o && d) {
            confirmBtn.disabled = false;
            errorMessage.style.display = 'none';
        } else {
            confirmBtn.disabled = true;
            errorMessage.style.display = 'none';
        }
    }

    storeOrigin.addEventListener('change', function () {
        if (this.value) {
            filmListContainer.style.display = 'block';
            filmList.innerHTML = '<div class="text-center text-muted"><div class="spinner-border spinner-border-sm"></div><p class="mt-2">Chargement...</p></div>';
            fetch(`/api/stores/${this.value}/inventory`)
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        filmList.innerHTML = data.data.map(f =>
                            `<div class="border-bottom pb-2 mb-2"><strong>${f.title}</strong> ${f.is_rented == 1 ? '<span class="badge bg-danger ms-2">Loué</span>' : ''}</div>`
                        ).join('');
                    } else {
                        filmList.innerHTML = '<p class="text-muted text-center">Aucun film dans ce store</p>';
                    }
                })
                .catch(() => { filmList.innerHTML = '<p class="text-danger text-center">Erreur de chargement</p>'; });
        } else {
            filmListContainer.style.display = 'none';
        }
        validateStores();
    });

    storeDestination.addEventListener('change', validateStores);

    confirmBtn.addEventListener('click', function () {
        const o = storeOrigin.value, d = storeDestination.value;
        if (!o || !d || o === d) return;

        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Transfert...';

        fetch('/api/stores/transfer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ origin_store_id: o, destination_store_id: d })
        })
        .then(r => r.json())
        .then(data => {
            transferMessage.className = data.success ? 'alert alert-success' : 'alert alert-danger';
            transferMessage.innerHTML = (data.success ? '<i class="bi bi-check-circle"></i> ' : '<i class="bi bi-exclamation-circle"></i> ') + (data.message ?? '');
            transferMessage.style.display = 'block';
            if (data.success) {
                storeOrigin.disabled = true;
                storeDestination.disabled = true;
                confirmBtn.innerHTML = '<i class="bi bi-check-circle"></i> Transféré !';
                setTimeout(() => location.reload(), 2000);
            } else {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="bi bi-check-circle"></i> Transférer';
            }
        })
        .catch(() => {
            transferMessage.className = 'alert alert-danger';
            transferMessage.innerHTML = '<i class="bi bi-exclamation-circle"></i> Erreur lors du transfert';
            transferMessage.style.display = 'block';
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="bi bi-check-circle"></i> Transférer';
        });
    });

    document.getElementById('changeStoreModal').addEventListener('show.bs.modal', function () {
        storeOrigin.value = '';
        storeDestination.value = '';
        storeOrigin.disabled = false;
        storeDestination.disabled = false;
        filmListContainer.style.display = 'none';
        transferMessage.style.display = 'none';
        errorMessage.style.display = 'none';
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="bi bi-check-circle"></i> Transférer';
    });
});
</script>
@endsection
