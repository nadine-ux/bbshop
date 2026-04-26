@extends('adminlte::page')

@section('title', 'Commandes')

@section('content_header')
<div class="page-header">
    <div class="page-header__left">
        <div class="page-header__icon"><i class="fas fa-clipboard-list"></i></div>
        <div>
            <h1>Commandes</h1>
            <p>{{ $commandes->total() }} commande(s) au total</p>
        </div>
    </div>
    <a href="{{ route('commandes.create') }}" class="btn-add">
        <i class="fas fa-plus"></i> Nouvelle commande
    </a>
</div>
@stop

@section('content')

{{-- BARRE RECHERCHE + TRI --}}
<div class="search-section">
    <form method="GET" action="{{ route('commandes.index') }}" id="filterForm">

        {{-- Recherche texte --}}
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text"
                   name="q"
                   value="{{ request('q') }}"
                   placeholder="Rechercher par gestionnaire ou N° commande..."
                   autocomplete="off">
            @if(request('q'))
                <a href="{{ request()->fullUrlWithQuery(['q'=>null]) }}" class="clear-input">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>

        {{-- Filtres : tri + statut + gestionnaire --}}
        <div class="filters-row">

            <div class="filter-item">
                <label><i class="fas fa-sort"></i> Trier par</label>
                <select name="sort">
                    <option value="date"         {{ request('sort','date')=='date'         ? 'selected':'' }}>📅 Date</option>
                    <option value="numero"       {{ request('sort')=='numero'              ? 'selected':'' }}># Numéro</option>
                    <option value="statut"       {{ request('sort')=='statut'              ? 'selected':'' }}>🔵 Statut</option>
                    <option value="gestionnaire" {{ request('sort')=='gestionnaire'        ? 'selected':'' }}>👤 Gestionnaire</option>
                </select>
            </div>

            <div class="filter-item">
                <label><i class="fas fa-circle"></i> Statut</label>
                <select name="statut">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('statut')=='en_attente' ? 'selected':'' }}>⏳ En attente</option>
                    <option value="validee"    {{ request('statut')=='validee'    ? 'selected':'' }}>✅ Validée</option>
                    <option value="annulee"    {{ request('statut')=='annulee'    ? 'selected':'' }}>🔴 Annulée</option>
                </select>
            </div>

            @isset($gestionnaires)
            <div class="filter-item">
                <label><i class="fas fa-user"></i> Gestionnaire</label>
                <select name="gestionnaire_id">
                    <option value="">Tous</option>
                    @foreach($gestionnaires as $g)
                        <option value="{{ $g->id }}" {{ request('gestionnaire_id')==$g->id ? 'selected':'' }}>
                            {{ $g->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endisset

            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                <a href="{{ route('commandes.index') }}" class="btn-reset">
                    <i class="fas fa-redo"></i> Réinitialiser
                </a>
            </div>

        </div>
    </form>
</div>

{{-- Compteur --}}
<div class="results-info">
    <span><strong>{{ $commandes->total() }}</strong> commande(s) trouvée(s)</span>
</div>

@if(session('success'))
    <div class="alert-success-bar">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

{{-- TABLE --}}
<div class="table-card">
    <div class="table-responsive">
        <table class="cmd-table">
            <thead>
                <tr>
                    <th><i class="fas fa-calendar-alt"></i> Date</th>
                    <th><i class="fas fa-hashtag"></i> N° Commande</th>
                    <th><i class="fas fa-user-tie"></i> Gestionnaire</th>
                    <th><i class="fas fa-circle"></i> Statut</th>
                    <th class="th-action">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commandes as $commande)
                @php
                    $statut = $commande->statut ?? 'en_attente';
                @endphp
                <tr class="cmd-row" data-anim="{{ $loop->index }}">
                    <td class="td-date">
                        <span class="date-badge">
                            <i class="fas fa-calendar-day"></i>
                            {{ \Carbon\Carbon::parse($commande->created_at)->format('d.m.Y') }}
                        </span>
                    </td>
                    <td class="td-num">
                        <span class="num-pill">#{{ str_pad($commande->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td class="td-gest">
                        <div class="gest-info">
                            <div class="gest-avatar">{{ strtoupper(substr($commande->user->name ?? 'U', 0, 1)) }}</div>
                            <span>{{ $commande->user->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="td-statut">
                        @if($statut === 'validee')
                            <span class="statut-badge statut--validee">✅ Validée</span>
                        @elseif($statut === 'annulee')
                            <span class="statut-badge statut--annulee">🔴 Annulée</span>
                        @else
                            <span class="statut-badge statut--attente">⏳ En attente</span>
                        @endif
                    </td>
                    <td class="td-action">
                        <button class="btn-voir"
                                onclick="openCommande({{ $commande->id }})">
                            <i class="fas fa-eye"></i> Voir
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-clipboard"></i>
                            <p>Aucune commande trouvée</p>
                            <a href="{{ route('commandes.index') }}">Réinitialiser les filtres</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="pagination-wrap">
    {{ $commandes->appends(request()->query())->links() }}
</div>


{{-- ══════════════════════════════════
     POPUP DÉTAIL COMMANDE
══════════════════════════════════ --}}
<div id="popupCommande" class="popup-overlay" onclick="closePopupOutside(event,'popupCommande')">
    <div class="popup-box popup-commande">
        <button class="popup-close" onclick="closePopup('popupCommande')">
            <i class="fas fa-times"></i>
        </button>

        <div class="popup-cmd__header">
            <div class="popup-cmd__icon"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <h2>Détail de la commande</h2>
                <p id="popupCmdSubtitle">Chargement...</p>
            </div>
        </div>

        <div id="popupCmdBody">
            <div class="popup-loading"><div class="spinner"></div><p>Chargement...</p></div>
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
.clear-input { color: var(--muted); text-decoration: none; padding: .25rem .5rem; border-radius: 6px; transition: color .2s; }
.clear-input:hover { color: var(--red); }

.filters-row {
    display: flex; flex-wrap: nowrap; gap: .5rem; align-items: flex-end;
    padding-top: 1rem; border-top: 2px solid var(--border);
    overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none;
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
    transition: border-color .25s; cursor: pointer; max-width: 150px;
}
.filter-item select:focus { border-color: var(--orange); }
.filter-actions { display: flex; gap: .4rem; align-items: flex-end; flex-shrink: 0; }
.btn-filter {
    background: linear-gradient(135deg, var(--red), #ff4444);
    color: white; border: none; padding: .7rem 1.25rem; border-radius: 10px;
    font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: .4rem; transition: all .3s;
}
.btn-filter:hover { transform: translateY(-1px); }
.btn-reset {
    background: #6c757d; color: white; padding: .7rem 1.1rem; border-radius: 10px;
    font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: .4rem; transition: all .3s;
}
.btn-reset:hover { background: #5a6268; color: white; text-decoration: none; }

.results-info { color: var(--muted); font-size: .88rem; margin-bottom: 1rem; padding: 0 .25rem; }
.results-info strong { color: var(--text); font-size: .95rem; }

/* Alert success */
.alert-success-bar {
    background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
    border-left: 4px solid var(--green);
    color: #1b5e20; padding: .85rem 1.25rem; border-radius: 10px;
    margin-bottom: 1rem; font-weight: 600; font-size: .9rem;
    display: flex; align-items: center; gap: .5rem;
}

/* ══ TABLE CARD ════════════════════════════ */
.table-card {
    background: var(--white); border-radius: var(--radius);
    box-shadow: var(--shadow); overflow: hidden; margin-bottom: 1rem;
}

.cmd-table {
    width: 100%; border-collapse: collapse;
}
.cmd-table thead tr {
    background: linear-gradient(135deg, #f8f9fa, #f0f2f5);
    border-bottom: 2px solid var(--border);
}
.cmd-table thead th {
    padding: 1rem 1.1rem; font-size: .75rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: .06em; color: var(--muted);
    white-space: nowrap;
}
.cmd-table thead th i { color: var(--orange); margin-right: .3rem; }
.th-action { text-align: center; }

.cmd-row {
    border-bottom: 1.5px solid var(--border);
    transition: background .18s;
    animation: fadeUp .3s ease both;
}
.cmd-row:last-child { border-bottom: none; }
.cmd-row:hover { background: #fdf6f3; }

@keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
.cmd-row[data-anim="0"] { animation-delay: .02s; }
.cmd-row[data-anim="1"] { animation-delay: .06s; }
.cmd-row[data-anim="2"] { animation-delay: .10s; }
.cmd-row[data-anim="3"] { animation-delay: .14s; }
.cmd-row[data-anim="4"] { animation-delay: .18s; }

.cmd-table td { padding: .9rem 1.1rem; vertical-align: middle; font-size: .9rem; color: var(--text); }

/* Date badge */
.date-badge {
    display: inline-flex; align-items: center; gap: .35rem;
    background: #f0f4ff; color: var(--blue);
    padding: .3rem .7rem; border-radius: 8px; font-weight: 700; font-size: .82rem;
}
.date-badge i { font-size: .75rem; }

/* Numéro pill */
.num-pill {
    display: inline-block;
    background: linear-gradient(135deg, var(--orange), #ff8c42);
    color: white; padding: .28rem .75rem; border-radius: 20px;
    font-weight: 800; font-size: .82rem;
    box-shadow: 0 2px 8px rgba(255,107,53,.3);
}

/* Gestionnaire */
.gest-info { display: flex; align-items: center; gap: .55rem; }
.gest-avatar {
    width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg, var(--purple), #a855f7);
    color: white; font-weight: 800; font-size: .78rem;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 8px rgba(142,68,173,.3);
}

/* Statut badges */
.statut-badge {
    display: inline-block; padding: .28rem .7rem; border-radius: 20px;
    font-size: .75rem; font-weight: 700;
}
.statut--attente { background: #fff8e1; color: #e65100; border: 1.5px solid #ffe082; }
.statut--validee { background: #e8f5e9; color: #2e7d32; border: 1.5px solid #a5d6a7; }
.statut--annulee { background: #fdecea; color: var(--red); border: 1.5px solid #ffcdd2; }

/* Bouton voir */
.btn-voir {
    background: linear-gradient(135deg, var(--blue), #5dade2);
    color: white; border: none; padding: .5rem 1rem; border-radius: 9px;
    font-weight: 700; font-size: .82rem; cursor: pointer;
    display: inline-flex; align-items: center; gap: .35rem;
    box-shadow: 0 3px 8px rgba(52,152,219,.3); transition: all .25s;
}
.btn-voir:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(52,152,219,.35); }

/* Empty state */
.empty-state { text-align: center; padding: 3.5rem 2rem; color: var(--muted); }
.empty-state i { font-size: 3.5rem; color: #ddd; display: block; margin-bottom: 1rem; }
.empty-state p { font-size: 1.1rem; margin-bottom: .75rem; }
.empty-state a { color: var(--orange); font-weight: 700; }

/* Pagination */
.pagination-wrap { display: flex; justify-content: center; padding: 1.5rem 0; }

/* ══ POPUP ══════════════════════════════════ */
.popup-overlay {
    display: none;
    position: fixed; inset: 0; background: rgba(0,0,0,.52);
    z-index: 9000; align-items: center; justify-content: center; padding: 1rem;
    backdrop-filter: blur(3px); animation: overlayIn .2s ease;
}
.popup-overlay.is-open { display: flex; }
@keyframes overlayIn { from{opacity:0} to{opacity:1} }

.popup-box {
    background: white; border-radius: 20px; position: relative;
    max-height: 90vh; overflow-y: auto; width: 100%;
    animation: boxIn .25s ease;
    box-shadow: 0 24px 64px rgba(0,0,0,.22);
}
@keyframes boxIn { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }

.popup-commande { max-width: 680px; }

.popup-close {
    position: absolute; top: 1rem; right: 1rem; z-index: 2;
    width: 34px; height: 34px; border-radius: 50%; border: none; background: #f0f0f0;
    color: var(--muted); cursor: pointer; font-size: .9rem;
    display: flex; align-items: center; justify-content: center; transition: all .2s;
}
.popup-close:hover { background: var(--red); color: white; transform: rotate(90deg); }

.popup-loading {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 3rem; color: var(--muted); gap: 1rem;
}
.spinner {
    width: 40px; height: 40px; border: 4px solid var(--border);
    border-top-color: var(--orange); border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin { to{transform:rotate(360deg)} }

/* Popup header */
.popup-cmd__header {
    display: flex; align-items: center; gap: 1rem;
    padding: 1.5rem 1.5rem 1rem; border-bottom: 2px solid var(--border);
}
.popup-cmd__icon {
    width: 48px; height: 48px; border-radius: 13px; flex-shrink: 0;
    background: linear-gradient(135deg, var(--blue), #5dade2);
    color: white; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
    box-shadow: 0 4px 12px rgba(52,152,219,.3);
}
.popup-cmd__header h2 { font-size: 1.15rem; font-weight: 800; color: var(--text); margin: 0; }
.popup-cmd__header p  { color: var(--muted); font-size: .88rem; margin: 0; }

/* Meta infos de la commande */
.cmd-meta {
    display: flex; flex-wrap: wrap; gap: .6rem; padding: 1rem 1.5rem;
    border-bottom: 2px solid var(--border);
}
.cmd-meta-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    background: var(--bg); border-radius: 9px;
    padding: .45rem .85rem; font-size: .82rem; color: var(--text); font-weight: 600;
}
.cmd-meta-chip i { color: var(--orange); }

/* Table articles dans popup */
.popup-articles-wrap { padding: 1.25rem 1.5rem; }
.popup-articles-title {
    font-size: .8rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: .06em; color: var(--muted); margin-bottom: .75rem;
    display: flex; align-items: center; gap: .4rem;
}
.popup-articles-title i { color: var(--orange); }

.articles-table { width: 100%; border-collapse: collapse; }
.articles-table thead tr {
    background: linear-gradient(135deg, #f8f9fa, #f0f2f5);
}
.articles-table thead th {
    padding: .7rem .9rem; font-size: .72rem; font-weight: 800;
    text-transform: uppercase; letter-spacing: .05em; color: var(--muted);
    text-align: left; white-space: nowrap;
}
.articles-table thead th i { color: var(--orange); margin-right: .25rem; }
.articles-table tbody tr { border-bottom: 1.5px solid var(--border); transition: background .15s; }
.articles-table tbody tr:last-child { border-bottom: none; }
.articles-table tbody tr:hover { background: #fdf6f3; }
.articles-table tbody td { padding: .75rem .9rem; font-size: .88rem; color: var(--text); vertical-align: middle; }

.td-article-name { font-weight: 700; }
.td-qty-reste  { color: var(--blue);   font-weight: 700; }
.td-qty-alerte { color: var(--yellow); font-weight: 700; }

/* Input quantité modifiable */
.qty-input {
    width: 80px; border: 2px solid var(--border); border-radius: 8px;
    padding: .4rem .5rem; font-size: .88rem; font-weight: 700;
    color: var(--orange); text-align: center; outline: none;
    transition: border-color .2s;
}
.qty-input:focus { border-color: var(--orange); }

/* Note gestionnaire */
.note-gestionnaire {
    margin: 0 1.5rem 1.25rem;
    background: #fff8e1; border: 1.5px solid #ffe082;
    border-radius: 10px; padding: .75rem 1rem;
    font-size: .83rem; color: #7d5a00; line-height: 1.5;
}
.note-gestionnaire strong { display: block; margin-bottom: .3rem; color: #5d4037; }

/* Boutons footer popup */
.popup-footer {
    display: flex; gap: .75rem; padding: 1rem 1.5rem 1.5rem;
    border-top: 2px solid var(--border); justify-content: flex-end;
}
.btn-annuler {
    background: #6c757d; color: white; border: none;
    padding: .75rem 1.5rem; border-radius: 11px; font-weight: 700;
    cursor: pointer; display: flex; align-items: center; gap: .4rem;
    transition: all .25s; font-size: .9rem;
}
.btn-annuler:hover { background: #5a6268; transform: translateY(-1px); }
.btn-valider {
    background: linear-gradient(135deg, var(--green), #2ecc71);
    color: white; border: none;
    padding: .75rem 1.75rem; border-radius: 11px; font-weight: 700;
    cursor: pointer; display: flex; align-items: center; gap: .4rem;
    box-shadow: 0 4px 12px rgba(39,174,96,.3); transition: all .25s; font-size: .9rem;
}
.btn-valider:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(39,174,96,.35); }
.btn-valider:disabled { opacity: .6; cursor: not-allowed; transform: none; }

/* ══ RESPONSIVE ════════════════════════════ */
@media(max-width: 768px) {
    .page-header { flex-wrap: wrap; gap: .75rem; }
    .cmd-table thead th:nth-child(1) { display: none; }
    .cmd-table tbody td:nth-child(1) { display: none; }
    .qty-input { width: 65px; }
}
@media(max-width: 480px) {
    .popup-footer { flex-direction: column; }
    .btn-annuler, .btn-valider { justify-content: center; }
    .articles-table thead th:nth-child(3) { display: none; }
    .articles-table tbody td:nth-child(3) { display: none; }
}
</style>
@stop

@section('js')
<script>
// ═══════════════════════
//  POPUP HELPERS
// ═══════════════════════
function openPopup(id)  { document.getElementById(id).classList.add('is-open'); }
function closePopup(id) { document.getElementById(id).classList.remove('is-open'); }
function closePopupOutside(e, id) { if (e.target.id === id) closePopup(id); }

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closePopup('popupCommande');
});

// ═══════════════════════
//  OUVRIR COMMANDE
// ═══════════════════════
let currentCommandeId = null;

function openCommande(id) {
    currentCommandeId = id;
    document.getElementById('popupCmdSubtitle').textContent = 'Chargement...';
    document.getElementById('popupCmdBody').innerHTML =
        '<div class="popup-loading"><div class="spinner"></div><p>Chargement...</p></div>';
    openPopup('popupCommande');

    fetch(`/commandes/${id}/detail-json`)
        .then(r => r.json())
        .then(data => renderCommande(data))
        .catch(() => {
            document.getElementById('popupCmdBody').innerHTML =
                '<div class="popup-loading" style="color:#e74c3c"><i class="fas fa-exclamation-triangle fa-2x"></i><p>Erreur de chargement</p></div>';
        });
}

function renderCommande(cmd) {
    document.getElementById('popupCmdSubtitle').textContent =
        `Commande #${String(cmd.id).padStart(4,'0')} — ${cmd.statut ?? 'en attente'}`;

    const isValidee = cmd.statut === 'validee';
    const isAnnulee = cmd.statut === 'annulee';
    const canEdit   = !isValidee && !isAnnulee;

    // Lignes du tableau articles
    const lignes = (cmd.lignes ?? []).map((l, i) => `
        <tr>
            <td class="td-article-name">${l.article_nom ?? l.article?.nom ?? '—'}</td>
            <td class="td-qty-reste">${l.quantite_restante ?? '—'} pcs</td>
            <td class="td-qty-alerte">${l.quantite_alerte ?? '—'} pcs</td>
            <td>
                ${canEdit
                    ? `<input type="number" class="qty-input" id="qty_${i}"
                             value="${l.quantite_demandee ?? l.quantite ?? 0}" min="0">`
                    : `<strong style="color:var(--orange)">${l.quantite_demandee ?? l.quantite ?? 0} pcs</strong>`
                }
            </td>
        </tr>`).join('');

    document.getElementById('popupCmdBody').innerHTML = `

        {{-- Meta --}}
        <div class="cmd-meta">
            <span class="cmd-meta-chip"><i class="fas fa-hashtag"></i> #${String(cmd.id).padStart(4,'0')}</span>
            <span class="cmd-meta-chip"><i class="fas fa-calendar-alt"></i> ${cmd.date ?? cmd.created_at ?? '—'}</span>
            <span class="cmd-meta-chip"><i class="fas fa-user-tie"></i> ${cmd.gestionnaire ?? cmd.user?.name ?? '—'}</span>
            <span class="cmd-meta-chip">
                ${cmd.statut === 'validee'    ? '✅ Validée'
                : cmd.statut === 'annulee'    ? '🔴 Annulée'
                :                               '⏳ En attente'}
            </span>
        </div>

        {{-- Note gestionnaire --}}
        <div class="note-gestionnaire">
            <strong><i class="fas fa-info-circle"></i> Information</strong>
            La quantité a été proposée par le gestionnaire. Vous pouvez la modifier avant de valider.
        </div>

        {{-- Table articles --}}
        <div class="popup-articles-wrap">
            <div class="popup-articles-title">
                <i class="fas fa-boxes"></i> Articles de la commande
            </div>
            <div style="overflow-x:auto">
                <table class="articles-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-box"></i> Nom article</th>
                            <th><i class="fas fa-warehouse"></i> Qté restante</th>
                            <th><i class="fas fa-exclamation-triangle"></i> Qté d'alerte</th>
                            <th><i class="fas fa-shopping-cart"></i> Qté à demander</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${lignes || `<tr><td colspan="4" style="text-align:center;color:var(--muted);padding:2rem">Aucun article</td></tr>`}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer boutons --}}
        <div class="popup-footer">
            ${isValidee || isAnnulee
                ? `<button class="btn-annuler" onclick="closePopup('popupCommande')">
                        <i class="fas fa-times"></i> Fermer
                   </button>`
                : `<button class="btn-annuler" onclick="annulerCommande(${cmd.id})">
                        <i class="fas fa-ban"></i> Annuler
                   </button>
                   <button class="btn-valider" onclick="validerCommande(${cmd.id})">
                        <i class="fas fa-check"></i> Validée
                   </button>`
            }
        </div>
    `;
}

// ═══════════════════════
//  VALIDER / ANNULER
// ═══════════════════════
function validerCommande(id) {
    // Récupérer les quantités modifiées
    const inputs = document.querySelectorAll('.qty-input');
    const quantites = Array.from(inputs).map((inp, i) => ({
        index: i,
        quantite: parseInt(inp.value) || 0
    }));

    if (!confirm('Valider cette commande ?')) return;

    fetch(`/commandes/${id}/valider`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
        },
        body: JSON.stringify({ quantites })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closePopup('popupCommande');
            showToast('✅ Commande validée avec succès !', 'green');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast('❌ Erreur : ' + (data.message ?? 'Inconnue'), 'red');
        }
    })
    .catch(() => showToast('❌ Erreur réseau', 'red'));
}

function annulerCommande(id) {
    if (!confirm('Annuler cette commande ? Cette action est irréversible.')) return;

    fetch(`/commandes/${id}/annuler`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closePopup('popupCommande');
            showToast('🔴 Commande annulée.', 'red');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast('❌ Erreur : ' + (data.message ?? 'Inconnue'), 'red');
        }
    })
    .catch(() => showToast('❌ Erreur réseau', 'red'));
}

// ═══════════════════════
//  TOAST
// ═══════════════════════
function showToast(msg, color) {
    const t = document.createElement('div');
    t.textContent = msg;
    t.style.cssText = `
        position:fixed; bottom:24px; right:24px; z-index:99999;
        background:${color === 'green' ? '#27ae60' : '#e74c3c'};
        color:white; padding:.85rem 1.5rem; border-radius:12px;
        font-weight:700; font-size:.9rem; box-shadow:0 8px 24px rgba(0,0,0,.2);
        animation: toastIn .3s ease;
    `;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}
</script>
<style>
@keyframes toastIn { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
</style>
@stop