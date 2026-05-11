@extends('adminlte::page')

@section('title', 'Inventaire Global')

@section('content_header')
@stop

@section('content')
<div class="container-fluid py-3">

    {{-- TOPBAR --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-500 d-flex align-items-center gap-2">
            <i class="ti ti-package" style="font-size:22px;color:var(--color-text-secondary)"></i>
            Inventaire global
        </h4>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('inventaire.mouvements') }}" class="btn-inv">
                <i class="ti ti-chart-bar"></i> Mouvements
            </a>
            <a href="{{ route('inventaire.valorisation') }}" class="btn-inv">
                <i class="ti ti-coin"></i> Valorisation
            </a>
            <a href="{{ route('inventaire.stock-critique') }}" class="btn-inv btn-inv-danger">
                <i class="ti ti-alert-triangle"></i> Alertes stock
            </a>
            <a href="{{ route('inventaire.print', request()->all()) }}" class="btn-inv" target="_blank">
                <i class="ti ti-printer"></i> Imprimer
            </a>
        </div>
    </div>

    {{-- STATS --}}
    <div class="inv-stats mb-4">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total articles</div>
                <div class="stat-value">{{ $stats['total_articles'] }}</div>
            </div>
            <div class="stat-icon icon-blue"><i class="ti ti-package"></i></div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Stock critique</div>
                <div class="stat-value">{{ $stats['stock_critique'] }}</div>
            </div>
            <div class="stat-icon icon-red"><i class="ti ti-alert-triangle"></i></div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Valeur stock</div>
                <div class="stat-value" style="font-size:17px">{{ number_format($stats['valeur_stock'], 0, ',', ' ') }} DA</div>
            </div>
            <div class="stat-icon icon-green"><i class="ti ti-coin"></i></div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Articles épuisés</div>
                <div class="stat-value">{{ $stats['articles_epuises'] }}</div>
            </div>
            <div class="stat-icon icon-amber"><i class="ti ti-inbox-off"></i></div>
        </div>
    </div>

    {{-- FILTRES --}}
    <div class="inv-card mb-3">
        <form method="GET" action="{{ route('inventaire.index') }}" class="filters-grid">
            <div class="field">
                <label><i class="ti ti-search"></i> Recherche</label>
                <input type="text" name="recherche" placeholder="Nom ou code-barres..." value="{{ request('recherche') }}">
            </div>
            <div class="field">
                <label><i class="ti ti-folder"></i> Catégorie</label>
                <select name="categorie_id">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('categorie_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label><i class="ti ti-filter"></i> État stock</label>
                <select name="stock_critique">
                    <option value="">Tous les états</option>
                    <option value="1" {{ request('stock_critique') == '1' ? 'selected' : '' }}>Stock critique</option>
                </select>
            </div>
            <div class="field">
                <label style="opacity:0">.</label>
                <button type="submit" class="btn-filter">
                    <i class="ti ti-search"></i> Filtrer
                </button>
            </div>
        </form>
    </div>

    {{-- TABLEAU --}}
    <div class="inv-card">
        <div class="table-topbar">
            <span class="table-title">Articles en stock</span>
            <span class="table-count">{{ $articles->total() }} articles</span>
        </div>
        <div class="table-responsive">
            <table class="inv-table">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Catégorie</th>
                        <th>Stock</th>
                        <th>Min.</th>
                        <th>Prix achat</th>
                        <th>Valeur</th>
                        <th>État</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($articles as $article)
                    @php
                        $pct = $article->quantite_minimale > 0
                            ? min(100, round($article->stock / ($article->quantite_minimale * 3) * 100))
                            : ($article->stock > 0 ? 100 : 0);
                        $fillClass = $article->stock == 0 ? 'fill-danger' : ($article->stock_critique ? 'fill-warn' : 'fill-ok');
                    @endphp
                    <tr class="{{ $article->stock_critique && $article->stock > 0 ? 'row-warn' : '' }}">
                        <td>
                            <div class="article-cell">
                                <div class="article-photo">
                                    @if($article->photo)
                                        <img src="{{ Storage::url($article->photo) }}" alt="">
                                    @else
                                        <i class="ti ti-package"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="article-name">{{ $article->nom }}</div>
                                    <div class="article-code">{{ $article->code_barres ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-info-pill">
                                <i class="ti ti-folder"></i>
                                {{ $article->category->nom ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="stock-cell">
                                <span class="stock-num">{{ $article->stock }}</span>
                                <div class="stock-track">
                                    <div class="stock-fill {{ $fillClass }}" style="width:{{ $pct }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted-sm">{{ $article->quantite_minimale }}</td>
                        <td class="price-cell">{{ number_format($article->prix_achat, 2, ',', ' ') }} DA</td>
                        <td class="price-cell fw-500">{{ number_format($article->stock * $article->prix_achat, 0, ',', ' ') }} DA</td>
                        <td>
                            @if($article->stock == 0)
                                <span class="badge-pill badge-danger-pill"><i class="ti ti-inbox-off"></i> Épuisé</span>
                            @elseif($article->stock_critique)
                                <span class="badge-pill badge-warn-pill"><i class="ti ti-alert-triangle"></i> Critique</span>
                            @else
                                <span class="badge-pill badge-ok-pill"><i class="ti ti-check"></i> OK</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('inventaire.show', $article) }}" class="btn-detail">
                                <i class="ti ti-eye"></i> Détails
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <i class="ti ti-package" style="font-size:32px;color:var(--color-text-secondary)"></i>
                            <p>Aucun article trouvé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($articles->hasPages())
        <div class="table-footer">
            {{ $articles->links() }}
        </div>
        @endif
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
/* Layout */
.inv-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.inv-card { background: #fff; border: 0.5px solid #e0e0e0; border-radius: 12px; overflow: hidden; }

/* Stat cards */
.stat-card { background: #fff; border: 0.5px solid #e0e0e0; border-radius: 12px; padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; }
.stat-label { font-size: 12px; color: #888; margin-bottom: 4px; }
.stat-value { font-size: 22px; font-weight: 500; color: #1a1a1a; }
.stat-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.icon-blue { background: #e6f1fb; color: #185FA5; }
.icon-red { background: #fcebeb; color: #A32D2D; }
.icon-green { background: #eaf3de; color: #3B6D11; }
.icon-amber { background: #faeeda; color: #854F0B; }

/* Buttons */
.btn-inv { display: inline-flex; align-items: center; gap: 6px; padding: 7px 13px; border-radius: 8px; border: 0.5px solid #d0d0d0; font-size: 13px; color: #333; text-decoration: none; background: #fff; transition: background .15s; }
.btn-inv:hover { background: #f5f5f5; color: #333; text-decoration: none; }
.btn-inv-danger { border-color: #f09595; color: #A32D2D; }
.btn-inv-danger:hover { background: #fcebeb; }

/* Filters */
.filters-grid { display: grid; grid-template-columns: 2fr 1.5fr 1.5fr auto; gap: 10px; align-items: end; padding: 1rem 1.25rem; }
.field label { font-size: 12px; color: #666; margin-bottom: 5px; display: flex; align-items: center; gap: 4px; }
.field input, .field select { width: 100%; height: 34px; border: 0.5px solid #d0d0d0; border-radius: 8px; padding: 0 10px; font-size: 13px; background: #fff; color: #333; }
.field input:focus, .field select:focus { outline: none; border-color: #378ADD; box-shadow: 0 0 0 2px rgba(55,138,221,.15); }
.btn-filter { width: 100%; height: 34px; background: #185FA5; color: #e6f1fb; border: none; border-radius: 8px; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; }
.btn-filter:hover { background: #0C447C; }

/* Table */
.table-topbar { display: flex; justify-content: space-between; align-items: center; padding: .875rem 1.25rem; border-bottom: 0.5px solid #e8e8e8; }
.table-title { font-size: 14px; font-weight: 500; color: #1a1a1a; }
.table-count { font-size: 12px; color: #888; background: #f5f5f5; padding: 3px 10px; border-radius: 20px; border: 0.5px solid #e0e0e0; }
.inv-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.inv-table thead tr { background: #fafafa; }
.inv-table thead th { padding: 9px 14px; text-align: left; font-weight: 500; font-size: 12px; color: #888; border-bottom: 0.5px solid #e8e8e8; white-space: nowrap; }
.inv-table tbody tr { border-bottom: 0.5px solid #f0f0f0; transition: background .1s; }
.inv-table tbody tr:hover { background: #fafafa; }
.inv-table tbody tr:last-child { border-bottom: none; }
.inv-table tbody td { padding: 10px 14px; vertical-align: middle; color: #1a1a1a; }
.row-warn { background: rgba(186,117,23,.04); }

/* Article cell */
.article-cell { display: flex; align-items: center; gap: 10px; }
.article-photo { width: 38px; height: 38px; border-radius: 8px; background: #f5f5f5; border: 0.5px solid #e0e0e0; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #aaa; flex-shrink: 0; overflow: hidden; }
.article-photo img { width: 100%; height: 100%; object-fit: cover; }
.article-name { font-weight: 500; font-size: 13px; color: #1a1a1a; }
.article-code { font-size: 11px; color: #999; font-family: monospace; margin-top: 1px; }

/* Badges */
.badge-info-pill { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 11px; background: #e6f1fb; color: #0C447C; border: 0.5px solid #B5D4F4; }
.badge-pill { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 11px; }
.badge-ok-pill { background: #eaf3de; color: #3B6D11; border: 0.5px solid #C0DD97; }
.badge-warn-pill { background: #faeeda; color: #854F0B; border: 0.5px solid #FAC775; }
.badge-danger-pill { background: #fcebeb; color: #A32D2D; border: 0.5px solid #F7C1C1; }

/* Stock bar */
.stock-cell { display: flex; flex-direction: column; gap: 3px; }
.stock-num { font-weight: 500; font-size: 14px; }
.stock-track { width: 70px; height: 4px; background: #ebebeb; border-radius: 2px; overflow: hidden; }
.stock-fill { height: 100%; border-radius: 2px; }
.fill-ok { background: #639922; }
.fill-warn { background: #BA7517; }
.fill-danger { background: #A32D2D; }

/* Misc */
.price-cell { font-size: 13px; color: #1a1a1a; }
.text-muted-sm { font-size: 13px; color: #999; }
.fw-500 { font-weight: 500; }
.btn-detail { display: inline-flex; align-items: center; gap: 5px; padding: 5px 10px; border-radius: 8px; border: 0.5px solid #d0d0d0; background: #fff; color: #333; font-size: 12px; text-decoration: none; }
.btn-detail:hover { background: #f5f5f5; color: #333; text-decoration: none; }
.empty-state { text-align: center; padding: 3rem; color: #aaa; }
.empty-state p { margin-top: .5rem; font-size: 14px; }
.table-footer { padding: .875rem 1.25rem; border-top: 0.5px solid #e8e8e8; }

@media (max-width: 768px) {
    .inv-stats { grid-template-columns: repeat(2, 1fr); }
    .filters-grid { grid-template-columns: 1fr; }
}
</style>
@stop