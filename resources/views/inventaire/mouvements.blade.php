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
                    <h2 class="mb-0">📊 Journal des Mouvements</h2>
                    <p class="text-muted mb-0">Historique complet depuis la table inventaires</p>
                </div>
                <div>
                    <a href="{{ route('inventaire.print.mouvements', request()->all()) }}" 
                       class="btn btn-secondary" 
                       target="_blank">
                        🖨️ Imprimer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Mouvements</h6>
                            <h3 class="mb-0">{{ $stats['total_mouvements'] }}</h3>
                        </div>
                        <div class="fs-1">📊</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">📥 Entrées</h6>
                            <h3 class="mb-0">{{ $stats['total_entrees'] }}</h3>
                        </div>
                        <div class="fs-1">📥</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">📤 Sorties</h6>
                            <h3 class="mb-0">{{ $stats['total_sorties'] }}</h3>
                        </div>
                        <div class="fs-1">📤</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Articles concernés</h6>
                            <h3 class="mb-0">{{ $stats['nombre_articles'] }}</h3>
                        </div>
                        <div class="fs-1">📦</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres par date -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('inventaire.mouvements') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">📅 Date début</label>
                    <input type="date" name="date_debut" class="form-control" 
                           value="{{ \Carbon\Carbon::parse($dateDebut)->format('Y-m-d') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">📅 Date fin</label>
                    <input type="date" name="date_fin" class="form-control" 
                           value="{{ \Carbon\Carbon::parse($dateFin)->format('Y-m-d') }}">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        🔍 Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des mouvements -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Liste des mouvements 
                <small class="text-muted">
                    (Du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} 
                    au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }})
                </small>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Article</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Stock Avant</th>
                            <th>Stock Après</th>
                            <th>Prix Unit.</th>
                            <th>Motif</th>
                            <th>Utilisateur</th>
                            <th>Référence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mouvements as $mouvement)
                        <tr>
                            <td>
                                <strong>{{ $mouvement->date_mouvement->format('d/m/Y') }}</strong><br>
                                <small class="text-muted">{{ $mouvement->date_mouvement->format('H:i') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('inventaire.show', $mouvement->article) }}" class="text-decoration-none">
                                    <strong>{{ $mouvement->article->nom }}</strong>
                                </a><br>
                                <small class="text-muted">{{ $mouvement->article->category->nom ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $mouvement->type_color }}">
                                    {{ $mouvement->type_libelle }}
                                </span>
                            </td>
                            <td>
                                <strong class="text-{{ $mouvement->type == 'entree' ? 'success' : 'danger' }}">
                                    {{ $mouvement->type == 'entree' ? '+' : '-' }}{{ $mouvement->quantite }}
                                </strong>
                            </td>
                            <td>{{ $mouvement->stock_avant }}</td>
                            <td>
                                <strong>{{ $mouvement->stock_apres }}</strong>
                            </td>
                            <td>
                                @if($mouvement->prix_unitaire)
                                    {{ number_format($mouvement->prix_unitaire, 2) }} DA
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $mouvement->motif ?? '-' }}</small>
                            </td>
                            <td>
                                <small>{{ $mouvement->user->name ?? '-' }}</small>
                            </td>
                            <td>
                                @if($mouvement->entree_id)
                                    <a href="{{ route('entrees.show', $mouvement->entree_id) }}" 
                                       class="btn btn-sm btn-outline-success">
                                        E#{{ $mouvement->entree_id }}
                                    </a>
                                @elseif($mouvement->sortie_id)
                                    <a href="{{ route('sorties.show', $mouvement->sortie_id) }}" 
                                       class="btn btn-sm btn-outline-danger">
                                        S#{{ $mouvement->sortie_id }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fs-1">📭</i>
                                    <p class="mb-0 mt-2">Aucun mouvement enregistré sur cette période</p>
                                    <small>Du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($mouvements->hasPages())
        <div class="card-footer bg-white">
            {{ $mouvements->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Statistiques complémentaires -->
    @if($mouvements->count() > 0)
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">📊 Répartition par type</h6>
                </div>
                <div class="card-body">
                    @php
                        $entreesCount = $mouvements->where('type', 'entree')->count();
                        $sortiesCount = $mouvements->where('type', 'sortie')->count();
                        $total = $entreesCount + $sortiesCount;
                    @endphp
                    
                    @if($total > 0)
                    <div class="progress mb-3" style="height: 30px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ ($entreesCount / $total) * 100 }}%"
                             aria-valuenow="{{ $entreesCount }}" aria-valuemin="0" aria-valuemax="{{ $total }}">
                            Entrées: {{ $entreesCount }} ({{ round(($entreesCount / $total) * 100, 1) }}%)
                        </div>
                        <div class="progress-bar bg-danger" role="progressbar" 
                             style="width: {{ ($sortiesCount / $total) * 100 }}%"
                             aria-valuenow="{{ $sortiesCount }}" aria-valuemin="0" aria-valuemax="{{ $total }}">
                            Sorties: {{ $sortiesCount }} ({{ round(($sortiesCount / $total) * 100, 1) }}%)
                        </div>
                    </div>
                    @endif

                    <table class="table table-sm">
                        <tr>
                            <td><strong>Total Entrées</strong></td>
                            <td class="text-end"><span class="badge bg-success">{{ $entreesCount }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Total Sorties</strong></td>
                            <td class="text-end"><span class="badge bg-danger">{{ $sortiesCount }}</span></td>
                        </tr>
                        <tr class="table-light">
                            <td><strong>Différence</strong></td>
                            <td class="text-end">
                                <strong class="text-{{ ($entreesCount - $sortiesCount) >= 0 ? 'success' : 'danger' }}">
                                    {{ $entreesCount - $sortiesCount >= 0 ? '+' : '' }}{{ $entreesCount - $sortiesCount }}
                                </strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0">📈 Top 5 Articles les plus actifs</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Article</th>
                                    <th class="text-center">Mouvements</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $topArticles = $mouvements->groupBy('article_id')
                                        ->map(function($items) {
                                            return [
                                                'article' => $items->first()->article,
                                                'count' => $items->count()
                                            ];
                                        })
                                        ->sortByDesc('count')
                                        ->take(5);
                                @endphp
                                @foreach($topArticles as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('inventaire.show', $item['article']) }}" class="text-decoration-none">
                                            {{ $item['article']->nom }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $item['count'] }}</span>
                                    </td>
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

@section('css')
@endsection