@extends('adminlte::page')

@section('title', 'Articles')

@section('content_header')
<div class="page-header">
    <div class="page-header__left">
        <div class="page-header__icon"><i class="fas fa-boxes"></i></div>
        <div>
            <h1>Articles</h1>
            <p>{{ $articles->total() }} article(s) en stock</p>
        </div>
    </div>
    <a href="{{ route('articles.create') }}" class="btn-add">
        <i class="fas fa-plus"></i> Nouvel article
    </a>
</div>
@stop

@section('content')

{{-- ① BARRE DE RECHERCHE TEXTE --}}
<div class="search-section">
    <form method="GET" action="{{ route('articles.index') }}" id="filterForm">

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text"
                   name="nom"
                   value="{{ request('nom') }}"
                   placeholder="Rechercher un article par nom..."
                   autocomplete="off">
            @if(request('nom'))
                <a href="{{ request()->fullUrlWithQuery(['nom'=>null]) }}" class="clear-input">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>

        {{-- ② BARRE DE SCAN CODE-BARRES --}}
        <div class="scan-box">
            <div class="scan-input-row">
                <i class="fas fa-barcode"></i>
                <input type="text"
                       name="code_barres"
                       id="barcodeInput"
                       value="{{ request('code_barres') }}"
                       placeholder="Scanner ou saisir un code-barres..."
                       autocomplete="off">
                <button type="button" id="btnCamera" class="btn-camera">
                    <i class="fas fa-camera"></i> Caméra
                </button>
                @if(request('code_barres'))
                    <a href="{{ request()->fullUrlWithQuery(['code_barres'=>null]) }}" class="clear-input">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
            {{-- Fenêtre caméra --}}
            <div id="cameraBox" class="camera-box d-none">
                <div class="camera-box__header">
                    <span><i class="fas fa-camera"></i> Scanner en cours...</span>
                    <button type="button" id="btnCameraClose"><i class="fas fa-times"></i></button>
                </div>
                <video id="cameraVideo" autoplay playsinline muted></video>
                <div class="camera-frame"><div class="camera-line"></div></div>
                <p class="camera-hint">Pointez vers le code-barres</p>
                <div id="cameraStatus">Initialisation...</div>
            </div>
        </div>

        {{-- ③ FILTRES : MARQUE / FOURNISSEUR / ÉTAT STOCK --}}
        <div class="filters-row">

            <div class="filter-item">
                <label><i class="fas fa-certificate"></i> Marque</label>
                <select name="marque_id">
                    <option value="">Toutes les marques</option>
                    @isset($marques)
                        @foreach($marques as $m)
                            <option value="{{ $m->id }}" {{ request('marque_id')==$m->id ? 'selected' : '' }}>
                                {{ $m->nom }}
                            </option>
                        @endforeach
                    @endisset
                </select>
            </div>

            <div class="filter-item">
                <label><i class="fas fa-truck"></i> Fournisseur</label>
                <select name="fournisseur_id">
                    <option value="">Tous les fournisseurs</option>
                    @isset($fournisseurs)
                        @foreach($fournisseurs as $f)
                            <option value="{{ $f->id }}" {{ request('fournisseur_id')==$f->id ? 'selected' : '' }}>
                                {{ $f->nom }}
                            </option>
                        @endforeach
                    @endisset
                </select>
            </div>

            <div class="filter-item">
                <label><i class="fas fa-warehouse"></i> État du stock</label>
                <select name="etat_stock">
                    <option value="">Tous les états</option>
                    <option value="normal"  {{ request('etat_stock')=='normal'  ? 'selected' : '' }}>✅ Normal</option>
                    <option value="faible"  {{ request('etat_stock')=='faible'  ? 'selected' : '' }}>⚠️ Stock faible</option>
                    <option value="rupture" {{ request('etat_stock')=='rupture' ? 'selected' : '' }}>🔴 Rupture</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                <a href="{{ route('articles.index') }}" class="btn-reset">
                    <i class="fas fa-redo"></i> Réinitialiser
                </a>
            </div>

        </div>

    </form>
</div>

{{-- Compteur résultats --}}
<div class="results-info">
    <span><strong>{{ $articles->total() }}</strong> article(s) trouvé(s)</span>
</div>

{{-- ④ GRILLE DES CARDS --}}
<div class="cards-grid">
    @forelse($articles as $article)
    @php
        $s = $article->stock == 0 ? 'rupture'
           : ($article->stock <= $article->quantite_minimale ? 'faible' : 'normal');
    @endphp

    <div class="article-card">

        {{-- IMAGE → clique = popup détail --}}
        <div class="card-img-wrap" onclick="openDetail({{ $article->id }})">
            @if($article->photo)
                <img src="{{ asset('storage/'.$article->photo) }}" alt="{{ $article->nom }}">
            @else
                <div class="card-img-placeholder">
                    <i class="fas fa-box-open"></i>
                </div>
            @endif

            {{-- Badge état stock --}}
            <span class="stock-badge stock-badge--{{ $s }}">
                @if($s === 'rupture') 🔴 Rupture
                @elseif($s === 'faible') ⚠️ Faible
                @else ✅ {{ $article->stock }}
                @endif
            </span>

            {{-- Hover overlay --}}
            <div class="card-img-hover">
                <i class="fas fa-eye"></i>
                <span>Voir détails</span>
            </div>
        </div>

        {{-- Nom + meta --}}
        <div class="card-info">
            <h3>{{ $article->nom }}</h3>
            @if($article->marque)
                <span class="card-meta"><i class="fas fa-certificate"></i> {{ $article->marque->nom }}</span>
            @endif
            @if($article->category)
                <span class="card-meta"><i class="fas fa-tag"></i> {{ $article->category->nom }}</span>
            @endif
            @if($article->prix_achat)
                <span class="card-price">{{ number_format($article->prix_achat, 2) }} DZD</span>
            @endif
        </div>

        {{-- ⑤ 3 ICÔNES D'ACTION --}}
        <div class="card-actions">

            {{-- Historique (entrées + sorties) --}}
            <button class="card-btn card-btn--history"
                    onclick="openHistory({{ $article->id }}, '{{ addslashes($article->nom) }}')"
                    title="Historique des mouvements">
                <i class="fas fa-history"></i>
            </button>

            {{-- Modifier --}}
            <a href="{{ route('articles.edit', $article) }}"
               class="card-btn card-btn--edit"
               title="Modifier l'article">
                <i class="fas fa-pen"></i>
            </a>

            {{-- Supprimer --}}
            <form action="{{ route('articles.destroy', $article) }}" method="POST"
                  onsubmit="return confirm('Supprimer {{ addslashes($article->nom) }} ?')">
                @csrf @method('DELETE')
                <button type="submit" class="card-btn card-btn--delete" title="Supprimer">
                    <i class="fas fa-trash"></i>
                </button>
            </form>

        </div>
    </div>

    @empty
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <p>Aucun article trouvé</p>
        <a href="{{ route('articles.index') }}">Réinitialiser les filtres</a>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
<div class="pagination-wrap">
    {{ $articles->appends(request()->query())->links() }}
</div>


{{-- ══════════════════════════════════
     POPUP DÉTAIL ARTICLE
══════════════════════════════════ --}}
<div id="popupDetail" class="popup-overlay" onclick="closePopupOutside(event, 'popupDetail')">
    <div class="popup-box popup-detail">
        <button class="popup-close" onclick="closePopup('popupDetail')">
            <i class="fas fa-times"></i>
        </button>
        <div id="popupDetailBody">
            <div class="popup-loading"><div class="spinner"></div><p>Chargement...</p></div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════
     POPUP HISTORIQUE MOUVEMENTS
══════════════════════════════════ --}}
<div id="popupHistory" class="popup-overlay" onclick="closePopupOutside(event, 'popupHistory')">
    <div class="popup-box popup-history">
        <button class="popup-close" onclick="closePopup('popupHistory')">
            <i class="fas fa-times"></i>
        </button>

        <div class="popup-history__header">
            <div class="popup-history__icon"><i class="fas fa-history"></i></div>
            <div>
                <h2>Historique des mouvements</h2>
                <p id="historyArticleName"></p>
            </div>
        </div>

        {{-- Onglets --}}
        <div class="history-tabs">
            <button class="htab active" data-filter="all"    onclick="switchTab(this,'all')">
                <i class="fas fa-list"></i> Tous
            </button>
            <button class="htab" data-filter="entree" onclick="switchTab(this,'entree')">
                <i class="fas fa-arrow-down"></i> Entrées
            </button>
            <button class="htab" data-filter="sortie" onclick="switchTab(this,'sortie')">
                <i class="fas fa-arrow-up"></i> Sorties
            </button>
        </div>

        <div id="historyBody">
            <div class="popup-loading"><div class="spinner"></div></div>
        </div>
    </div>
</div>

@stop

@section('css')
<style>
/* ══ VARIABLES ══════════════════════════════ */
:root {
    --red:    #E60000;
    --orange: #FF6B35;
    --green:  #27ae60;
    --yellow: #f39c12;
    --blue:   #3498db;
    --purple: #8e44ad;
    --text:   #2c3e50;
    --muted:  #7f8c8d;
    --border: #e9ecef;
    --bg:     #f5f6fa;
    --white:  #fff;
    --radius: 14px;
    --shadow: 0 2px 14px rgba(0,0,0,.07);
}
.content-wrapper { background: var(--bg) !important; }

/* ══ PAGE HEADER ════════════════════════════ */
.page-header {
    display: flex; justify-content: space-between; align-items: center;
    background: var(--white); padding: 1.25rem 1.5rem;
    border-radius: var(--radius); box-shadow: var(--shadow);
    margin-bottom: 1.5rem;
}
.page-header__left { display: flex; align-items: center; gap: 1rem; }
.page-header__icon {
    width: 50px; height: 50px; border-radius: 13px;
    background: linear-gradient(135deg, var(--red), #ff4444);
    color: white; display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; box-shadow: 0 4px 12px rgba(230,0,0,.25);
}
.page-header h1 { font-size: 1.55rem; font-weight: 800; color: var(--text); margin: 0; }
.page-header p  { color: var(--muted); font-size: .88rem; margin: 0; }
.btn-add {
    background: linear-gradient(135deg, var(--red), #ff4444);
    color: white; padding: .75rem 1.4rem; border-radius: 12px;
    font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: .4rem;
    box-shadow: 0 4px 12px rgba(230,0,0,.25); transition: all .3s;
}
.btn-add:hover { transform: translateY(-2px); color: white; text-decoration: none; }

/* ══ SEARCH SECTION ═════════════════════════ */
.search-section {
    background: var(--white); border-radius: var(--radius);
    box-shadow: var(--shadow); padding: 1.5rem;
    margin-bottom: 1rem; display: flex; flex-direction: column; gap: 1rem;
}

/* ① Recherche texte */
.search-box {
    display: flex; align-items: center; gap: .75rem;
    border: 2px solid var(--border); border-radius: 12px;
    padding: .25rem .75rem; transition: border-color .25s; position: relative;
}
.search-box:focus-within { border-color: var(--orange); }
.search-box > i { color: var(--orange); font-size: 1.1rem; flex-shrink: 0; }
.search-box input {
    flex: 1; border: none; outline: none; background: transparent;
    font-size: 1rem; padding: .65rem .25rem; color: var(--text);
}
.clear-input {
    color: var(--muted); text-decoration: none; padding: .25rem .5rem;
    border-radius: 6px; transition: color .2s;
}
.clear-input:hover { color: var(--red); }

/* ② Scan code-barres */
.scan-box { display: flex; flex-direction: column; gap: .6rem; }
.scan-input-row {
    display: flex; align-items: center; gap: .6rem;
    border: 2px solid var(--border); border-radius: 12px;
    padding: .25rem .75rem; transition: border-color .25s;
}
.scan-input-row:focus-within { border-color: var(--green); }
.scan-input-row > i { color: var(--green); font-size: 1.1rem; flex-shrink: 0; }
.scan-input-row input {
    flex: 1; border: none; outline: none; background: transparent;
    font-size: .95rem; padding: .65rem .25rem; color: var(--text);
}
.btn-camera {
    background: linear-gradient(135deg, var(--green), #2ecc71);
    color: white; border: none; border-radius: 9px; padding: .55rem 1rem;
    font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: .4rem;
    font-size: .85rem; white-space: nowrap; transition: all .3s;
    box-shadow: 0 3px 8px rgba(39,174,96,.3);
}
.btn-camera:hover { transform: translateY(-1px); }

/* Caméra */
.camera-box {
    border-radius: 12px; overflow: hidden; border: 2px solid var(--green); background: #000;
}
.camera-box__header {
    display: flex; justify-content: space-between; align-items: center;
    padding: .55rem 1rem; background: var(--green); color: white; font-weight: 600; font-size: .88rem;
}
.camera-box__header button {
    background: rgba(255,255,255,.2); color: white; border: none;
    border-radius: 7px; padding: .25rem .65rem; cursor: pointer;
}
#cameraVideo { width: 100%; max-height: 200px; display: block; object-fit: cover; }
.camera-frame {
    position: relative; height: 0; pointer-events: none;
}
/* Frame overlay dessiné en CSS sur la vidéo */
.camera-box { position: relative; }
.camera-line {
    position: absolute; top: 40px; left: 50%; transform: translateX(-50%);
    width: 65%; height: 3px; background: #2ecc71;
    box-shadow: 0 0 8px #2ecc71; animation: scanLine 1.8s linear infinite; z-index: 10;
}
@keyframes scanLine {
    0%   { top: 40px; }
    100% { top: 200px; }
}
.camera-hint {
    position: absolute; bottom: 28px; left: 50%; transform: translateX(-50%);
    color: white; font-size: .78rem; background: rgba(0,0,0,.5);
    padding: .2rem .65rem; border-radius: 20px; white-space: nowrap; z-index: 10;
}
#cameraStatus {
    padding: .4rem 1rem; background: #111; color: #aaa; font-size: .78rem;
}

/* ③ Filtres */
/* ③ Filtres */
.filters-row {
    display: flex; flex-wrap: nowrap; gap: .5rem; align-items: flex-end;
    padding-top: 1rem; border-top: 2px solid var(--border);
    overflow-x: auto; -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}
.filters-row::-webkit-scrollbar { display: none; }
.filter-item { display: flex; flex-direction: column; gap: .3rem; flex: 0 0 auto; }
.filter-item label {
    font-size: .72rem; font-weight: 700; color: var(--muted);
    display: flex; align-items: center; gap: .3rem;
    text-transform: uppercase; letter-spacing: .04em; white-space: nowrap;
}
.filter-item label i { color: var(--orange); }
.filter-item select {
    border: 2px solid var(--border); border-radius: 10px; padding: .55rem .5rem;
    font-size: .82rem; outline: none; background: white; color: var(--text);
    transition: border-color .25s; cursor: pointer; max-width: 130px;
}
.filter-item select:focus { border-color: var(--orange); }
.filter-actions { display: flex; gap: .4rem; align-items: flex-end; flex-shrink: 0; }
.btn-filter {
    background: linear-gradient(135deg, var(--red), #ff4444);
    color: white; border: none; padding: .7rem 1.25rem; border-radius: 10px;
    font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: .4rem;
    transition: all .3s;
}
.btn-filter:hover { transform: translateY(-1px); }
.btn-reset {
    background: #6c757d; color: white; padding: .7rem 1.1rem; border-radius: 10px;
    font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: .4rem;
    transition: all .3s;
}
.btn-reset:hover { background: #5a6268; color: white; text-decoration: none; }

/* Compteur */
.results-info {
    color: var(--muted); font-size: .88rem; margin-bottom: 1rem; padding: 0 .25rem;
}
.results-info strong { color: var(--text); font-size: .95rem; }

/* ══ GRILLE CARDS ════════════════════════════ */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: 1.25rem;
}

/* ④ CARD ARTICLE */
.article-card {
    background: var(--white); border-radius: 16px;
    box-shadow: var(--shadow); overflow: hidden;
    display: flex; flex-direction: column;
    transition: transform .3s, box-shadow .3s;
    animation: fadeUp .3s ease both;
}
@keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
.article-card:hover { transform: translateY(-5px); box-shadow: 0 12px 32px rgba(255,107,53,.15); }

/* Image */
.card-img-wrap {
    position: relative; height: 185px; overflow: hidden;
    cursor: pointer; background: var(--bg);
}
.card-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform .45s; }
.article-card:hover .card-img-wrap img { transform: scale(1.07); }
.card-img-placeholder {
    width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
    font-size: 3.5rem; color: #ddd;
}

/* Badge stock */
.stock-badge {
    position: absolute; top: 9px; left: 9px;
    font-size: .7rem; font-weight: 700; padding: .22rem .6rem;
    border-radius: 20px; backdrop-filter: blur(4px);
    box-shadow: 0 2px 8px rgba(0,0,0,.2);
}
.stock-badge--normal  { background: rgba(39,174,96,.9);  color: white; }
.stock-badge--faible  { background: rgba(243,156,18,.88); color: white; }
.stock-badge--rupture { background: rgba(230,0,0,.9);    color: white; }

/* Hover overlay sur image */
.card-img-hover {
    position: absolute; inset: 0; background: rgba(0,0,0,0);
    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .4rem;
    transition: background .3s;
}
.article-card:hover .card-img-hover { background: rgba(0,0,0,.38); }
.card-img-hover i    { font-size: 1.6rem; color: white; opacity: 0; transform: translateY(6px); transition: all .3s; }
.card-img-hover span { font-size: .85rem; font-weight: 700; color: white; opacity: 0; transform: translateY(6px); transition: all .3s .05s; }
.article-card:hover .card-img-hover i,
.article-card:hover .card-img-hover span { opacity: 1; transform: translateY(0); }

/* Info */
.card-info { padding: .9rem 1rem .5rem; flex: 1; }
.card-info h3 {
    font-size: .98rem; font-weight: 800; color: var(--text); margin: 0 0 .45rem;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.card-meta {
    display: block; font-size: .78rem; color: var(--muted); margin-bottom: .2rem;
}
.card-meta i { color: var(--orange); margin-right: .25rem; }
.card-price { display: block; font-size: 1rem; font-weight: 800; color: var(--orange); margin-top: .4rem; }

/* ⑤ 3 ICÔNES D'ACTION */
.card-actions {
    display: flex; border-top: 2px solid var(--bg);
    background: #fafafa;
}
.card-btn {
    flex: 1; padding: .75rem; border: none; background: transparent;
    cursor: pointer; font-size: 1rem; transition: all .2s;
    display: flex; align-items: center; justify-content: center;
    text-decoration: none;
}
.card-btn + .card-btn { border-left: 2px solid var(--bg); }
.card-btn:hover { transform: translateY(-2px); }

.card-btn--history { color: var(--purple); }
.card-btn--history:hover { background: #f3e5f5; color: var(--purple); }
.card-btn--edit    { color: var(--yellow); }
.card-btn--edit:hover { background: #fff8e1; color: var(--yellow); text-decoration: none; }
.card-btn--delete  { color: var(--red); }
.card-btn--delete:hover { background: #fdecea; color: var(--red); }

/* Empty state */
.empty-state {
    grid-column: 1/-1; text-align: center; padding: 3.5rem 2rem;
    background: white; border-radius: var(--radius); box-shadow: var(--shadow);
    color: var(--muted);
}
.empty-state i { font-size: 3.5rem; color: #ddd; display: block; margin-bottom: 1rem; }
.empty-state p { font-size: 1.1rem; margin-bottom: .75rem; }
.empty-state a { color: var(--orange); font-weight: 700; }

/* Pagination */
.pagination-wrap { display: flex; justify-content: center; padding: 1.5rem 0; }

/* ══ POPUPS ══════════════════════════════════ */
.popup-overlay {
    display: none; /* caché par défaut */
    position: fixed; inset: 0; background: rgba(0,0,0,.52);
    z-index: 9000; align-items: center; justify-content: center; padding: 1rem;
    backdrop-filter: blur(3px); animation: overlayIn .2s ease;
}
.popup-overlay.is-open { display: flex; }
@keyframes overlayIn { from{opacity:0} to{opacity:1} }

.popup-box {
    background: white; border-radius: 20px; position: relative;
    max-height: 88vh; overflow-y: auto; width: 100%;
    animation: boxIn .25s ease;
    box-shadow: 0 24px 64px rgba(0,0,0,.22);
}
@keyframes boxIn { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }

.popup-detail  { max-width: 640px; }
.popup-history { max-width: 660px; }

.popup-close {
    position: absolute; top: 1rem; right: 1rem; z-index: 2;
    width: 34px; height: 34px; border-radius: 50%; border: none; background: #f0f0f0;
    color: var(--muted); cursor: pointer; font-size: .9rem;
    display: flex; align-items: center; justify-content: center; transition: all .2s;
}
.popup-close:hover { background: var(--red); color: white; transform: rotate(90deg); }

.popup-loading { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem; color: var(--muted); gap: 1rem; }
.spinner {
    width: 40px; height: 40px; border: 4px solid var(--border);
    border-top-color: var(--orange); border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin { to{transform:rotate(360deg)} }

/* ── POPUP DÉTAIL ──────────────────────── */
.detail-img {
    width: 100%; height: 220px; object-fit: cover;
    border-radius: 20px 20px 0 0; display: block;
}
.detail-img-ph {
    width: 100%; height: 160px; background: var(--bg);
    border-radius: 20px 20px 0 0; display: flex; align-items: center; justify-content: center;
    font-size: 5rem; color: #ddd;
}
.detail-body { padding: 1.5rem; }
.detail-name { font-size: 1.4rem; font-weight: 800; color: var(--text); margin: 0 0 .25rem; }
.detail-code { color: var(--muted); font-size: .88rem; margin-bottom: 1rem; }
.detail-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: .6rem; margin-bottom: 1rem;
}
.detail-cell { background: var(--bg); border-radius: 10px; padding: .7rem .9rem; }
.detail-cell__label { font-size: .7rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: .2rem; }
.detail-cell__val   { font-size: .92rem; font-weight: 700; color: var(--text); }
.detail-desc { background: var(--bg); border-radius: 10px; padding: .75rem .9rem; font-size: .9rem; color: var(--text); margin-bottom: 1rem; line-height: 1.5; }

/* ── POPUP HISTORIQUE ──────────────────── */
.popup-history__header {
    display: flex; align-items: center; gap: 1rem;
    padding: 1.5rem 1.5rem 1rem; border-bottom: 2px solid var(--border);
}
.popup-history__icon {
    width: 48px; height: 48px; border-radius: 13px; flex-shrink: 0;
    background: #f3e5f5; color: var(--purple);
    display: flex; align-items: center; justify-content: center; font-size: 1.4rem;
}
.popup-history__header h2 { font-size: 1.15rem; font-weight: 800; color: var(--text); margin: 0; }
.popup-history__header p  { color: var(--muted); font-size: .88rem; margin: 0; }

/* Onglets */
.history-tabs {
    display: flex; gap: .5rem; padding: 1rem 1.5rem; border-bottom: 2px solid var(--border);
}
.htab {
    flex: 1; padding: .55rem; border: 2px solid var(--border); background: white;
    border-radius: 9px; cursor: pointer; font-weight: 700; font-size: .82rem;
    display: flex; align-items: center; justify-content: center; gap: .35rem; transition: all .2s;
    color: var(--text);
}
.htab.active         { background: var(--text);   border-color: var(--text);  color: white; }
.htab[data-filter="entree"]:hover { background: var(--green); border-color: var(--green); color: white; }
.htab[data-filter="sortie"]:hover { background: var(--red);   border-color: var(--red);   color: white; }
.htab[data-filter="entree"].active { background: var(--green); border-color: var(--green); color: white; }
.htab[data-filter="sortie"].active { background: var(--red);   border-color: var(--red);   color: white; }

/* Liste mouvements */
#historyBody { padding: 1.25rem 1.5rem; max-height: 380px; overflow-y: auto; }
.mv-item {
    display: flex; align-items: center; gap: .75rem;
    padding: .75rem; border-radius: 10px; margin-bottom: .4rem;
    border: 1.5px solid var(--border); transition: background .15s;
}
.mv-item:hover { background: var(--bg); }
.mv-icon {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .95rem;
}
.mv-icon--entree { background: #e8f5e9; color: var(--green); }
.mv-icon--sortie { background: #fdecea; color: var(--red); }
.mv-info { flex: 1; }
.mv-type { font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; }
.mv-type--entree { color: var(--green); }
.mv-type--sortie { color: var(--red); }
.mv-qty  { font-size: 1.05rem; font-weight: 800; color: var(--text); }
.mv-motif { font-size: .78rem; color: var(--muted); }
.mv-date  { font-size: .76rem; color: var(--muted); font-weight: 600; white-space: nowrap; }
.history-empty { text-align: center; color: var(--muted); padding: 2rem; }

@media(max-width:768px) {
    .cards-grid { grid-template-columns: repeat(2,1fr); }
    .detail-grid { grid-template-columns: 1fr; }
    
}
@media(max-width:480px) {
    .cards-grid { grid-template-columns: 1fr; }
}
</style>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxing-js/0.21.1/zxing.min.js"></script>
<script>
// ═══════════════════════
//  CAMÉRA SCANNER
// ═══════════════════════
const btnCamera  = document.getElementById('btnCamera');
const btnCameraClose = document.getElementById('btnCameraClose');
const cameraBox  = document.getElementById('cameraBox');
const cameraVideo = document.getElementById('cameraVideo');
const cameraStatus = document.getElementById('cameraStatus');
let zxReader = null;

btnCamera.addEventListener('click', async () => {
    cameraBox.classList.remove('d-none');
    cameraStatus.textContent = 'Ouverture de la caméra...';
    try {
        zxReader = new ZXing.BrowserMultiFormatReader();
        const devices = await ZXing.BrowserCodeReader.listVideoInputDevices();
        let dId = devices[0]?.deviceId;
        const back = devices.find(d => /back|arrière|environment/i.test(d.label));
        if (back) dId = back.deviceId;
        cameraStatus.textContent = 'Pointez vers le code-barres...';
        await zxReader.decodeFromVideoDevice(dId, 'cameraVideo', (result) => {
            if (result) {
                document.getElementById('barcodeInput').value = result.getText();
                cameraStatus.textContent = '✅ Détecté : ' + result.getText();
                stopCamera();
                setTimeout(() => document.getElementById('filterForm').submit(), 500);
            }
        });
    } catch(e) {
        cameraStatus.textContent = '❌ ' + e.message;
    }
});

function stopCamera() {
    if (zxReader) { zxReader.reset(); zxReader = null; }
    cameraBox.classList.add('d-none');
}
btnCameraClose?.addEventListener('click', stopCamera);


// ═══════════════════════
//  POPUP HELPERS
// ═══════════════════════
function openPopup(id)  { document.getElementById(id).classList.add('is-open'); }
function closePopup(id) { document.getElementById(id).classList.remove('is-open'); }
function closePopupOutside(e, id) { if (e.target.id === id) closePopup(id); }

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closePopup('popupDetail');
        closePopup('popupHistory');
        stopCamera();
    }
});


// ═══════════════════════
//  POPUP DÉTAIL
// ═══════════════════════
function openDetail(id) {
    document.getElementById('popupDetailBody').innerHTML =
        '<div class="popup-loading"><div class="spinner"></div><p>Chargement...</p></div>';
    openPopup('popupDetail');

    fetch(`/articles/${id}/detail-json`)
        .then(r => r.json())
        .then(a => {
            const s = a.stock == 0 ? 'rupture' : (a.stock <= a.quantite_minimale ? 'faible' : 'normal');
            const sLabel = s === 'rupture' ? '🔴 Rupture' : s === 'faible' ? '⚠️ Faible' : '✅ Normal';
            const c = a.contenance_carton || 1;
            const cartons = Math.floor(a.stock / c);
            const reste   = a.stock % c;

            document.getElementById('popupDetailBody').innerHTML = `
                ${a.photo
                    ? `<img src="/storage/${a.photo}" class="detail-img" alt="${a.nom}">`
                    : `<div class="detail-img-ph"><i class="fas fa-box-open"></i></div>`}
                <div class="detail-body">
                    <h2 class="detail-name">${a.nom}</h2>
                    <p class="detail-code"><i class="fas fa-barcode"></i> ${a.code_barres || '—'}</p>

                    <div class="detail-grid">
                        <div class="detail-cell">
                            <div class="detail-cell__label">Stock</div>
                            <div class="detail-cell__val">${a.stock} pcs<br><small style="font-weight:500;color:#999">${cartons} cartons + ${reste} pcs</small></div>
                        </div>
                        <div class="detail-cell">
                            <div class="detail-cell__label">État</div>
                            <div class="detail-cell__val">${sLabel}</div>
                        </div>
                        <div class="detail-cell">
                            <div class="detail-cell__label">Prix d'achat</div>
                            <div class="detail-cell__val">${a.prix_achat ? parseFloat(a.prix_achat).toFixed(2)+' DZD' : '—'}</div>
                        </div>
                        <div class="detail-cell">
                            <div class="detail-cell__label">Qté minimale</div>
                            <div class="detail-cell__val">${a.quantite_minimale} pcs</div>
                        </div>
                        <div class="detail-cell">
                            <div class="detail-cell__label">Catégorie</div>
                            <div class="detail-cell__val">${a.category?.nom || '—'}</div>
                        </div>
                        <div class="detail-cell">
                            <div class="detail-cell__label">Marque</div>
                            <div class="detail-cell__val">${a.marque?.nom || '—'}</div>
                        </div>
                        <div class="detail-cell">
                            <div class="detail-cell__label">Fournisseur</div>
                            <div class="detail-cell__val">${a.fournisseur?.nom || '—'}</div>
                        </div>
                        <div class="detail-cell">
                            <div class="detail-cell__label">Péremption</div>
                            <div class="detail-cell__val">${a.date_peremption || '—'}</div>
                        </div>
                    </div>

                    ${a.description ? `<div class="detail-desc">${a.description}</div>` : ''}
                </div>`;
        })
        .catch(() => {
            document.getElementById('popupDetailBody').innerHTML =
                '<div class="popup-loading" style="color:#e74c3c"><i class="fas fa-exclamation-triangle fa-2x"></i><p>Erreur de chargement</p></div>';
        });
}


// ═══════════════════════
//  POPUP HISTORIQUE
// ═══════════════════════
let allMouvements = [];

function openHistory(id, nom) {
    document.getElementById('historyArticleName').textContent = nom;
    document.getElementById('historyBody').innerHTML =
        '<div class="popup-loading"><div class="spinner"></div></div>';
    openPopup('popupHistory');

    // Réinitialiser onglets
    document.querySelectorAll('.htab').forEach(t => t.classList.remove('active'));
    document.querySelector('.htab[data-filter="all"]').classList.add('active');

    fetch(`/articles/${id}/mouvements-json`)
        .then(r => r.json())
        .then(data => {
            allMouvements = data;
            renderHistory('all');
        })
        .catch(() => {
            document.getElementById('historyBody').innerHTML =
                '<div class="history-empty"><i class="fas fa-exclamation-triangle"></i> Erreur de chargement</div>';
        });
}

function renderHistory(filter) {
    const list = filter === 'all' ? allMouvements : allMouvements.filter(m => m.type === filter);
    if (!list.length) {
        document.getElementById('historyBody').innerHTML =
            `<div class="history-empty">
                <i class="fas fa-inbox" style="font-size:2.5rem;display:block;margin-bottom:.75rem;color:#ddd"></i>
                Aucun mouvement ${filter !== 'all' ? "de type "+filter : ''}
            </div>`;
        return;
    }
    document.getElementById('historyBody').innerHTML = list.map(m => `
        <div class="mv-item">
            <div class="mv-icon mv-icon--${m.type}">
                <i class="fas fa-arrow-${m.type === 'entree' ? 'down' : 'up'}"></i>
            </div>
            <div class="mv-info">
                <div class="mv-type mv-type--${m.type}">${m.type === 'entree' ? '↓ Entrée' : '↑ Sortie'}</div>
                <div class="mv-qty">${m.type === 'entree' ? '+' : '-'}${m.quantite} pcs
                    ${m.cartons ? `<small style="font-weight:500;color:#999">(${m.cartons} ctn + ${m.pieces} pcs)</small>` : ''}
                </div>
                <div class="mv-motif">${m.motif || '—'}</div>
            </div>
            <div class="mv-date">${m.date || '—'}</div>
        </div>`).join('');
}

function switchTab(btn, filter) {
    document.querySelectorAll('.htab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    renderHistory(filter);
}
</script>
@stop