@extends('adminlte::page')

@section('title', 'Fournisseurs')

@section('content_header')
@stop

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('inventaire.index') }}" class="btn btn-outline-secondary mb-2">
                        ← Retour à l'inventaire
                    </a>
                    <h2 class="mb-0">📦 {{ $article->nom }}</h2>
                </div>
                <div>
                    <a href="{{ route('articles.edit', $article) }}" class="btn btn-warning">
                        ✏️ Modifier l'article
                    </a>
                </div>
                <a href="{{ route('inventaire.print.article', $article) }}" 
   class="btn btn-secondary" 
   target="_blank">
    🖨️ Imprimer la fiche
</a>
            </div>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    @if($article->photo)
                        <img src="{{ Storage::url($article->photo) }}" 
                             alt="{{ $article->nom }}" 
                             class="img-fluid rounded mb-3" 
                             style="max-height: 300px;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" 
                             style="height: 300px;">
                            <span class="display-1">📦</span>
                        </div>
                    @endif
                    
                    <h4>{{ $article->nom }}</h4>
                    @if($article->code_barres)
                        <p class="text-muted">
                            <code>{{ $article->code_barres }}</code>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="row g-3">
                <!-- Stock actuel -->
                <div class="col-md-6">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">📊 Stock Actuel</h6>
                            <h2 class="mb-0 
                                {{ $article->stock == 0 ? 'text-danger' : ($article->stock_critique ? 'text-warning' : 'text-success') }}">
                                {{ $article->stock }}
                            </h2>
                            <small class="text-muted">Min: {{ $article->quantite_minimale }}</small>
                            
                            @if($article->stock_critique)
                                <div class="alert alert-warning mt-2 mb-0">
                                    ⚠️ Stock en dessous du minimum !
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Valeur stock -->
                <div class="col-md-6">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">💰 Valeur Stock</h6>
                            <h2 class="mb-0 text-success">
                                {{ number_format($stats['valeur_stock'], 2) }} DA
                            </h2>
                            <small class="text-muted">Prix unitaire: {{ number_format($article->prix_achat, 2) }} DA</small>
                        </div>
                    </div>
                </div>

                <!-- Total entrées -->
                <div class="col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="text-white-50 mb-2">📥 Total Entrées</h6>
                            <h3 class="mb-0">{{ $stats['total_entrees'] }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Total sorties -->
                <div class="col-md-6">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h6 class="text-white-50 mb-2">📤 Total Sorties</h6>
                            <h3 class="mb-0">{{ $stats['total_sorties'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations détaillées -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">ℹ️ Informations détaillées</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%">Catégorie:</th>
                            <td>{{ $article->category->nom ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Fournisseur:</th>
                            <td>{{ $article->fournisseur->nom ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Contenance carton:</th>
                            <td>{{ $article->contenance_carton }} pièces</td>
                        </tr>
                        @if($article->date_peremption)
                        <tr>
                            <th>Date péremption:</th>
                            <td>{{ \Carbon\Carbon::parse($article->date_peremption)->format('d/m/Y') }}</td>
                        </tr>
                        @endif
                        @if($article->description)
                        <tr>
                            <th>Description:</th>
                            <td>{{ $article->description }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des entrées -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">📥 Historique des Entrées</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Fournisseur</th>
                                    <th>Cartons</th>
                                    <th>Pièces</th>
                                    <th>Total</th>
                                    <th>Prix Unitaire</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entrees as $entree)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($entree->date_reception)->format('d/m/Y') }}</td>
                                    <td>{{ $entree->fournisseur->nom ?? '-' }}</td>
                                    <td>{{ $entree->pivot->quantite_cartons }}</td>
                                    <td>{{ $entree->pivot->quantite_pieces }}</td>
                                    <td><strong>{{ $entree->pivot->quantite_total }}</strong></td>
                                    <td>{{ number_format($entree->pivot->prix_unitaire, 2) }} DA</td>
                                    <td>
                                        <a href="{{ route('entrees.show', $entree) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            Voir détails
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">
                                        Aucune entrée enregistrée
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($entrees->hasPages())
                <div class="card-footer bg-white">
                    {{ $entrees->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Historique des sorties -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">📤 Historique des Sorties</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Destination</th>
                                    <th>Motif</th>
                                    <th>Cartons</th>
                                    <th>Pièces</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sorties as $sortie)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($sortie->date_sortie)->format('d/m/Y') }}</td>
                                    <td>{{ $sortie->destination }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $sortie->motif }}</span>
                                    </td>
                                    <td>{{ $sortie->pivot->quantite_cartons }}</td>
                                    <td>{{ $sortie->pivot->quantite_pieces }}</td>
                                    <td><strong>{{ $sortie->pivot->quantite_total }}</strong></td>
                                    <td>
                                        <a href="{{ route('sorties.show', $sortie) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            Voir détails
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">
                                        Aucune sortie enregistrée
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($sorties->hasPages())
                <div class="card-footer bg-white">
                    {{ $sorties->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection