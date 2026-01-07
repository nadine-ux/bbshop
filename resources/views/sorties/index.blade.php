@extends('adminlte::page')

@section('title','Sorties')

@section('content_header')
    <h1>Liste des sorties</h1>
@stop

@section('content')
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('sorties.create') }}" class="btn btn-primary">Nouvelle sortie</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Filtres -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('sorties.index') }}" class="form-row">
                <div class="form-group col-md-2">
                    <label for="numero_bon">Numéro bon</label>
                    <input type="text" id="numero_bon" name="numero_bon" value="{{ request('numero_bon') }}" class="form-control" placeholder="Ex: SB-2025-001">
                </div>
                <div class="form-group col-md-2">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" value="{{ request('date') }}" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label for="destination">Destination</label>
                    <input type="text" id="destination" name="destination" value="{{ request('destination') }}" class="form-control" placeholder="Client / Lieu">
                </div>
                <div class="form-group col-md-2">
                    <label for="motif">Motif</label>
                    <input type="text" id="motif" name="motif" value="{{ request('motif') }}" class="form-control" placeholder="Motif">
                </div>
                <div class="form-group col-md-2">
                    <label for="article">Article</label>
                    <input type="text" id="article" name="article" value="{{ request('article') }}" class="form-control" placeholder="Nom article">
                </div>
                <div class="form-group col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Filtrer</button>
                    <a href="{{ route('sorties.index') }}" class="btn btn-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Numéro bon</th>
                        <th>Date</th>
                        <th>Destination</th>
                        <th>Motif</th>
                        <th>Articles sortis</th>
                        <th>Commentaire</th>
                        <th style="width:120px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($sorties as $sortie)
                    <tr>
                        <td>{{ $sortie->numero_bon ?? $sortie->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($sortie->created_at)->format('d/m/Y') }}</td>
                        <td>{{ $sortie->destination ?? '—' }}</td>
                        <td>{{ $sortie->motif ?? '—' }}</td>
                        <td>
                            @if($sortie->articles->isNotEmpty())
                                <ul class="mb-0">
                                    @foreach($sortie->articles as $article)
                                        @php
                                            $qt = $article->pivot->quantite_total;
                                            $c  = $article->contenance_carton ?: 1;
                                            $cartonsAffiche = intdiv($qt, $c);
                                            $resteAffiche   = $qt % $c;
                                        @endphp
                                        <li>
                                            <strong>{{ $article->nom }}</strong> :
                                            {{ $qt }} pièces ({{ $cartonsAffiche }} cartons et {{ $resteAffiche }} pièces)
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <em>Aucun article</em>
                            @endif
                        </td>
                        <td>{{ $sortie->commentaire ?? '—' }}</td>
                        <td class="text-center">
                            <!-- Icône Voir -->
                            <a href="{{ route('sorties.show',$sortie) }}" class="text-info mr-2" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <!-- Icône Modifier -->
                            <a href="{{ route('sorties.edit',$sortie) }}" class="text-warning mr-2" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- Icône Supprimer -->
                            <form action="{{ route('sorties.destroy',$sortie) }}" method="POST" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-0 m-0" 
                                        onclick="return confirm('Supprimer cette sortie ?')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <!-- Icône PDF -->
                           
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Aucune sortie enregistrée</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $sorties->links() }}
            </div>
        </div>
    </div>
@stop
