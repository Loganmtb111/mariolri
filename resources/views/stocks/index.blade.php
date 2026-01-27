@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestion des stocks de DVD</h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#changeStoreModal">
                            <i class="bi bi-shop"></i> Changer l'emplacement du stock
                        </button>
                        <a href="#" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Ajouter un DVD au stock
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

                    @isset($error)
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-circle"></i> {{ $error }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endisset

                    <!-- Liste des DVD en stock -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Titre du film</th>
                                    <th>Genre</th>
                                    <th>Total exemplaires</th>
                                    <th>Disponibilité</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($films as $film)
                                    <tr>
                                        <td><strong>{{ $film->title }}</strong></td>
                                        <td><span class="badge bg-secondary">{{ $film->category }}</span></td>
                                        <td>{{ $film->total_inventories }}</td>
                                        <td>
                                            @if($film->status === 'available')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> {{ $film->status_label }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-x-circle"></i> {{ $film->status_label }}
                                                </span>
                                                @if($film->status_reason)
                                                    <small class="text-muted d-block">{{ $film->status_reason }}</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-success" title="Ajouter">
                                                    <i class="bi bi-plus"></i> Ajouter
                                                </button>
                                                <button class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="bi bi-pencil"></i> Modifier
                                                </button>
                                                @if($film->status === 'unavailable')
                                                    <button class="btn btn-sm btn-danger" disabled title="Impossible de supprimer un DVD non disponible">
                                                        <i class="bi bi-trash"></i> Supprimer
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce DVD ?')">
                                                        <i class="bi bi-trash"></i> Supprimer
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">Aucun film disponible dans le stock</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Légende -->
                    <div class="mt-4 alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Légende :</h6>
                        <ul class="mb-0">
                            <li><span class="badge bg-success">Disponible</span> : Le DVD est disponible dans le store</li>
                            <li><span class="badge bg-secondary">Non disponible</span> : Le DVD est soit loué, soit retiré de la liste pour le client</li>
                            <li>Le bouton <strong>Supprimer</strong> est désactivé si le DVD est actuellement loué</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour changer le store -->
<div class="modal fade" id="changeStoreModal" tabindex="-1" aria-labelledby="changeStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStoreModalLabel">Voulez-vous changer le store ?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Liste des stores</p>

                <!-- Message de succès/erreur -->
                <div id="transferMessage" class="alert" style="display: none;" role="alert"></div>

                <!-- Store d'origine -->
                <div class="mb-3">
                    <label for="storeOrigin" class="form-label">Store d'origine</label>
                    <select class="form-select" id="storeOrigin">
                        <option value="">Sélectionner un store</option>
                        <option value="1">Store 1</option>
                        <option value="2">Store 2</option>
                    </select>
                </div>

                <!-- Store de destination -->
                <div class="mb-3">
                    <label for="storeDestination" class="form-label">Store de destination</label>
                    <select class="form-select" id="storeDestination">
                        <option value="">Sélectionner un store</option>
                        <option value="1">Store 1</option>
                        <option value="2">Store 2</option>
                    </select>
                </div>

                <!-- Liste des films du store d'origine -->
                <div id="filmListContainer" style="display: none;">
                    <h6 class="mb-2">DVDs dans le store d'origine</h6>
                    <div id="filmList" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.25rem; padding: 0.5rem;">
                        <div class="text-center text-muted">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            <p class="mt-2">Chargement des films...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div id="errorMessage" class="text-danger small me-auto" style="display: none;">
                    Les stores d'origine et de destination doivent être différents
                </div>
                <button type="button" class="btn btn-success" id="confirmStoreChange">
                    <i class="bi bi-check-circle"></i> Oui
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const storeOrigin = document.getElementById('storeOrigin');
    const storeDestination = document.getElementById('storeDestination');
    const confirmBtn = document.getElementById('confirmStoreChange');
    const errorMessage = document.getElementById('errorMessage');
    const filmListContainer = document.getElementById('filmListContainer');
    const filmList = document.getElementById('filmList');
    const transferMessage = document.getElementById('transferMessage');

    function validateStores() {
        const originValue = storeOrigin.value;
        const destinationValue = storeDestination.value;

        // Réinitialiser le message de transfert
        transferMessage.style.display = 'none';
        transferMessage.className = 'alert';
        transferMessage.textContent = '';

        if (originValue && destinationValue && originValue === destinationValue) {
            confirmBtn.disabled = true;
            errorMessage.style.display = 'block';
        } else if (originValue && destinationValue) {
            confirmBtn.disabled = false;
            errorMessage.style.display = 'none';
        } else {
            confirmBtn.disabled = true;
            errorMessage.style.display = 'none';
        }
    }

    function loadStoreInventory(storeId) {
        // Afficher le conteneur et le loader
        filmListContainer.style.display = 'block';
        filmList.innerHTML = `
            <div class="text-center text-muted">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-2">Chargement des films...</p>
            </div>
        `;

        // Appeler l'API
        fetch(`/api/stores/${storeId}/inventory`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    // Afficher la liste des films
                    filmList.innerHTML = data.data.map(film => `
                        <div class="border-bottom pb-2 mb-2">
                            <strong>${film.title}</strong>
                            ${film.is_rented == 1 ? '<span class="badge bg-danger ms-2">Loué</span>' : ''}
                            <div class="small text-muted">
                                ${film.category ? `<span class="badge bg-secondary">${film.category}</span>` : ''}
                            </div>
                        </div>
                    `).join('');
                } else {
                    filmList.innerHTML = '<p class="text-muted text-center">Aucun film dans ce store</p>';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                filmList.innerHTML = '<p class="text-danger text-center">Erreur lors du chargement des films</p>';
            });
    }

    storeOrigin.addEventListener('change', function() {
        const storeId = this.value;
        if (storeId) {
            loadStoreInventory(storeId);
        } else {
            filmListContainer.style.display = 'none';
        }
        validateStores();
    });

    storeDestination.addEventListener('change', validateStores);

    // Gérer le clic sur le bouton de confirmation
    confirmBtn.addEventListener('click', function() {
        const originStoreId = storeOrigin.value;
        const destinationStoreId = storeDestination.value;

        if (!originStoreId || !destinationStoreId || originStoreId === destinationStoreId) {
            return;
        }

        // Désactiver le bouton pendant le traitement
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Transfert en cours...';

        // Récupérer le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Envoyer la requête de transfert
        fetch('/api/stores/transfer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                origin_store_id: originStoreId,
                destination_store_id: destinationStoreId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher un message de succès en vert
                transferMessage.className = 'alert alert-success';
                transferMessage.innerHTML = '<i class="bi bi-check-circle"></i> ' + data.message;
                transferMessage.style.display = 'block';

                // Désactiver les menus et le bouton
                storeOrigin.disabled = true;
                storeDestination.disabled = true;
                confirmBtn.innerHTML = '<i class="bi bi-check-circle"></i> Transféré !';

                // Recharger la page après 2 secondes
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else {
                // Afficher un message d'erreur en rouge
                transferMessage.className = 'alert alert-danger';
                transferMessage.innerHTML = '<i class="bi bi-exclamation-circle"></i> Erreur : ' + data.message;
                transferMessage.style.display = 'block';

                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="bi bi-check-circle"></i> Oui';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);

            // Afficher un message d'erreur en rouge
            transferMessage.className = 'alert alert-danger';
            transferMessage.innerHTML = '<i class="bi bi-exclamation-circle"></i> Une erreur est survenue lors du transfert';
            transferMessage.style.display = 'block';

            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="bi bi-check-circle"></i> Oui';
        });
    });

    confirmBtn.disabled = true;

    // Réinitialiser la modal quand elle est ouverte
    const modal = document.getElementById('changeStoreModal');
    modal.addEventListener('show.bs.modal', function() {
        // Réinitialiser les champs
        storeOrigin.value = '';
        storeDestination.value = '';
        storeOrigin.disabled = false;
        storeDestination.disabled = false;
        filmListContainer.style.display = 'none';

        // Réinitialiser les messages
        transferMessage.style.display = 'none';
        transferMessage.className = 'alert';
        transferMessage.textContent = '';
        errorMessage.style.display = 'none';

        // Réinitialiser le bouton
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="bi bi-check-circle"></i> Oui';
    });
});
</script>

@endsection