@extends('adminlte::page')

@section('title', 'Détail de la demande')

@section('content_header')
    <h1>Détail de la demande #{{ $demande->id }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header bg-primary">
        <h3 class="card-title">Informations principales</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>Employé</th>
                <td>{{ $demande->employe->name }}</td>
            </tr>
            <tr>
                <th>Article</th>
                <td>{{ $demande->article->nom }}</td>
            </tr>
            <tr>
                <th>Quantité en cartons</th>
                <td>{{ $demande->quantite_cartons }}</td>
            </tr>
            <tr>
                <th>Quantité en pièces</th>
                <td>{{ $demande->quantite_pieces }}</td>
            </tr>
            <tr>
                <th>Quantité totale</th>
                <td><span class="badge badge-info">{{ $demande->quantite_total }}</span></td>
            </tr>
            <tr>
                <th>Remarque</th>
                <td>{{ $demande->remarque ?? '—' }}</td>
            </tr>
            <tr>
                <th>Date</th>
                <td>{{ $demande->date }}</td>
            </tr>
            <tr>
                <th>Statut</th>
                <td>
                    @if($demande->statut == 'en_attente')
                        <span class="badge badge-warning">En attente</span>
                    @elseif($demande->statut == 'validee')
                        <span class="badge badge-success">Validée</span>
                    @elseif($demande->statut == 'refusee')
                        <span class="badge badge-danger">Refusée</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

{{-- Boutons d’action --}}
<div class="mt-3">
    <a href="{{ route('demandes.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>

    @can('manage requests')
        {{-- Formulaire de validation --}}
        <form action="{{ route('demandes.valider', $demande->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="fas fa-check"></i> Valider
            </button>
        </form>

        {{-- Formulaire de refus avec remarque --}}
        <form action="{{ route('demandes.refuser', $demande->id) }}" method="POST" style="display:inline;">
            @csrf
            <div class="form-group d-inline-block">
                <input type="text" name="remarque" class="form-control" placeholder="Motif du refus" style="width:200px;">
            </div>
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-times"></i> Refuser
            </button>
        </form>
    @endcan
</div>
@stop
