@extends('adminlte::page')

@section('title','Entrées')

@section('content_header')
    <h1>Liste des entrées</h1>
@stop

@section('content')
    <a href="{{ route('entrees.create') }}" class="btn btn-primary mb-3">Nouvelle entrée</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Num Bon</th>
                        <th>Date réception</th>
                        <th>Fournisseur</th>
                        <th>Numéro bon</th>
                        <th>Articles reçus</th>
                        <th>Commentaire</th>
                        <th style="width:120px;" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($entrees as $entree)
                    <tr>
                        <td>{{ $entree->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($entree->date_reception)->format('d/m/Y') }}</td>
                        <td>{{ $entree->fournisseur->nom ?? '—' }}</td>
                        <td>{{ $entree->numero_bon }}</td>
                        <td>
                            @if($entree->articles->isNotEmpty())
                                <ul class="mb-0">
                                    @foreach($entree->articles as $article)
                                        <li>
                                            <strong>{{ $article->nom }}</strong> :
                                            {{ $article->pivot->quantite_total }} pièces
                                            ({{ $article->pivot->quantite_cartons }} cartons,
                                             {{ $article->pivot->quantite_pieces }} pièces)
                                            @if($article->pivot->prix_unitaire)
                                                — Prix unitaire: {{ number_format($article->pivot->prix_unitaire, 2) }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <em>Aucun article</em>
                            @endif
                        </td>
                        <td>{{ $entree->commentaire ?? '—' }}</td>
                        <td class="text-center">
                            <!-- Icône Voir -->
                            <a href="{{ route('entrees.show',$entree) }}" class="text-info mr-2" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <!-- Icône Modifier -->
                            <a href="{{ route('entrees.edit',$entree) }}" class="text-warning mr-2" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- Icône Supprimer -->
                            <form action="{{ route('entrees.destroy',$entree) }}" method="POST" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-0 m-0" 
                                        onclick="return confirm('Supprimer cette entrée ?')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <!-- Icône PDF -->
                            <a href="" class="text-success ml-2" title="Télécharger PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Aucune entrée enregistrée</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <!-- Pagination responsive -->
            <div class="d-flex justify-content-center mt-3">
                {{ $entrees->links() }}
            </div>
        </div>
    </div>
@stop
