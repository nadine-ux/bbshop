@extends('adminlte::page')

@section('title', 'Demande #' . $demande->id)

@section('content_header')
    <h1>
        Demande #{{ $demande->id }}
        <x-statut-badge :statut="$demande->statut" class="ml-2" />
    </h1>
@stop

@section('content')
@php $user = auth()->user(); @endphp

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">

        <p class="mb-1"><strong>Employé :</strong> {{ $demande->employe->name ?? '—' }}</p>
        <p class="mb-1"><strong>Gestionnaire :</strong> {{ $demande->gestionnaire->name ?? '—' }}</p>
        <p class="mb-1"><strong>Date :</strong> {{ \Carbon\Carbon::parse($demande->date)->format('d/m/Y') }}</p>
        @if($demande->remarque)
            <p class="mb-1"><strong>Remarque :</strong> {{ $demande->remarque }}</p>
        @endif
        @if($demande->motif_refus)
            <p class="mb-1 text-danger"><strong>Motif du refus :</strong> {{ $demande->motif_refus }}</p>
        @endif
        @if($demande->date_validation)
            <p class="mb-1"><strong>Validée le :</strong> {{ \Carbon\Carbon::parse($demande->date_validation)->format('d/m/Y H:i') }}</p>
        @endif
        @if($demande->date_reception)
            <p class="mb-1"><strong>Reçue le :</strong> {{ \Carbon\Carbon::parse($demande->date_reception)->format('d/m/Y H:i') }}</p>
        @endif

        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Article</th>
                        <th>Cartons</th>
                        <th>Pièces</th>
                        <th>Total (pcs)</th>
                        <th>Stock actuel</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($demande->lignes as $l)
                        <tr>
                            <td>{{ $l->article->nom ?? 'Article supprimé' }}</td>
                            <td>{{ $l->quantite_cartons }}</td>
                            <td>{{ $l->quantite_pieces }}</td>
                            <td>{{ $l->quantite_total }}</td>
                            <td>{{ $l->article->stock ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Actions gestionnaire --}}
        @if($demande->statut === 'en_attente' && ($user->hasRole('Directeur') || $demande->gestionnaire_id === $user->id))
            <form method="POST" action="{{ route('demandes.valider', $demande) }}" class="mt-3"
                  onsubmit="return confirm('Valider la demande (préparée et envoyée) ?')">
                @csrf
                <button class="btn btn-success">
                    <i class="fas fa-check"></i> Préparer et valider
                </button>
            </form>

            <form method="POST" action="{{ route('demandes.refuser', $demande) }}" class="mt-4">
                @csrf
                <div class="form-group col-md-6 px-0">
                    <label>Motif du refus</label>
                    <textarea name="motif_refus" rows="2" class="form-control" required></textarea>
                </div>
                <button class="btn btn-outline-danger">
                    <i class="fas fa-times"></i> Refuser
                </button>
            </form>
        @endif

        {{-- Action employé --}}
        @if($demande->statut === 'validee' && $demande->employe_id === $user->id)
            <form method="POST" action="{{ route('demandes.recevoir', $demande) }}" class="mt-3"
                  onsubmit="return confirm('Confirmer la réception ?')">
                @csrf
                <button class="btn btn-success">
                    <i class="fas fa-box-open"></i> Commande reçue
                </button>
            </form>
        @endif

        <div class="mt-4">
            <a href="{{ route('demandes.index') }}" class="btn btn-link px-0">← Retour</a>
        </div>
    </div>
</div>
@stop