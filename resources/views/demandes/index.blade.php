@extends('adminlte::page')

@section('title','Mes demandes')

@section('content_header')
    <h1>Historique de mes demandes</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="mb-3 d-flex justify-content-between flex-wrap">
    <a href="{{ route('demandes.create') }}" class="btn btn-primary mb-2">
        <i class="fas fa-plus"></i> Nouvelle demande
    </a>

    <form method="GET" action="{{ route('demandes.index') }}" class="form-inline mb-2">
        <input type="text" name="article" class="form-control mr-2" placeholder="Article" value="{{ request('article') }}">
        <select name="statut" class="form-control mr-2">
            <option value="">-- Statut --</option>
            <option value="en_attente" {{ request('statut')=='en_attente'?'selected':'' }}>En attente</option>
            <option value="validee"    {{ request('statut')=='validee'?'selected':'' }}>Validée</option>
            <option value="recue"      {{ request('statut')=='recue'?'selected':'' }}>Reçue</option>
            <option value="refusee"    {{ request('statut')=='refusee'?'selected':'' }}>Refusée</option>
        </select>
        <button type="submit" class="btn btn-secondary">
            <i class="fas fa-search"></i> Filtrer
        </button>
    </form>
</div>

<div class="table-responsive">
<table class="table table-bordered table-striped align-middle">
    <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Gestionnaire</th>
            <th>Articles</th>
            <th>Statut</th>
            <th>Remarque</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($demandes as $demande)
            <tr>
                <td>{{ $demande->id }}</td>
                <td>{{ \Carbon\Carbon::parse($demande->date)->format('d/m/Y') }}</td>
                <td>{{ $demande->gestionnaire->name ?? '—' }}</td>
                <td>
                    @foreach($demande->lignes as $l)
                        <div>
                            {{ $l->article->nom ?? 'Article supprimé' }}
                            <span class="text-muted">× {{ $l->quantite_total }}</span>
                        </div>
                    @endforeach
                </td>
                <td>
                    @if($demande->statut == 'en_attente')
                        <span class="badge badge-warning">En attente</span>
                    @elseif($demande->statut == 'validee')
                        <span class="badge badge-success">Validée</span>
                    @elseif($demande->statut == 'recue')
                        <span class="badge badge-info">Reçue</span>
                    @else
                        <span class="badge badge-danger">Refusée</span>
                    @endif
                </td>
                <td>
                    @if($demande->statut == 'refusee' && $demande->motif_refus)
                        <span class="text-danger">{{ $demande->motif_refus }}</span>
                    @else
                        {{ $demande->remarque ?? '—' }}
                    @endif
                </td>
                <td class="text-nowrap">
                    <a href="{{ route('demandes.show', $demande) }}" class="btn btn-sm btn-outline-secondary">Voir</a>
                    @if($demande->statut === 'validee')
                        <form method="POST" action="{{ route('demandes.recevoir', $demande) }}" class="d-inline"
                              onsubmit="return confirm('Confirmer la réception de cette commande ?')">
                            @csrf
                            <button class="btn btn-sm btn-success">Commande reçue</button>
                        </form>
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
</div>

<div class="d-flex justify-content-center">
    {{ $demandes->links() }}
</div>
@stop