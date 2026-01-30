@extends('adminlte::page')

@section('title', 'Fournisseurs')

@section('content_header')
@stop

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>📦 Inventaire Global</h2>
                <div>
                    <a href="{{ route('inventaire.mouvements') }}" class="btn btn-outline-primary">
                        📊 Journal des mouvements
                    </a>
                    <a href="{{ route('inventaire.valorisation') }}" class="btn btn-outline-success">
                        💰 Valorisation
                    </a>
                    <a href="{{ route('inventaire.stock-critique') }}" class="btn btn-outline-danger">
                        ⚠️ Alertes stock
                    </a>
                    <a href="{{ route('inventaire.print', request()->all()) }}" 
   class="btn btn-secondary" 
   target="_blank">
    🖨️ Imprimer
</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Articles</h6>
                            <h3 class="mb-0">{{ $stats['total_articles'] }}</h3>
                        </div>
                        <div class="fs-1">📦</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Stock Critique</h6>
                            <h3 class="mb-0">{{ $stats['stock_critique'] }}</h3>
                        </div>
                        <div class="fs-1">⚠️</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Valeur Stock</h6>
                            <h3 class="mb-0">{{ number_format($stats['valeur_stock'], 2) }} DA</h3>
                        </div>
                        <div class="fs-1">💰</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Articles Épuisés</h6>
                            <h3 class="mb-0">{{ $stats['articles_epuises'] }}</h3>
                        </div>
                        <div class="fs-1">📭</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('inventaire.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">🔍 Recherche</label>
                    <input type="text" name="recherche" class="form-control" 
                           placeholder="Nom ou code-barres..." 
                           value="{{ request('recherche') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">📂 Catégorie</label>
                    <select name="categorie_id" class="form-select">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}" 
                                {{ request('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                {{ $categorie->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">⚠️ État stock</label>
                    <select name="stock_critique" class="form-select">
                        <option value="">Tous</option>
                        <option value="1" {{ request('stock_critique') == '1' ? 'selected' : '' }}>
                            Stock critique uniquement
                        </option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des articles -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Liste des articles en stock</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Photo</th>
                            <th>Nom</th>
                            <th>Code-barres</th>
                            <th>Catégorie</th>
                            <th>Stock Actuel</th>
                            <th>Stock Min.</th>
                            <th>Prix Achat</th>
                            <th>Valeur</th>
                            <th>État</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                        <tr class="{{ $article->stock_critique ? 'table-warning' : '' }}">
                            <td>
                                @if($article->photo)
                                    <img src="{{ Storage::url($article->photo) }}" 
                                         alt="{{ $article->nom }}" 
                                         class="rounded" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center text-white" 
                                         style="width: 50px; height: 50px;">
                                        📦
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $article->nom }}</strong>
                            </td>
                            <td>
                                <code>{{ $article->code_barres ?? '-' }}</code>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $article->category->nom ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <strong class="fs-5">{{ $article->stock }}</strong>
                            </td>
                            <td>{{ $article->quantite_minimale }}</td>
                            <td>{{ number_format($article->prix_achat, 2) }} DA</td>
                            <td>
                                <strong>{{ number_format($article->stock * $article->prix_achat, 2) }} DA</strong>
                            </td>
                            <td>
                                @if($article->stock == 0)
                                    <span class="badge bg-danger">📭 Épuisé</span>
                                @elseif($article->stock_critique)
                                    <span class="badge bg-warning">⚠️ Critique</span>
                                @else
                                    <span class="badge bg-success">✅ OK</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('inventaire.show', $article) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="Voir détails">
                                    👁️ Détails
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fs-1">📦</i>
                                    <p class="mb-0">Aucun article trouvé</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($articles->hasPages())
        <div class="card-footer bg-white">
            {{ $articles->links() }}
        </div>
        @endif
    </div>
</div>
@stop

@section('css')
@endsection