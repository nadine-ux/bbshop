@extends('adminlte::page')

@section('title', 'Nouvel Article')

@section('content_header')
<div class="art-header">
    <a href="{{ route('articles.index') }}" class="art-back">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h1 class="art-title">Nouvel article</h1>
        <p class="art-sub">Ajout au stock • {{ now()->format('d M Y') }}</p>
    </div>
    <div class="art-header-badge">
        <span class="pulse-dot"></span> Stock
    </div>
</div>
@stop

@section('content')

<form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data" id="mainForm" novalidate>
@csrf

<div class="art-grid">

    <div class="art-col">

        <div class="art-card" data-section="info">
            <div class="art-card-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Informations de base
            </div>

            <div class="fld">
                <label class="fld-label">Nom de l'article <span class="req">*</span></label>
                <div class="fld-wrap">
                    <input type="text" name="nom"
                           class="fld-input @error('nom') has-err @enderror"
                           placeholder="Ex: Coca‑Cola 1.5L, Samsung A54…"
                           value="{{ old('nom') }}" required autocomplete="off">
                    <span class="fld-ico">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    </span>
                </div>
                @error('nom')<p class="err-msg">{{ $message }}</p>@enderror
            </div>

            <div class="fld">
                <label class="fld-label">Codes-barres <span class="req">*</span></label>
                <div id="barcodeList"></div>
                <button type="button" class="btn-add-bc" id="btnAddBarcode">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Ajouter un code-barres
                </button>
                <p class="fld-hint">Le code <strong>Principal</strong> est utilisé par défaut lors des ventes.</p>
            </div>

            <div class="fld">
                <label class="fld-label">Catégorie <span class="req">*</span></label>
                <input type="hidden" name="categorie_id" id="categorie_id" value="{{ old('categorie_id') }}">
                <div class="combo-wrap" id="comboWrap_cat">
                    <div class="combo-trigger" id="comboTrigger_cat">
                        <svg class="combo-ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
                        <input type="text" id="catSearch" class="combo-input" placeholder="Rechercher une catégorie…" autocomplete="off">
                        <button type="button" class="combo-clear d-none" id="catClear">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                        <svg class="combo-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                    <div class="combo-dropdown d-none" id="catDropdown">
                        <div class="combo-list" id="catList"></div>
                        <div class="combo-empty d-none" id="catEmpty">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Aucun résultat
                        </div>
                    </div>
                    <div class="combo-badge d-none" id="catBadge">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span id="catBadgeName"></span>
                    </div>
                </div>
                @error('categorie_id')<p class="err-msg">{{ $message }}</p>@enderror
            </div>

            <div class="fld">
                <label class="fld-label">Marque <span class="opt">optionnel</span></label>
                <input type="hidden" name="marque_id" id="marque_id" value="{{ old('marque_id') }}">
                <div class="combo-wrap" id="comboWrap_mar">
                    <div class="combo-trigger" id="comboTrigger_mar">
                        <svg class="combo-ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                        <input type="text" id="marSearch" class="combo-input" placeholder="Rechercher une marque…" autocomplete="off">
                        <button type="button" class="combo-clear d-none" id="marClear">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                        <svg class="combo-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                    <div class="combo-dropdown d-none" id="marDropdown">
                        <div class="combo-list" id="marList"></div>
                        <div class="combo-empty d-none" id="marEmpty">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Aucun résultat
                        </div>
                    </div>
                    <div class="combo-badge d-none" id="marBadge">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span id="marBadgeName"></span>
                    </div>
                </div>
                @error('marque_id')<p class="err-msg">{{ $message }}</p>@enderror
            </div>

            <div class="fld">
                <label class="fld-label">Description <span class="opt">optionnel</span></label>
                <textarea name="description"
                          class="fld-textarea @error('description') has-err @enderror"
                          placeholder="Décrivez l'article, ses spécifications, ses variantes…"
                          rows="3">{{ old('description') }}</textarea>
                @error('description')<p class="err-msg">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="art-card">
            <div class="art-card-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                Photo de l'article
            </div>
            <div class="upload-zone" id="uploadZone">
                <input type="file" name="photo" id="photoInput" class="d-none"
                       accept="image/*" onchange="handlePhotoChange(this)">
                <div class="upload-idle" id="uploadIdle">
                    <div class="upload-icon-wrap">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.3"/></svg>
                    </div>
                    <p class="upload-text">Glissez une image ou <span>cliquez</span></p>
                    <p class="upload-hint">JPG · PNG · GIF — max 2 Mo</p>
                </div>
                <div class="upload-preview d-none" id="uploadPreview">
                    <img id="previewImg" src="" alt="">
                    <button type="button" class="upload-remove" onclick="removePhoto()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="art-col">

        <div class="art-card">
            <div class="art-card-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
                Stock &amp; quantités
            </div>
            <div class="fld-row-3">
                <div class="fld">
                    <label class="fld-label">Stock initial <span class="req">*</span></label>
                    <div class="num-wrap">
                        <button type="button" class="num-btn" onclick="nudge('stock',-1)">−</button>
                        <input type="number" name="stock" id="stock"
                               class="num-input @error('stock') has-err @enderror"
                               min="0" value="{{ old('stock', 0) }}" required>
                        <button type="button" class="num-btn" onclick="nudge('stock',1)">+</button>
                    </div>
                    <p class="fld-hint">pièces</p>
                    @error('stock')<p class="err-msg">{{ $message }}</p>@enderror
                </div>
                <div class="fld">
                    <label class="fld-label">Qté minimale <span class="req">*</span></label>
                    <div class="num-wrap">
                        <button type="button" class="num-btn" onclick="nudge('quantite_minimale',-1)">−</button>
                        <input type="number" name="quantite_minimale" id="quantite_minimale"
                               class="num-input @error('quantite_minimale') has-err @enderror"
                               min="0" value="{{ old('quantite_minimale', 0) }}" required>
                        <button type="button" class="num-btn" onclick="nudge('quantite_minimale',1)">+</button>
                    </div>
                    <p class="fld-hint">seuil d'alerte</p>
                    @error('quantite_minimale')<p class="err-msg">{{ $message }}</p>@enderror
                </div>
                <div class="fld">
                    <label class="fld-label">Carton</label>
                    <div class="num-wrap">
                        <button type="button" class="num-btn" onclick="nudge('contenance_carton',-1)">−</button>
                        <input type="number" name="contenance_carton" id="contenance_carton"
                               class="num-input @error('contenance_carton') has-err @enderror"
                               min="1" value="{{ old('contenance_carton', 1) }}">
                        <button type="button" class="num-btn" onclick="nudge('contenance_carton',1)">+</button>
                    </div>
                    <p class="fld-hint">pièces/carton</p>
                    @error('contenance_carton')<p class="err-msg">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="stock-bar-wrap" id="stockBarWrap">
                <div class="stock-bar-track">
                    <div class="stock-bar-fill" id="stockBarFill"></div>
                    <div class="stock-bar-min"  id="stockBarMin"></div>
                </div>
                <div class="stock-bar-legend">
                    <span id="stockBarLabel">Stock: 0 pcs</span>
                    <span id="stockBarMinLabel">Min: 0</span>
                </div>
            </div>
        </div>

        <div class="art-card">
            <div class="art-card-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                Prix &amp; dates
            </div>
            <div class="fld">
                <label class="fld-label">Prix d'achat (DZD) <span class="opt">optionnel</span></label>
                <div class="fld-wrap">
                    <input type="number" step="0.01" name="prix_achat"
                           class="fld-input has-prefix @error('prix_achat') has-err @enderror"
                           placeholder="0.00" value="{{ old('prix_achat') }}">
                    <span class="fld-prefix">DA</span>
                </div>
                @error('prix_achat')<p class="err-msg">{{ $message }}</p>@enderror
            </div>
            <div class="fld">
                <label class="fld-label">Date de péremption <span class="opt">optionnel</span></label>
                <div class="fld-wrap">
                    <input type="date" name="date_peremption"
                           class="fld-input @error('date_peremption') has-err @enderror"
                           value="{{ old('date_peremption') }}">
                    <span class="fld-ico">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </span>
                </div>
                @error('date_peremption')<p class="err-msg">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="art-card recap-card" id="recapCard">
            <div class="art-card-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                Récapitulatif
            </div>
            <div class="recap-rows">
                <div class="recap-row"><span>Nom</span><strong id="r-nom">—</strong></div>
                <div class="recap-row"><span>Catégorie</span><strong id="r-cat">—</strong></div>
                <div class="recap-row"><span>Stock</span><strong id="r-stock">0 pcs</strong></div>
                <div class="recap-row"><span>Codes</span><strong id="r-codes">0</strong></div>
                <div class="recap-row"><span>Prix achat</span><strong id="r-prix">—</strong></div>
            </div>
        </div>
    </div>
</div>

<div class="art-actions">
    <a href="{{ route('articles.index') }}" class="btn-art-cancel">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        Annuler
    </a>
    <button type="submit" class="btn-art-save" id="btnSave">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Enregistrer
    </button>
</div>

</form>

{{-- SCANNER MODAL --}}
<div id="scanOverlay" class="scan-overlay" aria-hidden="true">
    <div class="scan-sheet" role="dialog" aria-modal="true" aria-label="Scanner un code-barres">

        <div class="scan-top">
            <div class="scan-top-left">
                <div class="scan-dot"></div>
                <span>Scanner un code-barres</span>
            </div>
            <button type="button" class="scan-close" id="btnCloseScan" aria-label="Fermer">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div class="scan-tabs">
            <button type="button" class="scan-tab active" data-tab="camera" onclick="switchScanTab('camera')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                Caméra
            </button>
            <button type="button" class="scan-tab" data-tab="image" onclick="switchScanTab('image')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                Image
            </button>
            <button type="button" class="scan-tab" data-tab="manual" onclick="switchScanTab('manual')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg>
                Manuel
            </button>
            <div class="scan-tab-indicator" id="scanTabIndicator"></div>
        </div>

        <div class="scan-panel" id="panelCamera">
            <div class="scan-viewport" id="scanViewport">
                <video id="scanVideo" autoplay muted playsinline></video>
                <div class="scan-overlay-frame">
                    <div class="scan-corner tl"></div>
                    <div class="scan-corner tr"></div>
                    <div class="scan-corner bl"></div>
                    <div class="scan-corner br"></div>
                    <div class="scan-beam" id="scanBeam"></div>
                </div>
                <canvas id="scanCanvas" style="display:none"></canvas>
            </div>
            <div class="scan-status-bar" id="scanStatus">
                <div class="scan-spinner"></div>
                <span id="scanStatusText">Initialisation…</span>
            </div>
        </div>

        <div class="scan-panel d-none" id="panelImage">
            <div class="img-drop-zone" id="imgDropZone">
                <input type="file" id="scanImageInput" accept="image/*" capture="environment" style="display:none">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                <p>Glissez une image ici</p>
                <span>ou</span>
                <button type="button" class="btn-pick-img" onclick="document.getElementById('scanImageInput').click()">Choisir un fichier</button>
                <p class="img-status" id="imgStatus"></p>
            </div>
        </div>

        <div class="scan-panel d-none" id="panelManual">
            <div class="manual-zone">
                <label>Saisissez le code-barres</label>
                <div class="manual-input-row">
                    <input type="text" id="manualInput" inputmode="numeric"
                           placeholder="Ex: 3760091721367"
                           onkeydown="if(event.key==='Enter'){event.preventDefault();confirmManual();}">
                    <button type="button" class="btn-manual-ok" onclick="confirmManual()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </button>
                </div>
                <p class="manual-hint">Appuyez sur <kbd>Entrée</kbd> pour valider</p>
            </div>
        </div>

        <div class="scan-success d-none" id="scanSuccess">
            <div class="scan-success-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="scan-success-code" id="scanSuccessCode"></div>
        </div>

    </div>
</div>

<div class="art-toast-wrap" id="toastWrap"></div>

@stop

@section('css')
<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap');

:root {
    --bg:        #0d0f14;
    --surface:   #13161d;
    --surface2:  #1a1e28;
    --border:    rgba(255,255,255,.07);
    --border2:   rgba(255,255,255,.12);
    --accent:    #6c63ff;
    --accent2:   #8b83ff;
    --green:     #22d37a;
    --red:       #ff5252;
    --yellow:    #ffc94a;
    --text:      #e8eaf0;
    --text2:     #8890a4;
    --text3:     #555e72;
    --radius:    14px;
    --radius-sm: 9px;
    --font:      'DM Sans', system-ui, sans-serif;
    --font-h:    'Syne', sans-serif;
    --transition: .2s cubic-bezier(.4,0,.2,1);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; }
body, .content-wrapper { background: var(--bg) !important; font-family: var(--font); color: var(--text); }
.content-wrapper { padding-bottom: 110px !important; }
a { color: inherit; }

.art-header { display: flex; align-items: center; gap: 1rem; padding: 1.25rem 1.5rem; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 1.5rem; }
.art-back { width: 40px; height: 40px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: var(--surface2); border: 1px solid var(--border2); border-radius: var(--radius-sm); color: var(--text2); text-decoration: none; transition: var(--transition); }
.art-back:hover { background: var(--accent); border-color: var(--accent); color: white; }
.art-title { font-family: var(--font-h); font-size: 1.35rem; font-weight: 700; color: var(--text); line-height: 1; }
.art-sub { font-size: .78rem; color: var(--text3); margin-top: .25rem; }
.art-header-badge { margin-left: auto; display: flex; align-items: center; gap: .5rem; padding: .35rem .9rem; border-radius: 20px; background: rgba(108,99,255,.12); border: 1px solid rgba(108,99,255,.3); font-size: .78rem; font-weight: 600; color: var(--accent2); }
.pulse-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--green); box-shadow: 0 0 0 0 rgba(34,211,122,.4); animation: pulse 1.8s infinite; }
@keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(34,211,122,.4); } 70% { box-shadow: 0 0 0 7px rgba(34,211,122,0); } 100% { box-shadow: 0 0 0 0 rgba(34,211,122,0); } }

.art-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.art-col  { display: flex; flex-direction: column; gap: 1.25rem; }

.art-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; transition: border-color var(--transition); }
.art-card:hover { border-color: var(--border2); }
.art-card-label { display: flex; align-items: center; gap: .5rem; font-family: var(--font-h); font-size: .7rem; font-weight: 700; color: var(--text3); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 1.25rem; padding-bottom: .75rem; border-bottom: 1px solid var(--border); }
.art-card-label svg { color: var(--accent); }

.fld { margin-bottom: 1.1rem; }
.fld:last-child { margin-bottom: 0; }
.fld-label { display: block; font-size: .8rem; font-weight: 600; color: var(--text2); margin-bottom: .45rem; }
.req { color: var(--red); margin-left: .15rem; }
.opt { color: var(--text3); font-weight: 400; font-size: .75rem; margin-left: .3rem; }
.fld-wrap { position: relative; }
.fld-input { width: 100%; height: 44px; background: var(--surface2); border: 1px solid var(--border2); border-radius: var(--radius-sm); padding: 0 2.6rem 0 .9rem; font-family: var(--font); font-size: .92rem; color: var(--text); outline: none; transition: border-color var(--transition), box-shadow var(--transition); -webkit-appearance: none; }
.fld-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(108,99,255,.18); }
.fld-input.has-err { border-color: var(--red); }
.fld-input::placeholder { color: var(--text3); }
.fld-ico { position: absolute; right: .75rem; top: 50%; transform: translateY(-50%); color: var(--text3); pointer-events: none; }
.fld-input.has-prefix { padding-left: 3rem; }
.fld-prefix { position: absolute; left: .9rem; top: 50%; transform: translateY(-50%); font-size: .8rem; font-weight: 700; color: var(--accent); pointer-events: none; }
.fld-textarea { width: 100%; background: var(--surface2); border: 1px solid var(--border2); border-radius: var(--radius-sm); padding: .7rem .9rem; font-family: var(--font); font-size: .92rem; color: var(--text); resize: vertical; min-height: 80px; outline: none; transition: border-color var(--transition), box-shadow var(--transition); }
.fld-textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(108,99,255,.18); }
.fld-textarea::placeholder { color: var(--text3); }
.fld-hint { font-size: .74rem; color: var(--text3); margin-top: .35rem; }
.err-msg  { font-size: .77rem; color: var(--red); margin-top: .35rem; }

#barcodeList { display: flex; flex-direction: column; gap: .55rem; margin-bottom: .75rem; }
.bc-row { display: flex; gap: .45rem; align-items: center; background: var(--surface2); border: 1px solid var(--border2); border-radius: var(--radius-sm); padding: .55rem .65rem; transition: border-color var(--transition); animation: rowIn .2s ease; }
@keyframes rowIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:none; } }
.bc-row.is-primary { border-color: var(--green); }
.bc-code { flex: 2; min-width: 0; height: 36px; background: var(--surface); border: 1px solid var(--border2); border-radius: 7px; padding: 0 .7rem; font-family: 'Courier New', monospace; font-size: .88rem; color: var(--text); outline: none; transition: border-color var(--transition); }
.bc-code:focus { border-color: var(--accent); }
.bc-code.ok { border-color: var(--green); background: rgba(34,211,122,.05); }
.bc-lbl { flex: 1; min-width: 0; height: 36px; background: var(--surface); border: 1px solid var(--border2); border-radius: 7px; padding: 0 .7rem; font-family: var(--font); font-size: .82rem; color: var(--text2); outline: none; }
.bc-lbl:focus { border-color: var(--accent); outline: none; }
.bc-lbl::placeholder { color: var(--text3); }
.bc-btn { width: 34px; height: 34px; flex-shrink: 0; border: none; border-radius: 7px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: opacity var(--transition), transform var(--transition); color: white; }
.bc-btn:hover { opacity: .85; transform: scale(1.07); }
.bc-btn-cam  { background: var(--green); }
.bc-btn-img  { background: var(--accent); }
.bc-btn-del  { background: rgba(255,82,82,.2); color: var(--red); border: 1px solid rgba(255,82,82,.3); }
.bc-btn-del:hover { background: var(--red); color: white; }
.bc-primary-btn { height: 34px; padding: 0 .6rem; border-radius: 7px; border: 1px solid var(--border2); background: transparent; color: var(--text3); font-size: .7rem; font-weight: 700; font-family: var(--font); cursor: pointer; white-space: nowrap; transition: all var(--transition); flex-shrink: 0; }
.bc-primary-btn.on { background: var(--green); border-color: var(--green); color: #0d1f14; }
.bc-primary-btn:hover:not(.on) { border-color: var(--green); color: var(--green); }
.btn-add-bc { display: inline-flex; align-items: center; gap: .45rem; padding: .55rem 1rem; border-radius: var(--radius-sm); background: rgba(108,99,255,.12); border: 1px dashed rgba(108,99,255,.4); color: var(--accent2); font-size: .82rem; font-weight: 600; font-family: var(--font); cursor: pointer; transition: all var(--transition); }
.btn-add-bc:hover { background: rgba(108,99,255,.22); border-style: solid; }

.combo-wrap { position: relative; }
.combo-trigger { display: flex; align-items: center; gap: .5rem; height: 44px; padding: 0 .9rem; background: var(--surface2); border: 1px solid var(--border2); border-radius: var(--radius-sm); cursor: text; transition: border-color var(--transition), box-shadow var(--transition); }
.combo-trigger:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(108,99,255,.18); }
.combo-ico { color: var(--accent); flex-shrink: 0; }
.combo-input { flex: 1; background: none; border: none; outline: none; font-family: var(--font); font-size: .92rem; color: var(--text); min-width: 0; }
.combo-input::placeholder { color: var(--text3); }
.combo-clear { background: none; border: none; cursor: pointer; color: var(--text3); padding: 4px; display: flex; border-radius: 4px; transition: color var(--transition); }
.combo-clear:hover { color: var(--red); }
.combo-chevron { color: var(--text3); flex-shrink: 0; transition: transform var(--transition); }
.combo-trigger:focus-within .combo-chevron { transform: rotate(180deg); }
.combo-dropdown { position: absolute; top: calc(100% + 5px); left: 0; right: 0; background: var(--surface2); border: 1px solid var(--accent); border-radius: var(--radius-sm); box-shadow: 0 16px 40px rgba(0,0,0,.5); z-index: 1000; max-height: 240px; overflow-y: auto; animation: dropIn .15s ease; }
@keyframes dropIn { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:none; } }
.combo-dropdown::-webkit-scrollbar { width: 4px; }
.combo-dropdown::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 2px; }
.combo-item { display: flex; align-items: center; gap: .5rem; padding: .65rem .9rem; cursor: pointer; font-size: .88rem; border-bottom: 1px solid var(--border); transition: background var(--transition); }
.combo-item:last-child { border-bottom: none; }
.combo-item:hover { background: rgba(108,99,255,.1); }
.combo-item.depth-1 { padding-left: 1.8rem; color: var(--text2); font-size: .84rem; }
.combo-item.depth-2 { padding-left: 2.6rem; color: var(--text3); font-size: .8rem; }
.combo-item svg { color: var(--accent); flex-shrink: 0; }
.cmatch { color: var(--accent2); font-weight: 700; }
.combo-empty { padding: 1.5rem; text-align: center; color: var(--text3); font-size: .85rem; display: flex; flex-direction: column; align-items: center; gap: .5rem; }
.combo-badge { display: inline-flex; align-items: center; gap: .4rem; margin-top: .45rem; padding: .35rem .75rem; background: rgba(34,211,122,.1); border: 1px solid rgba(34,211,122,.3); border-radius: 20px; font-size: .8rem; font-weight: 600; color: var(--green); }

.upload-zone { border: 2px dashed var(--border2); border-radius: var(--radius); overflow: hidden; transition: border-color var(--transition); cursor: pointer; }
.upload-zone:hover, .upload-zone.dragover { border-color: var(--accent); background: rgba(108,99,255,.04); }
.upload-idle { padding: 2rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: .5rem; }
.upload-icon-wrap { width: 64px; height: 64px; border-radius: 50%; background: var(--surface2); border: 1px solid var(--border2); display: flex; align-items: center; justify-content: center; color: var(--accent); margin-bottom: .5rem; }
.upload-text { font-size: .9rem; color: var(--text2); }
.upload-text span { color: var(--accent); font-weight: 600; text-decoration: underline; }
.upload-hint { font-size: .75rem; color: var(--text3); }
.upload-preview { position: relative; }
.upload-preview img { width: 100%; max-height: 260px; object-fit: cover; display: block; }
.upload-remove { position: absolute; top: .6rem; right: .6rem; width: 32px; height: 32px; border-radius: 50%; background: rgba(0,0,0,.7); border: 1px solid rgba(255,255,255,.2); color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background var(--transition); }
.upload-remove:hover { background: var(--red); }

.fld-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: .75rem; }
.num-wrap { display: flex; align-items: center; height: 44px; background: var(--surface2); border: 1px solid var(--border2); border-radius: var(--radius-sm); overflow: hidden; }
.num-btn { width: 38px; flex-shrink: 0; height: 100%; border: none; background: var(--surface); color: var(--text2); font-size: 1.1rem; cursor: pointer; transition: all var(--transition); }
.num-btn:hover { background: var(--accent); color: white; }
.num-input { flex: 1; min-width: 0; height: 100%; border: none; outline: none; background: none; text-align: center; font-family: var(--font-h); font-size: 1rem; font-weight: 700; color: var(--text); -moz-appearance: textfield; }
.num-input::-webkit-outer-spin-button, .num-input::-webkit-inner-spin-button { -webkit-appearance: none; }

.stock-bar-wrap { margin-top: 1rem; }
.stock-bar-track { position: relative; height: 8px; border-radius: 4px; background: var(--surface2); border: 1px solid var(--border); overflow: visible; }
.stock-bar-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg, var(--green), #6ef7a7); transition: width .5s cubic-bezier(.4,0,.2,1); width: 0; }
.stock-bar-min { position: absolute; top: -4px; width: 3px; height: 16px; background: var(--yellow); border-radius: 2px; transition: left .5s cubic-bezier(.4,0,.2,1); left: 0; }
.stock-bar-legend { display: flex; justify-content: space-between; font-size: .72rem; color: var(--text3); margin-top: .5rem; }

.recap-card { border-color: rgba(108,99,255,.2); }
.recap-rows { display: flex; flex-direction: column; gap: .55rem; }
.recap-row { display: flex; justify-content: space-between; align-items: baseline; font-size: .83rem; color: var(--text2); padding-bottom: .45rem; border-bottom: 1px solid var(--border); }
.recap-row:last-child { border-bottom: none; padding-bottom: 0; }
.recap-row strong { color: var(--text); font-weight: 600; max-width: 60%; text-align: right; }

.art-actions { position: sticky; bottom: 0; z-index: 800; display: flex; justify-content: flex-end; gap: .75rem; padding: 1rem 1.5rem; background: rgba(13,15,20,.92); backdrop-filter: blur(12px); border-top: 1px solid var(--border); margin-top: 1.5rem; }
.btn-art-cancel { display: inline-flex; align-items: center; gap: .5rem; height: 44px; padding: 0 1.4rem; border-radius: var(--radius-sm); background: var(--surface2); border: 1px solid var(--border2); color: var(--text2); font-size: .88rem; font-weight: 600; text-decoration: none; transition: all var(--transition); }
.btn-art-cancel:hover { border-color: var(--red); color: var(--red); text-decoration: none; }
.btn-art-save { display: inline-flex; align-items: center; gap: .5rem; height: 44px; padding: 0 1.75rem; border-radius: var(--radius-sm); background: var(--accent); border: none; color: white; font-family: var(--font); font-size: .88rem; font-weight: 700; cursor: pointer; transition: all var(--transition); box-shadow: 0 4px 20px rgba(108,99,255,.4); }
.btn-art-save:hover { background: var(--accent2); transform: translateY(-2px); }
.btn-art-save:active { transform: translateY(0); }

.scan-overlay { position: fixed; inset: 0; z-index: 9000; background: rgba(0,0,0,.8); backdrop-filter: blur(8px); display: flex; align-items: flex-end; justify-content: center; opacity: 0; pointer-events: none; transition: opacity .25s; }
.scan-overlay.open { opacity: 1; pointer-events: all; }
.scan-sheet { width: min(100%, 460px); max-height: 90vh; background: var(--surface); border-radius: var(--radius) var(--radius) 0 0; overflow: hidden; display: flex; flex-direction: column; transform: translateY(40px); transition: transform .3s cubic-bezier(.34,1.56,.64,1); }
.scan-overlay.open .scan-sheet { transform: translateY(0); }
.scan-top { display: flex; align-items: center; justify-content: space-between; padding: .9rem 1.1rem; border-bottom: 1px solid var(--border); font-family: var(--font-h); font-size: .85rem; font-weight: 700; color: var(--text); }
.scan-top-left { display: flex; align-items: center; gap: .6rem; }
.scan-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--green); animation: pulse 1.8s infinite; }
.scan-close { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border2); background: var(--surface2); color: var(--text2); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all var(--transition); }
.scan-close:hover { background: var(--red); border-color: var(--red); color: white; }
.scan-tabs { display: flex; position: relative; border-bottom: 1px solid var(--border); background: var(--surface2); }
.scan-tab { flex: 1; display: flex; align-items: center; justify-content: center; gap: .4rem; height: 42px; background: none; border: none; cursor: pointer; font-family: var(--font); font-size: .8rem; font-weight: 600; color: var(--text3); transition: color var(--transition); position: relative; z-index: 1; }
.scan-tab.active { color: var(--accent2); }
.scan-tab-indicator { position: absolute; bottom: 0; height: 2px; background: var(--accent); border-radius: 2px 2px 0 0; transition: left .2s ease, width .2s ease; }
.scan-panel { padding: 0; }
.scan-viewport { position: relative; width: 100%; background: #000; overflow: hidden; max-height: 340px; }
#scanVideo { width: 100%; display: block; max-height: 340px; object-fit: cover; }
#scanCanvas { display: none; }
.scan-overlay-frame { position: absolute; inset: 0; pointer-events: none; }
.scan-corner { position: absolute; width: 24px; height: 24px; border-color: var(--accent2); border-style: solid; }
.scan-corner.tl { top: 20px; left: 20px; border-width: 3px 0 0 3px; border-radius: 4px 0 0 0; }
.scan-corner.tr { top: 20px; right: 20px; border-width: 3px 3px 0 0; border-radius: 0 4px 0 0; }
.scan-corner.bl { bottom: 20px; left: 20px; border-width: 0 0 3px 3px; border-radius: 0 0 0 4px; }
.scan-corner.br { bottom: 20px; right: 20px; border-width: 0 3px 3px 0; border-radius: 0 0 4px 0; }
.scan-beam { position: absolute; left: 20px; right: 20px; height: 2px; background: linear-gradient(90deg, transparent, var(--accent), var(--accent2), var(--accent), transparent); box-shadow: 0 0 10px 2px rgba(108,99,255,.6); animation: beamSweep 2s ease-in-out infinite; top: 20px; }
@keyframes beamSweep { 0% { top: 20px; } 50% { top: calc(100% - 20px); } 100% { top: 20px; } }
.scan-status-bar { display: flex; align-items: center; gap: .6rem; padding: .65rem 1rem; background: var(--surface2); border-top: 1px solid var(--border); font-size: .8rem; color: var(--text2); }
.scan-spinner { width: 14px; height: 14px; border: 2px solid var(--border2); border-top-color: var(--accent); border-radius: 50%; animation: spin .7s linear infinite; flex-shrink: 0; }
@keyframes spin { to { transform: rotate(360deg); } }
.img-drop-zone { display: flex; flex-direction: column; align-items: center; gap: .6rem; padding: 2rem 1.5rem; text-align: center; }
.img-drop-zone svg { color: var(--text3); }
.img-drop-zone p { font-size: .88rem; color: var(--text2); }
.img-drop-zone span { font-size: .8rem; color: var(--text3); }
.btn-pick-img { padding: .55rem 1.4rem; border-radius: var(--radius-sm); background: var(--accent); border: none; color: white; font-family: var(--font); font-size: .85rem; font-weight: 600; cursor: pointer; transition: all var(--transition); }
.btn-pick-img:hover { background: var(--accent2); }
.img-status { font-size: .8rem; color: var(--text3); }
.manual-zone { padding: 1.5rem; }
.manual-zone label { display: block; font-size: .8rem; color: var(--text2); font-weight: 600; margin-bottom: .6rem; }
.manual-input-row { display: flex; gap: .5rem; }
.manual-input-row input { flex: 1; height: 46px; background: var(--surface2); border: 1.5px solid var(--border2); border-radius: var(--radius-sm); padding: 0 .9rem; font-family: 'Courier New', monospace; font-size: 1rem; color: var(--text); outline: none; transition: border-color var(--transition); }
.manual-input-row input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(108,99,255,.18); }
.manual-input-row input::placeholder { color: var(--text3); font-family: var(--font); font-size: .88rem; }
.btn-manual-ok { width: 46px; height: 46px; border-radius: var(--radius-sm); background: var(--green); border: none; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all var(--transition); }
.btn-manual-ok:hover { background: #1db368; transform: scale(1.05); }
.manual-hint { font-size: .74rem; color: var(--text3); margin-top: .6rem; }
.manual-hint kbd { display: inline-block; padding: .1rem .4rem; border-radius: 4px; background: var(--surface2); border: 1px solid var(--border2); font-size: .7rem; font-family: var(--font); }
.scan-success { position: absolute; inset: 0; z-index: 10; background: rgba(13,15,20,.92); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .75rem; animation: fadeIn .2s ease; }
@keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
.scan-success-icon { width: 64px; height: 64px; border-radius: 50%; background: rgba(34,211,122,.15); border: 2px solid var(--green); display: flex; align-items: center; justify-content: center; color: var(--green); animation: popIn .3s cubic-bezier(.34,1.56,.64,1); }
@keyframes popIn { from { transform:scale(.5); opacity:0; } to { transform:scale(1); opacity:1; } }
.scan-success-code { font-family: 'Courier New', monospace; font-size: 1.1rem; color: var(--text); background: var(--surface2); padding: .5rem 1.2rem; border-radius: var(--radius-sm); border: 1px solid var(--green); }
.art-toast-wrap { position: fixed; bottom: 80px; right: 1.5rem; display: flex; flex-direction: column; gap: .5rem; z-index: 99999; pointer-events: none; }
.art-toast { display: flex; align-items: center; gap: .6rem; padding: .65rem 1rem; border-radius: var(--radius-sm); font-size: .82rem; font-weight: 600; color: white; box-shadow: 0 8px 24px rgba(0,0,0,.4); animation: toastIn .25s cubic-bezier(.34,1.56,.64,1); backdrop-filter: blur(8px); }
.art-toast.ok   { background: rgba(34,211,122,.92); }
.art-toast.err  { background: rgba(255,82,82,.92); }
.art-toast.warn { background: rgba(255,201,74,.92); color: #1a1200; }
@keyframes toastIn { from { opacity:0; transform:translateX(24px); } to { opacity:1; transform:none; } }
.d-none { display: none !important; }
.content-header { background: transparent !important; box-shadow: none !important; }
@media (max-width: 900px) { .art-grid { grid-template-columns: 1fr; } }
@media (max-width: 520px) { .fld-row-3 { grid-template-columns: 1fr 1fr; } .art-actions { flex-direction: column; } .btn-art-cancel, .btn-art-save { width: 100%; justify-content: center; } }
</style>
@stop

@php
    if (!function_exists('flattenCats')) {
        function flattenCats($cats, $depth = 0) {
            $out = [];
            foreach ($cats as $c) {
                $out[] = ['id' => $c->id, 'nom' => $c->nom, 'depth' => $depth];
                if (!empty($c->children)) $out = array_merge($out, flattenCats($c->children, $depth + 1));
            }
            return $out;
        }
    }
    $flatCats = flattenCats($categories);
@endphp

@section('js')
{{-- ZXing WASM : fallback pour iOS < 17 et vieux Android --}}
<script src="https://cdn.jsdelivr.net/npm/zxing-wasm@1/dist/browser/zxing_barcode_reader.umd.js"></script>
<script>
'use strict';

/* ══════════════════════════════════════════════════════════════
   VARIABLES GLOBALES — déclarées UNE SEULE FOIS
══════════════════════════════════════════════════════════════ */
const CATS  = {!! json_encode($flatCats) !!};
const MARQS = {!! json_encode($marques->map(fn($m)=>['id'=>$m->id,'nom'=>$m->nom])->values()) !!};

let bcCounter      = 0;
let activeRow      = null;
let camStream      = null;
let scanActive     = false;
let scanLoopId     = null;
let currentTab     = 'camera';
let nativeDetector = null;
let zxingReader    = null;

/* ══════════════════════════════════════════════════════════════
   INIT AU CHARGEMENT
══════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {

    /* pré-chauffe le moteur de scan */
    initDetector();

    /* barcodes initiaux */
    @if(old('barcodes'))
        const ob = Object.values({!! json_encode(old('barcodes')) !!});
        ob.forEach((b, i) => addRow(i === 0, b));
        const pi = ob.findIndex(b => b.primary === '1' || b.primary === 1);
        if (pi > 0) setPrimary(pi + 1);
    @else
        addRow(true);
    @endif

    document.getElementById('btnAddBarcode').addEventListener('click', () => addRow(false));
    document.getElementById('btnCloseScan').addEventListener('click', closeScan);

    document.getElementById('scanOverlay').addEventListener('click', e => {
        if (e.target.id === 'scanOverlay') closeScan();
    });

    document.getElementById('scanImageInput').addEventListener('change', handleScanImage);

    /* upload photo drag & drop */
    const uz = document.getElementById('uploadZone');
    uz.addEventListener('click', () => document.getElementById('photoInput').click());
    uz.addEventListener('dragover',  e => { e.preventDefault(); uz.classList.add('dragover'); });
    uz.addEventListener('dragleave', () => uz.classList.remove('dragover'));
    uz.addEventListener('drop', e => {
        e.preventDefault(); uz.classList.remove('dragover');
        const f = e.dataTransfer.files[0];
        if (f && f.type.startsWith('image/')) {
            const dt = new DataTransfer(); dt.items.add(f);
            document.getElementById('photoInput').files = dt.files;
            handlePhotoChange(document.getElementById('photoInput'));
        }
    });

    /* combos */
    buildCombo({ searchId:'catSearch', dropdownId:'catDropdown', listId:'catList', emptyId:'catEmpty', hiddenId:'categorie_id', badgeId:'catBadge', badgeNameId:'catBadgeName', clearId:'catClear', data: CATS, depthClass: true });
    buildCombo({ searchId:'marSearch', dropdownId:'marDropdown', listId:'marList', emptyId:'marEmpty', hiddenId:'marque_id',    badgeId:'marBadge', badgeNameId:'marBadgeName', clearId:'marClear', data: MARQS, depthClass: false });

    @if(old('categorie_id'))
    const pc = CATS.find(c => c.id == {{ old('categorie_id') }});
    if (pc) preSelect('catSearch','categorie_id','catBadge','catBadgeName','catClear', pc);
    @endif
    @if(old('marque_id'))
    const pm = MARQS.find(m => m.id == {{ old('marque_id') }});
    if (pm) preSelect('marSearch','marque_id','marBadge','marBadgeName','marClear', pm);
    @endif

    ['stock','quantite_minimale'].forEach(id =>
        document.getElementById(id).addEventListener('input', updateStockBar)
    );
    updateStockBar();

    document.querySelector('input[name="nom"]').addEventListener('input', updateRecap);
    document.querySelector('input[name="prix_achat"]').addEventListener('input', updateRecap);
    document.getElementById('stock').addEventListener('input', updateRecap);
    updateRecap();

    document.getElementById('mainForm').addEventListener('submit', onSubmit);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeScan(); });

    updateTabIndicator('camera');
});

/* ══════════════════════════════════════════════════════════════
   MOTEUR DE SCAN — détection automatique du meilleur moteur
══════════════════════════════════════════════════════════════ */
async function initDetector() {
    /* 1. BarcodeDetector natif (Android 9+ Chrome / iOS 17+ WKWebView) */
    if ('BarcodeDetector' in window) {
        try {
            const supported = await BarcodeDetector.getSupportedFormats();
            const formats = supported.filter(f =>
                ['ean_13','ean_8','code_128','code_39','qr_code','upc_a','upc_e','itf','codabar','data_matrix'].includes(f)
            );
            nativeDetector = new BarcodeDetector({ formats: formats.length ? formats : ['ean_13','code_128','qr_code'] });
            console.log('[Scanner] ⚡ BarcodeDetector natif actif');
            return;
        } catch(e) { console.warn('[Scanner] BarcodeDetector erreur:', e); }
    }

    /* 2. ZXing WASM (fallback universel) */
    if (typeof ZXingWasm !== 'undefined') {
        try {
            zxingReader = new ZXingWasm.BrowserMultiFormatReader();
            console.log('[Scanner] ZXing WASM fallback actif');
            return;
        } catch(e) { console.warn('[Scanner] ZXing WASM erreur:', e); }
    }

    /* 3. Ancien ZXing (zxing.min.js) */
    if (typeof ZXing !== 'undefined') {
        try {
            zxingReader = { legacy: true };
            console.log('[Scanner] ZXing legacy fallback actif');
        } catch(e) {}
    }

    console.warn('[Scanner] Aucun moteur natif — mode manuel uniquement');
}

/* ══════════════════════════════════════════════════════════════
   BARCODE ROWS
══════════════════════════════════════════════════════════════ */
function addRow(primary = false, data = {}) {
    const id  = ++bcCounter;
    const div = document.createElement('div');
    div.className = 'bc-row' + (primary ? ' is-primary' : '');
    div.id = 'bcr-' + id;
    div.innerHTML = `
        <input type="hidden" name="barcodes[${id}][id]" value="${esc(data.id||'')}">
        <input type="text" name="barcodes[${id}][code]" id="bci-${id}"
               class="bc-code" placeholder="Code-barres…"
               value="${esc(data.code||'')}" required autocomplete="off"
               oninput="onBcInput(${id})">
        <input type="text" name="barcodes[${id}][label]" class="bc-lbl"
               placeholder="Libellé…" value="${esc(data.label||'')}">
        <button type="button" class="bc-primary-btn${primary ? ' on' : ''}" id="bcp-${id}"
                onclick="setPrimary(${id})">★ Principal</button>
        <button type="button" class="bc-btn bc-btn-cam" onclick="openScan(${id},'camera')" title="Caméra">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
        </button>
        <button type="button" class="bc-btn bc-btn-img" onclick="openScan(${id},'image')" title="Image">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        </button>
        <button type="button" class="bc-btn bc-btn-del" onclick="delRow(${id})" title="Supprimer">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
        </button>
        <input type="hidden" name="barcodes[${id}][primary]" id="bcpv-${id}" value="${primary ? '1' : ''}">
    `;
    document.getElementById('barcodeList').appendChild(div);
    ensurePrimary();
    updateRecap();
}

function onBcInput(id) {
    const inp = document.getElementById('bci-' + id);
    inp.classList.toggle('ok', inp.value.trim().length > 0);
    updateRecap();
}

function setPrimary(id) {
    document.querySelectorAll('#barcodeList .bc-row').forEach(r => r.classList.remove('is-primary'));
    document.querySelectorAll('#barcodeList .bc-primary-btn').forEach(b => b.classList.remove('on'));
    document.querySelectorAll('[id^="bcpv-"]').forEach(i => i.value = '');
    document.getElementById('bcr-' + id).classList.add('is-primary');
    document.getElementById('bcp-' + id).classList.add('on');
    document.getElementById('bcpv-' + id).value = '1';
}

function delRow(id) {
    const list = document.getElementById('barcodeList');
    if (list.children.length <= 1) { toast('⚠️ Minimum un code-barres requis', 'warn'); return; }
    const wasPrimary = document.getElementById('bcpv-' + id).value === '1';
    document.getElementById('bcr-' + id).remove();
    if (wasPrimary) ensurePrimary();
    updateRecap();
}

function ensurePrimary() {
    if (!document.querySelector('#barcodeList .bc-primary-btn.on')) {
        const first = document.querySelector('#barcodeList .bc-primary-btn');
        if (first) { const m = first.id.match(/bcp-(\d+)/); if (m) setPrimary(+m[1]); }
    }
}

/* ══════════════════════════════════════════════════════════════
   SCANNER — OUVERTURE / FERMETURE
══════════════════════════════════════════════════════════════ */
function openScan(rowId, tab = 'camera') {
    activeRow = rowId;
    const overlay = document.getElementById('scanOverlay');
    overlay.classList.add('open');
    overlay.removeAttribute('aria-hidden');
    document.getElementById('scanSuccess').classList.add('d-none');
    document.getElementById('imgStatus').textContent = '';
    document.getElementById('manualInput').value = '';
    switchScanTab(tab);
}

function closeScan() {
    stopCam();
    const overlay = document.getElementById('scanOverlay');
    overlay.classList.remove('open');
    overlay.setAttribute('aria-hidden', 'true');
    activeRow  = null;
    scanActive = false;
}

/* ══════════════════════════════════════════════════════════════
   TABS
══════════════════════════════════════════════════════════════ */
function switchScanTab(tab) {
    currentTab = tab;
    ['camera', 'image', 'manual'].forEach(t => {
        const panel = document.getElementById('panel' + cap(t));
        const btn   = document.querySelector(`.scan-tab[data-tab="${t}"]`);
        if (panel) panel.classList.toggle('d-none', t !== tab);
        if (btn)   btn.classList.toggle('active',   t === tab);
    });
    updateTabIndicator(tab);
    if (tab === 'camera') {
        startCam();
    } else {
        stopCam();
        if (tab === 'manual') setTimeout(() => document.getElementById('manualInput').focus(), 80);
    }
}

function updateTabIndicator(tab) {
    const btn = document.querySelector(`.scan-tab[data-tab="${tab}"]`);
    const ind = document.getElementById('scanTabIndicator');
    if (!btn || !ind) return;
    const parent = btn.parentElement.getBoundingClientRect();
    const rect   = btn.getBoundingClientRect();
    ind.style.left  = (rect.left - parent.left) + 'px';
    ind.style.width = rect.width + 'px';
}

/* ══════════════════════════════════════════════════════════════
   CAMÉRA
══════════════════════════════════════════════════════════════ */
async function startCam() {
    if (scanActive) return;
    setStatus('Accès caméra…', true);

    if (!navigator.mediaDevices?.getUserMedia) {
        setStatus('❌ Caméra non disponible — utilisez Manuel.', false);
        return;
    }

    if (!nativeDetector && !zxingReader) await initDetector();

    try {
        camStream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: { ideal: 'environment' },
                width:  { ideal: 1280, min: 640 },
                height: { ideal: 720,  min: 480 },
                advanced: [{ focusMode: 'continuous' }]
            },
            audio: false
        });

        const video = document.getElementById('scanVideo');
        video.srcObject = camStream;
        await video.play();

        setStatus(nativeDetector ? '⚡ Moteur natif — pointez le code' : '🔍 Pointez le code-barres', false);
        scanActive = true;
        startScanLoop(video);

    } catch (err) {
        scanActive = false;
        const msgs = {
            NotAllowedError:      '❌ Permission caméra refusée.',
            PermissionDeniedError:'❌ Permission caméra refusée.',
            NotFoundError:        '❌ Aucune caméra détectée.',
            OverconstrainedError: '❌ Essayez de recharger la page.'
        };
        setStatus(msgs[err.name] || '❌ Erreur : ' + err.message, false);
    }
}

/* ══════════════════════════════════════════════════════════════
   BOUCLE DE SCAN (requestAnimationFrame — ultra-fluide)
══════════════════════════════════════════════════════════════ */
function startScanLoop(video) {
    const canvas = document.getElementById('scanCanvas');
    const ctx    = canvas.getContext('2d', { willReadFrequently: true });
    let lastScan = 0;
    const INTERVAL = nativeDetector ? 100 : 150;

    const loop = async (ts) => {
        if (!scanActive) return;
        if (ts - lastScan < INTERVAL) { scanLoopId = requestAnimationFrame(loop); return; }
        if (video.readyState < video.HAVE_ENOUGH_DATA) { scanLoopId = requestAnimationFrame(loop); return; }
        lastScan = ts;

        try {
            if (nativeDetector) {
                /* ── BarcodeDetector natif : < 50ms sur mobile ── */
                const bmp     = await createImageBitmap(video);
                const results = await nativeDetector.detect(bmp);
                bmp.close();
                if (results.length > 0) { applyCode(results[0].rawValue); return; }
            } else if (zxingReader && !zxingReader.legacy) {
                /* ── ZXing WASM ── */
                canvas.width  = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0);
                const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const lum     = new ZXing.RGBLuminanceSource(imgData.data, canvas.width, canvas.height);
                const bmp2    = new ZXing.BinaryBitmap(new ZXing.HybridBinarizer(lum));
                const result  = new ZXing.MultiFormatReader().decode(bmp2);
                if (result) { applyCode(result.getText()); return; }
            }
        } catch (_) { /* pas encore trouvé — continuer */ }

        scanLoopId = requestAnimationFrame(loop);
    };

    scanLoopId = requestAnimationFrame(loop);
}

function stopCam() {
    scanActive = false;
    if (scanLoopId)  { cancelAnimationFrame(scanLoopId); scanLoopId = null; }
    if (camStream)   { camStream.getTracks().forEach(t => t.stop()); camStream = null; }
    const v = document.getElementById('scanVideo');
    if (v) { v.pause(); v.srcObject = null; }
}

/* ══════════════════════════════════════════════════════════════
   SCAN IMAGE
══════════════════════════════════════════════════════════════ */
async function handleScanImage() {
    const file = this.files?.[0];
    if (!file || activeRow === null) return;
    document.getElementById('imgStatus').textContent = '⏳ Analyse…';
    if (!nativeDetector && !zxingReader) await initDetector();
    try {
        if (nativeDetector) {
            const bmp     = await createImageBitmap(file);
            const results = await nativeDetector.detect(bmp);
            bmp.close();
            if (results.length > 0) { applyCode(results[0].rawValue); return; }
            throw new Error('non trouvé');
        } else {
            const url    = URL.createObjectURL(file);
            const reader = new ZXing.BrowserMultiFormatReader();
            const result = await reader.decodeFromImageUrl(url);
            URL.revokeObjectURL(url);
            if (result) { applyCode(result.getText()); return; }
        }
    } catch (_) {
        document.getElementById('imgStatus').textContent = '❌ Aucun code-barres détecté.';
    }
    this.value = '';
}

/* ══════════════════════════════════════════════════════════════
   MANUEL
══════════════════════════════════════════════════════════════ */
function confirmManual() {
    const val = document.getElementById('manualInput').value.trim();
    if (!val) { toast('Saisissez un code-barres', 'warn'); return; }
    applyCode(val);
}

/* ══════════════════════════════════════════════════════════════
   APPLIQUER LE CODE DANS LE CHAMP
══════════════════════════════════════════════════════════════ */
function applyCode(code) {
    if (activeRow === null) return;
    stopCam();

    document.getElementById('scanSuccessCode').textContent = code;
    document.getElementById('scanSuccess').classList.remove('d-none');

    const inp = document.getElementById('bci-' + activeRow);
    if (inp) {
        inp.value = code;
        inp.classList.add('ok');
        inp.dispatchEvent(new Event('input'));
        updateRecap();
    }

    if (navigator.vibrate) navigator.vibrate([60, 30, 60]);
    toast('✓ Code capturé : ' + code, 'ok');
    setTimeout(() => closeScan(), 900);
}

/* ══════════════════════════════════════════════════════════════
   PHOTO
══════════════════════════════════════════════════════════════ */
function handlePhotoChange(input) {
    if (!input.files?.[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('uploadIdle').classList.add('d-none');
        document.getElementById('uploadPreview').classList.remove('d-none');
    };
    reader.readAsDataURL(input.files[0]);
}

function removePhoto() {
    document.getElementById('photoInput').value = '';
    document.getElementById('previewImg').src = '';
    document.getElementById('uploadIdle').classList.remove('d-none');
    document.getElementById('uploadPreview').classList.add('d-none');
}

/* ══════════════════════════════════════════════════════════════
   COMBOS
══════════════════════════════════════════════════════════════ */
function buildCombo({ searchId, dropdownId, listId, emptyId, hiddenId, badgeId, badgeNameId, clearId, data, depthClass }) {
    const search    = document.getElementById(searchId);
    const dropdown  = document.getElementById(dropdownId);
    const list      = document.getElementById(listId);
    const empty     = document.getElementById(emptyId);
    const hidden    = document.getElementById(hiddenId);
    const badge     = document.getElementById(badgeId);
    const badgeName = document.getElementById(badgeNameId);
    const clear     = document.getElementById(clearId);
    if (!search) return;

    const select = item => {
        hidden.value = item.id;
        search.value = item.nom;
        badgeName.textContent = item.nom;
        badge.classList.remove('d-none');
        clear.classList.remove('d-none');
        dropdown.classList.add('d-none');
        if (badgeId === 'catBadge') document.getElementById('r-cat').textContent = item.nom;
        updateRecap();
    };

    search.addEventListener('input', () => {
        const q = search.value.trim().toLowerCase();
        clear.classList.toggle('d-none', !q);
        if (!q) { dropdown.classList.add('d-none'); return; }
        const filtered = data.filter(i => i.nom.toLowerCase().includes(q));
        list.innerHTML = '';
        empty.classList.toggle('d-none', filtered.length > 0);
        filtered.forEach(item => {
            const div = document.createElement('div');
            div.className = 'combo-item' + (depthClass && item.depth ? ' depth-' + item.depth : '');
            const hi = item.nom.replace(new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi'), '<span class="cmatch">$1</span>');
            div.innerHTML = depthClass
                ? `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>${hi}`
                : `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>${hi}`;
            div.addEventListener('click', () => select(item));
            list.appendChild(div);
        });
        dropdown.classList.remove('d-none');
    });

    clear.addEventListener('click', () => {
        hidden.value = ''; search.value = '';
        badge.classList.add('d-none'); clear.classList.add('d-none');
        dropdown.classList.add('d-none'); search.focus();
    });

    search.addEventListener('keydown', e => { if (e.key === 'Escape') dropdown.classList.add('d-none'); });
    document.addEventListener('click', e => {
        if (!e.target.closest('#' + searchId) && !e.target.closest('#' + dropdownId) && !e.target.closest('#' + clearId))
            dropdown.classList.add('d-none');
    });
}

function preSelect(searchId, hiddenId, badgeId, badgeNameId, clearId, item) {
    document.getElementById(hiddenId).value = item.id;
    document.getElementById(searchId).value = item.nom;
    document.getElementById(badgeNameId).textContent = item.nom;
    document.getElementById(badgeId).classList.remove('d-none');
    document.getElementById(clearId).classList.remove('d-none');
}

/* ══════════════════════════════════════════════════════════════
   STOCK BAR
══════════════════════════════════════════════════════════════ */
function updateStockBar() {
    const stock  = Math.max(0, parseInt(document.getElementById('stock').value) || 0);
    const min    = Math.max(0, parseInt(document.getElementById('quantite_minimale').value) || 0);
    const max    = Math.max(stock, min, 1);
    const pct    = Math.min(100, (stock / max) * 100);
    const minPct = Math.min(99, (min / max) * 100);
    document.getElementById('stockBarFill').style.width    = pct + '%';
    document.getElementById('stockBarMin').style.left      = minPct + '%';
    document.getElementById('stockBarLabel').textContent   = 'Stock: ' + stock + ' pcs';
    document.getElementById('stockBarMinLabel').textContent = 'Min: ' + min;
    document.getElementById('stockBarFill').style.background = stock < min
        ? 'linear-gradient(90deg,var(--red),#ff8a8a)'
        : 'linear-gradient(90deg,var(--green),#6ef7a7)';
    updateRecap();
}

/* ══════════════════════════════════════════════════════════════
   RÉCAP
══════════════════════════════════════════════════════════════ */
function updateRecap() {
    const nom   = document.querySelector('input[name="nom"]').value.trim();
    const stock = document.getElementById('stock').value;
    const prix  = document.querySelector('input[name="prix_achat"]').value;
    const codes = document.querySelectorAll('#barcodeList .bc-code').length;
    document.getElementById('r-nom').textContent   = nom   || '—';
    document.getElementById('r-stock').textContent = stock + ' pcs';
    document.getElementById('r-codes').textContent = codes;
    document.getElementById('r-prix').textContent  = prix  ? prix + ' DA' : '—';
}

/* ══════════════════════════════════════════════════════════════
   NUDGE (boutons +/-)
══════════════════════════════════════════════════════════════ */
function nudge(id, delta) {
    const inp = document.getElementById(id);
    inp.value = Math.max(parseInt(inp.min) || 0, (parseInt(inp.value) || 0) + delta);
    inp.dispatchEvent(new Event('input'));
}

/* ══════════════════════════════════════════════════════════════
   SUBMIT
══════════════════════════════════════════════════════════════ */
function onSubmit(e) {
    let ok = true;

    if (!document.getElementById('categorie_id').value) {
        e.preventDefault(); ok = false;
        document.getElementById('catSearch').focus();
        toast('Sélectionnez une catégorie', 'err');
    }

    const bcs = document.querySelectorAll('#barcodeList .bc-code');
    let bcOk = true;
    bcs.forEach(b => { if (!b.value.trim()) { b.style.borderColor = 'var(--red)'; bcOk = false; } });
    if (!bcOk) { e.preventDefault(); ok = false; toast('Remplissez tous les codes-barres', 'err'); }

    const codes = [...bcs].map(b => b.value.trim().toLowerCase());
    if (new Set(codes).size !== codes.length) { e.preventDefault(); ok = false; toast('Codes-barres en double', 'err'); }

    if (!ok) return;

    const stock = parseInt(document.getElementById('stock').value) || 0;
    const minQ  = parseInt(document.getElementById('quantite_minimale').value) || 0;
    if (stock < minQ && !confirm('⚠️ Stock initial inférieur à la quantité minimale. Continuer ?'))
        e.preventDefault();
}

/* ══════════════════════════════════════════════════════════════
   UTILITAIRES
══════════════════════════════════════════════════════════════ */
function setStatus(msg, loading) {
    const txt = document.getElementById('scanStatusText');
    const sp  = document.querySelector('.scan-spinner');
    if (txt) txt.textContent = msg;
    if (sp)  sp.style.display = loading ? 'block' : 'none';
}

function esc(s) {
    return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

function toast(msg, type = 'ok') {
    const d = document.createElement('div');
    d.className = 'art-toast ' + type;
    d.textContent = msg;
    document.getElementById('toastWrap').appendChild(d);
    setTimeout(() => { d.style.opacity = '0'; d.style.transition = 'opacity .3s'; setTimeout(() => d.remove(), 300); }, 3000);
}
</script>
@stop