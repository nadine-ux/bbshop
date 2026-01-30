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
                    <a href="{{ route('inventaire.print.stock-critique') }}" 
   class="btn btn-danger" 
   target="_blank">
    🖨️ Imprimer la liste
</a>
                    <h2 class="mb-0">⚠️ Articles en Stock Critique</h2>
                    <p class="text-muted mb-0">Articles dont le stock est inférieur ou égal au minimum requis</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerte -->
    @if($articles->count() > 0)
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <div class="fs-3 me-3">⚠️</div>
        <div>
            <strong>Attention !</strong> Vous avez <strong>{{ $articles->total() }}</strong> article(s) en stock critique.
            Il est recommandé de passer des commandes rapidement.
        </div>
    </div>
    @endif

    <!-- Tableau -->
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Liste des articles à réapprovisionner</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Photo</th>
                            <th>Article</th>
                            <th>Catégorie</th>
                            <th>Fournisseur</th>
                            <th>Stock Actuel</th>
                            <th>Stock Min</th>
                            <th>Écart</th>
                            <th>Prix Achat</th>
                            <th>État</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                        <tr class="{{ $article->stock == 0 ? 'table-danger' : 'table-warning' }}">
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
                                <strong>{{ $article->nom }}</strong><br>
                                @if($article->code_barres)
                                    <small class="text-muted">
                                        <code>{{ $article->code_barres }}</code>
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $article->category->nom ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $article->fournisseur->nom ?? '-' }}</td>
                            <td>
                                <strong class="fs-5 {{ $article->stock == 0 ? 'text-danger' : 'text-warning' }}">
                                    {{ $article->stock }}
                                </strong>
                            </td>
                            <td>{{ $article->quantite_minimale }}</td>
                            <td>
                                <span class="badge bg-danger">
                                    {{ $article->stock - $article->quantite_minimale }}
                                </span>
                            </td>
                            <td>{{ number_format($article->prix_achat, 2) }} DA</td>
                            <td>
                                @if($article->stock == 0)
                                    <span class="badge bg-danger">📭 ÉPUISÉ</span>
                                @else
                                    <span class="badge bg-warning">⚠️ CRITIQUE</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('inventaire.show', $article) }}" 
                                       class="btn btn-outline-primary" 
                                       title="Voir détails">
                                        👁️
                                    </a>
                                    <a href="{{ route('entrees.create') }}?article_id={{ $article->id }}" 
                                       class="btn btn-outline-success" 
                                       title="Nouvelle entrée">
                                        📥
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="text-success">
                                    <i class="fs-1">✅</i>
                                    <p class="mb-0 mt-3"><strong>Excellent !</strong></p>
                                    <p class="text-muted">Aucun article en stock critique</p>
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

    <!-- Récapitulatif par catégorie -->
    @if($articles->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📊 Récapitulatif par catégorie</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Catégorie</th>
                                    <th>Articles critiques</th>
                                    <th>Articles épuisés</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $parCategorie = $articles->groupBy('category.nom');
                                @endphp
                                @foreach($parCategorie as $categorie => $items)
                                <tr>
                                    <td><strong>{{ $categorie }}</strong></td>
                                    <td>{{ $items->count() }}</td>
                                    <td>{{ $items->where('stock', 0)->count() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection