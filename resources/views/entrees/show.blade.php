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
                    @if($entree->remise_globale > 0)
                    <tr>
                        <td><strong>Remise globale :</strong></td>
                        <td><span class="badge badge-danger" style="font-size:1em;">{{ number_format($entree->remise_globale, 2) }}%</span></td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        @if($entree->commentaire)
        <div class="alert alert-info">
            <strong><i class="fas fa-comment"></i> Commentaire :</strong> {{ $entree->commentaire }}
        </div>
        @endif

        {{-- TABLEAU DES ARTICLES --}}
        @php
            $grandTotalBrut = 0;
            $sousTotal      = 0;
            $totalPieces    = 0;
        @endphp

        <table class="table table-bordered mt-4">
            <thead class="thead-dark">
                <tr class="text-center">
                    <th style="width:4%">#</th>
                    <th style="width:13%">Code-barres</th>
                    <th style="width:23%">Désignation</th>
                    <th style="width:7%">Cartons</th>
                    <th style="width:7%">Pièces</th>
                    <th style="width:8%">Total pièces</th>
                    <th style="width:9%">Prix unitaire</th>
                    <th style="width:8%">Remise art.</th>
                    <th style="width:9%">Prix net</th>
                    <th style="width:12%">Montant net</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entree->articles as $index => $article)
                    @php
                        $quantiteTotal  = $article->pivot->quantite_total;
                        $prixUnitaire   = $article->pivot->prix_unitaire;
                        $remise         = $article->pivot->remise ?? 0;
                        $prixNet        = $prixUnitaire * (1 - $remise / 100);
                        $montantBrut    = $quantiteTotal * $prixUnitaire;
                        $montantNet     = $quantiteTotal * $prixNet;

                        $grandTotalBrut += $montantBrut;
                        $sousTotal      += $montantNet;
                        $totalPieces    += $quantiteTotal;
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
                        <td class="text-center">
                            @if($remise > 0)
                                <span class="badge badge-warning">{{ number_format($remise, 2) }}%</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if($remise > 0)
                                <span class="text-success font-weight-bold">{{ number_format($prixNet, 2) }} DZD</span>
                            @else
                                {{ number_format($prixUnitaire, 2) }} DZD
                            @endif
                        </td>
                        <td class="text-right">
                            <strong>{{ number_format($montantNet, 2) }} DZD</strong>
                        </td>
                    </tr>
                @endforeach

                {{-- TOTAL BRUT --}}
                <tr class="table-light">
                    <td colspan="9" class="text-right font-weight-bold">TOTAL BRUT :</td>
                    <td class="text-right font-weight-bold">{{ number_format($grandTotalBrut, 2) }} DZD</td>
                </tr>

                {{-- REMISES ARTICLES (si existe) --}}
                @if($grandTotalBrut > $sousTotal)
                <tr class="table-warning">
                    <td colspan="9" class="text-right font-weight-bold text-danger">
                        <i class="fas fa-tag"></i> Remises articles :
                    </td>
                    <td class="text-right font-weight-bold text-danger">
                        - {{ number_format($grandTotalBrut - $sousTotal, 2) }} DZD
                    </td>
                </tr>

                {{-- SOUS-TOTAL après remises articles --}}
                <tr class="table-light">
                    <td colspan="9" class="text-right font-weight-bold">Sous-total après remises articles :</td>
                    <td class="text-right font-weight-bold">{{ number_format($sousTotal, 2) }} DZD</td>
                </tr>
                @endif

                {{-- REMISE GLOBALE (si existe) --}}
                @php
                    $remiseGlobale        = $entree->remise_globale ?? 0;
                    $montantRemiseGlobale = $sousTotal * ($remiseGlobale / 100);
                    $totalNetFinal        = $sousTotal - $montantRemiseGlobale;
                @endphp

                @if($remiseGlobale > 0)
                <tr style="background-color: #fff3cd;">
                    <td colspan="9" class="text-right font-weight-bold text-danger">
                        <i class="fas fa-percent"></i> Remise globale sur bon ({{ number_format($remiseGlobale, 2) }}%) :
                    </td>
                    <td class="text-right font-weight-bold text-danger">
                        - {{ number_format($montantRemiseGlobale, 2) }} DZD
                    </td>
                </tr>
                @endif

                {{-- TOTAL NET FINAL --}}
                <tr style="background-color: #d4edda;">
                    <td colspan="5" class="text-right font-weight-bold" style="font-size:1.1em;">
                        TOTAL NET À PAYER :
                    </td>
                    <td class="text-center">
                        <span class="badge badge-dark" style="font-size:1em;">{{ $totalPieces }}</span>
                    </td>
                    <td colspan="3"></td>
                    <td class="text-right">
                        <strong style="font-size:1.3em; color:#155724;">
                            {{ number_format($totalNetFinal, 2) }} DZD
                        </strong>
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
        .no-print { display: none !important; }
        body { margin: 0; padding: 15px; }
        .card { box-shadow: none !important; border: none !important; }
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        .badge {
            border: 1px solid #999;
            color: #000 !important;
            background: none !important;
        }
    }
    #bon-entree { background: white; }
</style>
@stop