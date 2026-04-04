@extends('adminlte::page')

@section('title','Entrées')

@section('content_header')
    <div style="display:flex;align-items:center;justify-content:space-between;">
        <div>
            <h1 style="font-size:20px;font-weight:500;margin:0;">Entrées de stock</h1>
            <small class="text-muted">Gestion des bons de réception</small>
        </div>
        <a href="{{ route('entrees.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> Nouvelle entrée
        </a>
    </div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

{{-- ===== FILTRES ===== --}}
<div class="card card-outline card-primary shadow-sm mb-3">
    <div class="card-header py-2 px-3" style="background:#f8f9fa;border-bottom:1px solid #e9ecef;">
        <span style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.6px;color:#6c757d;">
            <i class="fas fa-filter mr-1"></i> Filtres & recherche
        </span>
    </div>
    <div class="card-body py-3 px-3">
        <form method="GET" action="{{ route('entrees.index') }}">
            <div class="row align-items-end g-2">

                {{-- Fournisseur (input texte) --}}
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-building fa-xs mr-1"></i>Fournisseur
                    </label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted" style="font-size:11px;"></i>
                            </span>
                        </div>
                        <input type="text"
                               name="fournisseur"
                               class="form-control form-control-sm"
                               placeholder="Rechercher un fournisseur..."
                               value="{{ request('fournisseur') }}"
                               autocomplete="off">
                    </div>
                </div>

                {{-- Date exacte --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-calendar-day fa-xs mr-1"></i>Date exacte
                    </label>
                    <input type="date"
                           name="date"
                           class="form-control form-control-sm"
                           value="{{ request('date') }}">
                </div>

                {{-- Mois --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-calendar fa-xs mr-1"></i>Mois
                    </label>
                    <select name="mois" class="form-control form-control-sm">
                        <option value="">— Tous —</option>
                        @foreach([1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',
                                  7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre']
                                 as $num => $nom)
                            <option value="{{ $num }}" {{ request('mois') == $num ? 'selected' : '' }}>
                                {{ $nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Année --}}
                <div class="col-6 col-sm-4 col-md-1">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-calendar-alt fa-xs mr-1"></i>Année
                    </label>
                    <select name="annee" class="form-control form-control-sm">
                        <option value="">— Toutes —</option>
                        @foreach($annees as $annee)
                            <option value="{{ $annee }}" {{ request('annee') == $annee ? 'selected' : '' }}>
                                {{ $annee }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Trier par --}}
                <div class="col-6 col-sm-4 col-md-2">
                    <label class="small font-weight-bold text-muted mb-1">
                        <i class="fas fa-sort fa-xs mr-1"></i>Trier par
                    </label>
                    <select name="sort" class="form-control form-control-sm">
                        <option value="date_reception" {{ request('sort','date_reception')==='date_reception' ? 'selected':'' }}>
                            Date réception
                        </option>
                        <option value="id" {{ request('sort')==='id' ? 'selected':'' }}>
                            N° Bon
                        </option>
                        <option value="fournisseur" {{ request('sort')==='fournisseur' ? 'selected':'' }}>
                            Fournisseur
                        </option>
                    </select>
                </div>

                {{-- Direction --}}
                <div class="col-6 col-sm-4 col-md-1">
                    <label class="small font-weight-bold text-muted mb-1">&nbsp;</label>
                    <select name="direction" class="form-control form-control-sm">
                        <option value="desc" {{ request('direction','desc')==='desc' ? 'selected':'' }}>↓ Desc</option>
                        <option value="asc"  {{ request('direction')==='asc'  ? 'selected':'' }}>↑ Asc</option>
                    </select>
                </div>

                {{-- Boutons --}}
                <div class="col-12 col-md-1 d-flex gap-1" style="gap:6px;">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('entrees.index') }}" class="btn btn-secondary btn-sm flex-fill">
                        <i class="fas fa-times"></i>
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- ===== COMPTEUR RÉSULTATS ===== --}}
<div class="d-flex align-items-center mb-2">
    <small class="text-muted">
        <strong class="text-dark">{{ $entrees->total() }}</strong> entrée(s) trouvée(s)
        @if(request()->hasAny(['date','mois','annee','fournisseur']))
            &mdash; <a href="{{ route('entrees.index') }}" class="text-danger" style="font-size:11px;">
                <i class="fas fa-times-circle"></i> Effacer les filtres
            </a>
        @endif
    </small>
</div>

{{-- ===== TABLEAU ===== --}}
<div class="card shadow-sm" style="border-radius:10px;overflow:hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:13px;">
                <thead style="background:#f8f9fa;border-bottom:2px solid #dee2e6;">
                    <tr>
                        <th style="width:90px;padding:10px 14px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;">N° Bon</th>
                        <th style="width:130px;padding:10px 14px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;">Date réception</th>
                        <th style="padding:10px 14px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;">Fournisseur</th>
                        <th style="width:130px;padding:10px 14px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;">Articles reçus</th>
                        <th style="width:150px;padding:10px 14px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;text-align:right;">Montant total</th>
                        <th style="padding:10px 14px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;">Commentaire</th>
                        <th style="width:110px;padding:10px 14px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#6c757d;text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($entrees as $entree)
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        {{-- N° Bon --}}
                        <td style="padding:12px 14px;">
                            <span style="font-weight:600;color:#1a56db;font-size:12px;">#{{ $entree->id }}</span>
                        </td>

                        {{-- Date --}}
                        <td style="padding:12px 14px;color:#495057;">
                            <i class="fas fa-calendar-alt text-muted mr-1" style="font-size:11px;"></i>
                            {{ \Carbon\Carbon::parse($entree->date_reception)->format('d/m/Y') }}
                        </td>

                        {{-- Fournisseur --}}
                        <td style="padding:12px 14px;">
                            @if($entree->fournisseur)
                                <div class="d-flex align-items-center">
                                    <div style="width:28px;height:28px;border-radius:50%;background:#e8f0fe;color:#1a56db;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:600;flex-shrink:0;margin-right:8px;">
                                        {{ strtoupper(substr($entree->fournisseur->nom, 0, 2)) }}
                                    </div>
                                    <span style="font-weight:500;">{{ $entree->fournisseur->nom }}</span>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Articles --}}
                        <td style="padding:12px 14px;">
                            @if($entree->articles->isNotEmpty())
                                <button type="button"
                                        class="btn btn-sm"
                                        style="background:#e8f5e9;color:#2e7d32;border:none;border-radius:20px;padding:3px 12px;font-size:11px;font-weight:600;"
                                        data-toggle="modal"
                                        data-target="#articlesModal{{ $entree->id }}">
                                    <i class="fas fa-boxes mr-1" style="font-size:10px;"></i>
                                    {{ $entree->articles->count() }} article(s)
                                </button>

                                {{-- Modal articles --}}
                                <div class="modal fade" id="articlesModal{{ $entree->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content" style="border-radius:12px;overflow:hidden;border:none;box-shadow:0 10px 40px rgba(0,0,0,.15);">
                                            <div class="modal-header" style="background:#1a56db;border:none;padding:14px 20px;">
                                                <h5 class="modal-title text-white" style="font-size:14px;font-weight:600;">
                                                    <i class="fas fa-boxes mr-2"></i>
                                                    Articles — Bon #{{ $entree->id }}
                                                </h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" style="opacity:1;">&times;</button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <table class="table table-sm mb-0" style="font-size:13px;">
                                                    <thead style="background:#f8f9fa;">
                                                        <tr>
                                                            <th style="padding:10px 16px;font-size:11px;text-transform:uppercase;color:#6c757d;">Article</th>
                                                            <th class="text-center" style="padding:10px;font-size:11px;text-transform:uppercase;color:#6c757d;">Cartons</th>
                                                            <th class="text-center" style="padding:10px;font-size:11px;text-transform:uppercase;color:#6c757d;">Pièces</th>
                                                            <th class="text-center" style="padding:10px;font-size:11px;text-transform:uppercase;color:#6c757d;">Total</th>
                                                            <th class="text-right" style="padding:10px;font-size:11px;text-transform:uppercase;color:#6c757d;">P.U</th>
                                                            <th class="text-right" style="padding:10px 16px;font-size:11px;text-transform:uppercase;color:#6c757d;">Montant</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($entree->articles as $article)
                                                            @php $montant = $article->pivot->quantite_total * $article->pivot->prix_unitaire; @endphp
                                                            <tr>
                                                                <td style="padding:10px 16px;font-weight:500;">{{ $article->nom }}</td>
                                                                <td class="text-center" style="padding:10px;">{{ $article->pivot->quantite_cartons }}</td>
                                                                <td class="text-center" style="padding:10px;">{{ $article->pivot->quantite_pieces }}</td>
                                                                <td class="text-center" style="padding:10px;">
                                                                    <span style="background:#e8f0fe;color:#1a56db;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:600;">
                                                                        {{ $article->pivot->quantite_total }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-right" style="padding:10px;">{{ number_format($article->pivot->prix_unitaire, 2) }} DZD</td>
                                                                <td class="text-right" style="padding:10px 16px;font-weight:600;">{{ number_format($montant, 2) }} DZD</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <em class="text-muted" style="font-size:12px;">Aucun article</em>
                            @endif
                        </td>

                        {{-- Montant total --}}
                        <td style="padding:12px 14px;text-align:right;">
                            @php
                                $montantTotal = $entree->articles->sum(fn($a) =>
                                    $a->pivot->quantite_total * $a->pivot->prix_unitaire
                                );
                            @endphp
                            <strong style="font-size:13px;color:#212529;">{{ number_format($montantTotal, 2) }} DZD</strong>
                        </td>

                        {{-- Commentaire --}}
                        <td style="padding:12px 14px;color:#6c757d;font-size:12px;">
                            {{ Str::limit($entree->commentaire, 30) ?? '—' }}
                        </td>

                        {{-- Actions --}}
                        <td style="padding:12px 14px;text-align:center;">
                            <div class="d-flex justify-content-center" style="gap:5px;">
                                <a href="{{ route('entrees.show', $entree) }}"
                                   class="btn btn-sm"
                                   style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;background:#ebf8ff;border:1px solid #bee3f8;color:#2b6cb0;border-radius:6px;"
                                   title="Voir">
                                    <i class="fas fa-eye" style="font-size:11px;"></i>
                                </a>
                                <a href="{{ route('entrees.edit', $entree) }}"
                                   class="btn btn-sm"
                                   style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;background:#fffbeb;border:1px solid #fbd38d;color:#b7791f;border-radius:6px;"
                                   title="Modifier">
                                    <i class="fas fa-edit" style="font-size:11px;"></i>
                                </a>
                                <form action="{{ route('entrees.destroy', $entree) }}"
                                      method="POST"
                                      style="display:inline;"
                                      onsubmit="return confirm('Voulez-vous vraiment supprimer cette entrée ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;background:#fff5f5;border:1px solid #fed7d7;color:#c53030;border-radius:6px;cursor:pointer;"
                                            title="Supprimer">
                                        <i class="fas fa-trash" style="font-size:11px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:3rem 1rem;color:#adb5bd;">
                            <i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.4;"></i>
                            Aucune entrée enregistrée
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($entrees->hasPages())
        <div class="d-flex align-items-center justify-content-between px-3 py-2" style="border-top:1px solid #f0f0f0;">
            <small class="text-muted">
                Page {{ $entrees->currentPage() }} sur {{ $entrees->lastPage() }} — {{ $entrees->total() }} résultats
            </small>
            {{ $entrees->links() }}
        </div>
        @endif
    </div>
</div>

@stop