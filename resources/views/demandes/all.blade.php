@extends('adminlte::page')

@section('title','Demandes à traiter')

@section('content_header')
    <h1>Demandes des employés</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>Employé</th>
            <th>Article</th>
            <th>Image</th>
            <th>Quantité</th>
            <th>Statut</th>
            <th>Remarque</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($demandes as $demande)
            <tr>
                <td>{{ $demande->employe->name }}</td>
                <td>{{ $demande->article->nom }}</td>
                <td>
                    @if($demande->article->photo)
                        <img src="{{ $demande->article->photo }}" alt="{{ $demande->article->nom }}" width="60">
                    @else
                        —
                    @endif
                </td>
                <td>
    {{ $demande->quantite_cartons }} carton(s) +
    {{ $demande->quantite_pieces }} pièce(s)
    = <strong>{{ $demande->quantite_total }}</strong> pièces
</td>

                <td>
                    @if($demande->statut == 'en_attente')
                        <span class="badge badge-warning">En attente</span>
                    @elseif($demande->statut == 'validee')
                        <span class="badge badge-success">Validée</span>
                    @else
                        <span class="badge badge-danger">Refusée</span>
                    @endif
                </td>
                <td>{{ $demande->remarque ?? '—' }}</td>
                <td>
                    @if($demande->statut == 'en_attente')
                        <form action="{{ route('demandes.valider',$demande) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i> Valider
                            </button>
                        </form>
                        <form action="{{ route('demandes.refuser',$demande) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="text" name="remarque" placeholder="Motif du refus" class="form-control mb-1">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-times"></i> Refuser
                            </button>
                        </form>
                    @else
                        —
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Aucune demande</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="d-flex justify-content-center">
    {{ $demandes->links() }}
</div>
@stop
