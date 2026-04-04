@extends('adminlte::page')

@section('title','Entrées')

@section('content_header')
    <h1>Liste des entrées</h1>
@stop

@section('content')
    <a href="{{ route('entrees.create') }}" class="btn btn-primary mb-3">
        <i class="fas fa-plus"></i> Nouvelle entrée
    </a>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
{{-- Filtres --}}
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('entrees.index') }}" class="form-inline flex-wrap gap-2">

            {{-- Date exacte --}}
            <div class="form-group mr-2 mb-2">
                <label class="mr-1">Date</label>
                <input type="date" name="date" class="form-control form-control-sm"
                       value="{{ request('date') }}">
            </div>

            {{-- Mois --}}
            <div class="form-group mr-2 mb-2">
                <label class="mr-1">Mois</label>
                <select name="mois" class="form-control form-control-sm">
                    <option value="">— Tous —</option>
                    @foreach([
                        1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',
                        5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',
                        9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'
                    ] as $num => $nom)
                        <option value="{{ $num }}" {{ request('mois') == $num ? 'selected' : '' }}>
                            {{ $nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Année --}}
            <div class="form-group mr-2 mb-2">
                <label class="mr-1">Année</label>
                <select name="annee" class="form-control form-control-sm">
                    <option value="">— Toutes —</option>
                    @foreach($annees as $annee)
                        <option value="{{ $annee }}" {{ request('annee') == $annee ? 'selected' : '' }}>
                            {{ $annee }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tri --}}
            <div class="form-group mr-2 mb-2">
                <label class="mr-1">Trier par</label>
                <select name="sort" class="form-control form-control-sm">
                    <option value="date_reception" {{ request('sort','date_reception') === 'date_reception' ? 'selected' : '' }}>Date</option>
                    <option value="id"             {{ request('sort') === 'id' ? 'selected' : '' }}>N° Bon</option>
                </select>
            </div>

            <div class="form-group mr-2 mb-2">
                <select name="direction" class="form-control form-control-sm">
                    <option value="desc" {{ request('direction','desc') === 'desc' ? 'selected' : '' }}>↓ Décroissant</option>
                    <option value="asc"  {{ request('direction') === 'asc'  ? 'selected' : '' }}>↑ Croissant</option>
                </select>
            </div>

            <div class="mb-2">
                <button type="submit" class="btn btn-primary btn-sm mr-1">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                <a href="{{ route('entrees.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i> Réinitialiser
                </a>
            </div>

        </form>
    </div>
</div>
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>N° Bon</th>
                        <th>Date réception</th>
                        <th>Fournisseur</th>
                        <th>Articles reçus</th>
                        <th>Montant total</th>
                        <th>Commentaire</th>
                        <th style="width:140px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($entrees as $entree)
                    <tr>
                        <td><strong>#{{ $entree->id }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($entree->date_reception)->format('d/m/Y') }}</td>
                        <td>{{ $entree->fournisseur->nom ?? '—' }}</td>
                        <td>
                            @if($entree->articles->isNotEmpty())
                                <button type="button" class="btn btn-sm btn-info" 
                                        data-toggle="modal" 
                                        data-target="#articlesModal{{ $entree->id }}">
                                    <i class="fas fa-list"></i> {{ $entree->articles->count() }} article(s)
                                </button>

                                <!-- Modal pour afficher les articles -->
                                <div class="modal fade" id="articlesModal{{ $entree->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info">
                                                <h5 class="modal-title">Articles de l'entrée #{{ $entree->id }}</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Article</th>
                                                            <th class="text-center">Cartons</th>
                                                            <th class="text-center">Pièces</th>
                                                            <th class="text-center">Total pièces</th>
                                                            <th class="text-right">Prix unitaire</th>
                                                            <th class="text-right">Montant</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($entree->articles as $article)
                                                            @php
                                                                $montant = $article->pivot->quantite_total * $article->pivot->prix_unitaire;
                                                            @endphp
                                                            <tr>
                                                                <td><strong>{{ $article->nom }}</strong></td>
                                                                <td class="text-center">{{ $article->pivot->quantite_cartons }}</td>
                                                                <td class="text-center">{{ $article->pivot->quantite_pieces }}</td>
                                                                <td class="text-center">
                                                                    <span class="badge badge-primary">{{ $article->pivot->quantite_total }}</span>
                                                                </td>
                                                                <td class="text-right">{{ number_format($article->pivot->prix_unitaire, 2) }} DZD</td>
                                                                <td class="text-right"><strong>{{ number_format($montant, 2) }} DZD</strong></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <em class="text-muted">Aucun article</em>
                            @endif
                        </td>
                        <td class="text-right">
                            @php
                                $montantTotal = $entree->articles->sum(function($article) {
                                    return $article->pivot->quantite_total * $article->pivot->prix_unitaire;
                                });
                            @endphp
                            <strong>{{ number_format($montantTotal, 2) }} DZD</strong>
                        </td>
                        <td>{{ Str::limit($entree->commentaire, 30) ?? '—' }}</td>
                        <td class="text-center">
                            <!-- Voir -->
                            <a href="{{ route('entrees.show', $entree) }}" 
                               class="btn btn-sm btn-info" 
                               title="Voir le bon d'entrée">
                                <i class="fas fa-eye"></i>
                            </a>
                            <!-- Modifier -->
                            <a href="{{ route('entrees.edit', $entree) }}" 
                               class="btn btn-sm btn-warning" 
                               title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- Supprimer -->
                            <form action="{{ route('entrees.destroy', $entree) }}" 
                                  method="POST" 
                                  style="display:inline"
                                  onsubmit="return confirm('Voulez-vous vraiment supprimer cette entrée ? Cette action annulera tous les mouvements de stock associés.')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-sm btn-danger" 
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Aucune entrée enregistrée</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $entrees->links() }}
            </div>
        </div>
    </div>
@stop