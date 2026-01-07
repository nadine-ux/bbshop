@extends('adminlte::page')

@section('title','Mes demandes')

@section('content_header')
    <h1>Historique de mes demandes</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="mb-3 d-flex justify-content-between">
    <!-- Bouton nouvelle demande -->
    <a href="{{ route('demandes.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nouvelle demande
    </a>

    <!-- Formulaire de filtre -->
    <form method="GET" action="{{ route('demandes.index') }}" class="form-inline">
        <input type="text" name="article" class="form-control mr-2" placeholder="Article" value="{{ request('article') }}">
        <select name="statut" class="form-control mr-2">
            <option value="">-- Statut --</option>
            <option value="en_attente" {{ request('statut')=='en_attente'?'selected':'' }}>En attente</option>
            <option value="validee" {{ request('statut')=='validee'?'selected':'' }}>Validée</option>
            <option value="refusee" {{ request('statut')=='refusee'?'selected':'' }}>Refusée</option>
        </select>
        <button type="submit" class="btn btn-secondary">
            <i class="fas fa-search"></i> Filtrer
        </button>
    </form>
</div>

<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>Article</th>
            <th>Image</th>
            <th>Quantité</th>
            <th>Statut</th>
            <th>Remarque</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($demandes as $demande)
            <tr>
                <td>{{ $demande->article->nom }}</td>
                <td>
                    @if($demande->article->photo)
                        <img src="{{ $demande->article->photo }}" alt="{{ $demande->article->nom }}" width="60">
                    @else
                        —
                    @endif
                </td>
                <td>{{ $demande->quantite }}</td>
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
                <td>{{ \Carbon\Carbon::parse($demande->date)->format('d/m/Y') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">Aucune demande</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="d-flex justify-content-center">
    {{ $demandes->links() }}
</div>
@stop
