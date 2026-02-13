@extends('adminlte::page')

@section('title', 'Bon d\'entrée #' . $entree->id)

@section('content')
<div class="card shadow-sm">
    <div class="card-body" id="bon-entree">

        {{-- EN-TÊTE --}}
        <div class="text-center mb-4">
            <h2><strong>BON D'ENTRÉE</strong></h2>
            <h4 class="text-muted">N° {{ str_pad($entree->id, 6, '0', STR_PAD_LEFT) }}</h4>
        </div>

        <hr>

        {{-- INFORMATIONS GÉNÉRALES --}}
        <div class="row mb-4">
            {{-- COLONNE GAUCHE --}}
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="150"><strong>Date de réception :</strong></td>
                        <td>{{ \Carbon\Carbon::parse($entree->date_reception)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Fournisseur :</strong></td>
                        <td>{{ $entree->fournisseur->nom ?? '—' }}</td>
                    </tr>
                    @if($entree->fournisseur && $entree->fournisseur->telephone)
                    <tr>
                        <td><strong>Téléphone :</strong></td>
                        <td>{{ $entree->fournisseur->telephone }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            {{-- COLONNE DROITE --}}
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="150"><strong>Gestionnaire :</strong></td>
                        <td>{{ $entree->gestionnaire->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date d'enregistrement :</strong></td>
                        <td>{{ $entree->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($entree->commentaire)
        <div class="alert alert-info">
            <strong><i class="fas fa-comment"></i> Commentaire :</strong> {{ $entree->commentaire }}
        </div>
        @endif

        {{-- TABLEAU DES ARTICLES --}}
        <table class="table table-bordered mt-4">
            <thead class="thead-dark">
                <tr class="text-center">
                    <th style="width: 5%">#</th>
                    <th style="width: 15%">Code-barres</th>
                    <th style="width: 30%">Désignation</th>
                    <th style="width: 10%">Cartons</th>
                    <th style="width: 10%">Pièces</th>
                    <th style="width: 10%">Total pièces</th>
                    <th style="width: 10%">Prix unitaire</th>
                    <th style="width: 10%">Montant</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                    $totalPieces = 0;
                @endphp

                @foreach($entree->articles as $index => $article)
                    @php
                        $quantiteTotal = $article->pivot->quantite_total;
                        $prixUnitaire = $article->pivot->prix_unitaire;
                        $montant = $quantiteTotal * $prixUnitaire;
                        $grandTotal += $montant;
                        $totalPieces += $quantiteTotal;
                    @endphp

                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $article->code_barres ?? '—' }}</td>
                        <td><strong>{{ $article->nom }}</strong></td>
                        <td class="text-center">{{ $article->pivot->quantite_cartons }}</td>
                        <td class="text-center">{{ $article->pivot->quantite_pieces }}</td>
                        <td class="text-center">
                            <span class="badge badge-primary">{{ $quantiteTotal }}</span>
                        </td>
                        <td class="text-right">{{ number_format($prixUnitaire, 2) }} DZD</td>
                        <td class="text-right"><strong>{{ number_format($montant, 2) }} DZD</strong></td>
                    </tr>
                @endforeach

                {{-- LIGNE TOTAUX --}}
                <tr class="table-secondary font-weight-bold">
                    <td colspan="5" class="text-right">TOTAUX :</td>
                    <td class="text-center">
                        <span class="badge badge-dark">{{ $totalPieces }}</span>
                    </td>
                    <td></td>
                    <td class="text-right">
                        <strong style="font-size: 1.1em;">{{ number_format($grandTotal, 2) }} DZD</strong>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- SIGNATURES --}}
        <div class="row mt-5 mb-4">
            <div class="col-md-6 text-center">
                <p><strong>Signature du fournisseur</strong></p>
                <div style="height: 80px; border-bottom: 2px solid #000; margin: 0 20px;"></div>
            </div>
            <div class="col-md-6 text-center">
                <p><strong>Signature du réceptionnaire</strong></p>
                <div style="height: 80px; border-bottom: 2px solid #000; margin: 0 20px;"></div>
            </div>
        </div>
    </div>

    {{-- BOUTONS D'ACTION --}}
    <div class="card-footer text-right no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <a href="{{ route('entrees.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>
@stop

@section('css')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            margin: 0;
            padding: 15px;
        }
        
        .card {
            box-shadow: none !important;
            border: none !important;
        }
        
        table {
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }
    
    #bon-entree {
        background: white;
    }
</style>
@stop