@extends('adminlte::page')

@section('title', 'Liste des Commandes')

@section('content_header')
    <h1>Liste des Commandes</h1>
@stop

@section('content')

{{-- ALERTES --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('success') }}
    </div>
@endif

{{-- STATS --}}
<div class="row mb-3">
    <div class="col-md-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $commandes->total() }}</h3>
                <p>Total commandes</p>
            </div>
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['en_attente'] }}</h3>
                <p>En attente</p>
            </div>
            <div class="icon"><i class="fas fa-clock"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['validee'] }}</h3>
                <p>Validées</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['rejetee'] }}</h3>
                <p>Rejetées</p>
            </div>
            <div class="icon"><i class="fas fa-times-circle"></i></div>
        </div>
    </div>
</div>

{{-- FILTRES --}}
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> Filtres</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('commandes.index') }}" id="filter-form">
            <div class="row">
                <div class="col-md-5">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text"
                               name="recherche"
                               class="form-control"
                               placeholder="N° commande ou gestionnaire..."
                               value="{{ request('recherche') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="statut" class="form-control">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="validée"    {{ request('statut') == 'validée'    ? 'selected' : '' }}>Validée</option>
                        <option value="rejetée"    {{ request('statut') == 'rejetée'    ? 'selected' : '' }}>Rejetée</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('commandes.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABLEAU --}}
<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="fas fa-list"></i> Commandes
        </h3>
        @can('create', App\Models\Commande::class)
        <a href="{{ route('commandes.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus"></i> Nouvelle commande
        </a>
        @endcan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th class="text-center" style="width:130px">N° commande</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Gestionnaire</th>
                        <th class="text-center">Nb articles</th>
                        <th class="text-center">Statut</th>
                        <th class="text-center" style="width:90px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commandes as $commande)
                    <tr>
                        <td class="text-center font-weight-bold text-primary">
                            {{ $commande->reference ?? 'CMD-'.$commande->id }}
                        </td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($commande->date)->format('d/m/Y') }}
                        </td>
                        <td>
                            <i class="fas fa-user-circle text-secondary mr-1"></i>
                            {{ $commande->user->name ?? '—' }}
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info">
                                {{ $commande->lignes->count() }} article(s)
                            </span>
                        </td>
                        <td class="text-center">
                            @if($commande->statut === 'en_attente')
                                <span class="badge badge-warning px-3 py-1">
                                    <i class="fas fa-clock mr-1"></i> En attente
                                </span>
                            @elseif($commande->statut === 'validée')
                                <span class="badge badge-success px-3 py-1">
                                    <i class="fas fa-check mr-1"></i> Validée
                                </span>
                            @else
                                <span class="badge badge-danger px-3 py-1">
                                    <i class="fas fa-times mr-1"></i> Rejetée
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button"
                                    class="btn btn-info btn-sm btn-detail"
                                    data-id="{{ $commande->id }}"
                                    title="Voir le détail">
                                <i class="fas fa-eye"></i> Voir
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Aucune commande trouvée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($commandes->hasPages())
    <div class="card-footer">
        {{ $commandes->links() }}
    </div>
    @endif
</div>

{{-- MODAL DÉTAIL --}}
<div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modal-detail-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modal-detail-label">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    <span id="modal-ref">Détail commande</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                {{-- Spinner chargement --}}
                <div id="modal-loading" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 text-muted">Chargement...</p>
                </div>

                {{-- Contenu chargé en AJAX --}}
                <div id="modal-content" style="display:none">

                    {{-- Info générale --}}
                    <div class="row mb-3" id="modal-infos"></div>

                    {{-- Note si en attente --}}
                    <div class="alert alert-info d-none" id="modal-note">
                        <i class="fas fa-info-circle mr-1"></i>
                        La quantité que la gestionnaire a saisie ne peut pas être modifiée ici —
                        seul le directeur peut valider cette commande.
                    </div>

                    {{-- Tableau articles --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Nom article</th>
                                    <th class="text-center">Code AL</th>
                                    <th class="text-center">Qté restante</th>
                                    <th class="text-center">Qté d'alerte</th>
                                    <th class="text-center">Qté à commander</th>
                                </tr>
                            </thead>
                            <tbody id="modal-tbody"></tbody>
                        </table>
                    </div>

                </div>
            </div>

            <div class="modal-footer" id="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Fermer
                </button>
            </div>

        </div>
    </div>
</div>

@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Ouvrir le modal et charger le détail AJAX ──────────────────────────
    document.querySelectorAll('.btn-detail').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            ouvrirDetail(id);
        });
    });

    function ouvrirDetail(id) {
        // Reset modal
        document.getElementById('modal-loading').style.display  = 'block';
        document.getElementById('modal-content').style.display  = 'none';
        document.getElementById('modal-ref').textContent        = 'Chargement...';
        document.getElementById('modal-note').classList.add('d-none');
        document.getElementById('modal-footer').innerHTML = `
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="fas fa-times"></i> Fermer
            </button>`;

        $('#modal-detail').modal('show');

        fetch(`/commandes/${id}/detail-json`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => afficherDetail(data))
        .catch(() => {
            document.getElementById('modal-loading').innerHTML =
                '<p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Erreur de chargement.</p>';
        });
    }

    function afficherDetail(data) {
        document.getElementById('modal-loading').style.display = 'none';
        document.getElementById('modal-content').style.display = 'block';

        // Référence
        document.getElementById('modal-ref').textContent = data.reference ?? ('CMD-' + data.id);

        // Infos générales
        const statut = badgeStatut(data.statut);
        document.getElementById('modal-infos').innerHTML = `
            <div class="col-md-3 col-6">
                <div class="info-box mb-2">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-hashtag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">N° commande</span>
                        <span class="info-box-number">${data.reference ?? 'CMD-'+data.id}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="info-box mb-2">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-calendar"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Date</span>
                        <span class="info-box-number">${data.date_formatee ?? data.date}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="info-box mb-2">
                    <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-user"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Gestionnaire</span>
                        <span class="info-box-number">${data.gestionnaire ?? '—'}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="info-box mb-2">
                    <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-flag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Statut</span>
                        <span class="info-box-number">${statut}</span>
                    </div>
                </div>
            </div>
        `;

        // Note si en attente
        if (data.statut === 'en_attente') {
            document.getElementById('modal-note').classList.remove('d-none');
        }

        // Lignes articles
        const tbody = document.getElementById('modal-tbody');
        if (!data.lignes || data.lignes.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">Aucun article</td></tr>`;
        } else {
            tbody.innerHTML = data.lignes.map(l => {
                const enAlerte = l.stock <= l.quantite_alerte && l.quantite_alerte > 0;
                return `<tr class="${enAlerte ? 'table-warning' : ''}">
                    <td class="font-weight-bold">${l.nom}</td>
                    <td class="text-center">${l.code_al ?? '—'}</td>
                    <td class="text-center">
                        <span class="badge badge-${l.stock > 0 ? 'success' : 'danger'}">
                            ${l.stock} pcs
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-warning text-dark">${l.quantite_alerte ?? 0} pcs</span>
                    </td>
                    <td class="text-center font-weight-bold">${l.quantite_commande} pcs</td>
                </tr>`;
            }).join('');
        }

        // Footer : bouton Valider si directeur + en attente
        @can('validate', App\Models\Commande::class)
        if (data.statut === 'en_attente') {
            document.getElementById('modal-footer').innerHTML = `
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button type="button" class="btn btn-success" onclick="validerCommande(${data.id})">
                    <i class="fas fa-check"></i> Valider la commande
                </button>
            `;
        }
        @endcan
    }

    function badgeStatut(s) {
        if (s === 'en_attente') return '<span class="badge badge-warning">En attente</span>';
        if (s === 'validée')    return '<span class="badge badge-success">Validée</span>';
        return '<span class="badge badge-danger">Rejetée</span>';
    }

    // ── Valider une commande (directeur) ───────────────────────────────────
    window.validerCommande = function (id) {
        if (!confirm('Confirmer la validation de cette commande ?')) return;

        fetch(`/commandes/${id}/valider`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                $('#modal-detail').modal('hide');
                // Recharger la page pour mettre à jour le tableau
                window.location.reload();
            } else {
                alert(data.message ?? 'Erreur lors de la validation.');
            }
        })
        .catch(() => alert('Erreur réseau.'));
    };

});
</script>
@stop

@section('css')
<style>
    .table td, .table th { vertical-align: middle !important; }
    .info-box { min-height: 60px; }
    .info-box-icon { width: 60px; line-height: 60px; font-size: 1.4rem; }
    .info-box-content { padding: 8px 10px; }
    .info-box-text { font-size: 12px; }
    .info-box-number { font-size: 14px; font-weight: 600; }
    .table-warning td { color: #856404; }
    .badge { font-size: 0.8em; padding: 5px 10px; }
</style>
@stop