@extends('adminlte::page')

@section('title','Bon d’entrée')

@section('content')
<div class="card shadow-sm">
    <div class="card-body" id="bon-entree">

        {{-- TITRE --}}
        <h2 class="text-center mb-5">
            <strong>Bon d’entrée</strong>
        </h2>

        {{-- INFOS BAS GAUCHE / DROITE --}}
        <div class="row mb-4">
            {{-- GAUCHE --}}
            <div class="col-md-6">
                <p><strong>Date réception :</strong>
                    {{ \Carbon\Carbon::parse($entree->date_reception)->format('d/m/Y') }}
                </p>
                <p><strong>N° Bon :</strong> {{ $entree->id }}</p>
                <p><strong>Fournisseur :</strong> {{ $entree->fournisseur->nom ?? '—' }}</p>
            </div>

            {{-- DROITE --}}
            <div class="col-md-6 text-right">
                <p><strong>Récepteur / Destination :</strong></p>
                <p><strong>Gestionnaire :</strong> {{ $entree->gestionnaire->name ?? '—' }}</p>

            </div>
        </div>

        {{-- TABLEAU --}}
        <table class="table table-bordered mt-4">
            <thead class="thead-dark text-center">
                <tr>
                    <th>Code-barres</th>
                    <th>ID Article</th>
                    <th>Cartons</th>
                    <th>Pièces</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entree->articles as $article)
                  @php
    $quantiteTotal = ($article->pivot->quantite_cartons * $article->contenance_carton) 
                     + $article->pivot->quantite_pieces;

    $total = $quantiteTotal * $article->prix_achat;
@endphp

                    <tr class="text-center">
                        <td>{{ $article->code_barres ?? '—' }}</td>
                        <td>{{ $article->nom }}</td>
                        <td>{{ $article->pivot->quantite_cartons }}</td>
                        <td>{{ $article->pivot->quantite_pieces }}</td>
                        <td>{{ number_format($article->prix_achat, 2) }}</td>
                        <td>{{ number_format($total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- SIGNATURES --}}
        <div class="row mt-5">
            <div class="col-md-6">
                <strong>Signature fournisseur :</strong><br><br>
                ______________________
            </div>
            <div class="col-md-6 text-right">
                <strong>Signature réception :</strong><br><br>
                ______________________
            </div>
        </div>
    </div>

    {{-- BOUTONS --}}
    <div class="card-footer text-right">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <a href="{{ route('entrees.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>
@stop
