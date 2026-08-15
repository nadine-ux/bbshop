@extends('adminlte::page')

@section('title', 'Nouvel Article')

@section('content_header')
{{-- ── Page Header ── --}}
<header class="page-header">
    <div class="header-icon">
        <svg viewBox="0 0 24 24"><path d="M4 6h2v12H4zm3 0h1v12H7zm2 0h2v12H9zm3 0h1v12h-1zm3 0h1v12h-1zm2 0h2v12h-2zM2 4v16a2 2 0 002 2h16a2 2 0 002-2V4a2 2 0 00-2-2H4a2 2 0 00-2 2z"/></svg>
    </div>
    <div class="header-text">
        <h1>Nouvel Article</h1>
        <p>Renseignez les informations et scannez les codes-barres</p>
    </div>
    <a href="{{ route('articles.index') }}" class="header-back">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Retour liste
    </a>
</header>
@stop

@section('content')

{{-- ── Alerts ── --}}
@if($errors->any())
<div class="alert">
    <div class="alert-inner alert-error">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        <ul style="list-style:none; padding:0; margin:0;">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

@if(session('success'))
<div class="alert">
    <div class="alert-inner alert-success">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 12.5L6.5 10 5 11.5l4 4 9-9L16.5 5 9 12.5z"/></svg>
        {{ session('success') }}
    </div>
</div>
@endif

{{-- ════════════════════════════════════════════════
     FORM
════════════════════════════════════════════════ --}}
<form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data" id="articleForm">
@csrf

<div class="form-layout">

    {{-- ════ COLONNE GAUCHE ════ --}}
    <div style="display:flex; flex-direction:column; gap:22px;">

        {{-- ── Informations générales ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-header-icon red">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/></svg>
                </div>
                <div>
                    <div class="card-header-title">Informations Générales</div>
                    <div class="card-header-sub">Identification et classification</div>
                </div>
            </div>
            <div class="card-body">
                <div class="field">
                    <label>Nom de l'article <span class="req">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom') }}"
                           placeholder="Ex: Paracétamol 500mg, Aspirine..."
                           class="{{ $errors->has('nom') ? 'is-invalid' : '' }}"
                           autocomplete="off">
                    @error('nom')<div class="error-msg"><svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><circle cx="12" cy="12" r="10"/><path d="M12 7v5m0 4h.01" stroke="white" stroke-width="2" fill="none"/></svg>{{ $message }}</div>@enderror
                </div>

                <div class="field-row">
                    <div class="field" style="margin:0">
                        <label>Marque</label>
                        <select name="marque_id">
                            <option value="">— Aucune marque —</option>
                            @foreach($marques as $m)
                                <option value="{{ $m->id }}" {{ old('marque_id') == $m->id ? 'selected' : '' }}>{{ $m->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field" style="margin:0">
                        <label>Fournisseur</label>
                        <select name="fournisseur_id">
                            <option value="">— Aucun —</option>
                            @foreach($fournisseurs as $f)
                                <option value="{{ $f->id }}" {{ old('fournisseur_id') == $f->id ? 'selected' : '' }}>{{ $f->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field" style="margin-top:18px">
                    <label>Catégorie parente <span class="req">*</span></label>
                    <select name="parent_categorie_id" id="parentCat" onchange="loadSubcats(this.value)">
                        <option value="">— Sélectionner une catégorie —</option>
                        @foreach($categories->whereNull('parent_id') as $cat)
                            @if(!$cat->parent_id)
                                <option value="{{ $cat->id }}" {{ old('parent_categorie_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>Sous-catégorie <span class="req">*</span></label>
                    <select name="categorie_id" id="subCat" class="{{ $errors->has('categorie_id') ? 'is-invalid' : '' }}">
                        <option value="">— Choisir d'abord une catégorie —</option>
                        @foreach($categories as $cat)
                            @if($cat->parent_id)
                                <option value="{{ $cat->id }}"
                                        data-parent="{{ $cat->parent_id }}"
                                        {{ old('categorie_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nom }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('categorie_id')<div class="error-msg">{{ $message }}</div>@enderror
                </div>

                <div class="field">
                    <label>Description</label>
                    <textarea name="description" placeholder="Informations complémentaires sur l'article...">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── Stock & Prix ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-header-icon amber">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z"/></svg>
                </div>
                <div>
                    <div class="card-header-title">Stock & Tarification</div>
                    <div class="card-header-sub">Quantités et prix</div>
                </div>
            </div>
            <div class="card-body">
                <div class="field-row">
                    <div class="field" style="margin:0">
                        <label>Stock initial <span class="req">*</span></label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0"
                               id="stockInput" oninput="updateStockMeter(this.value)">
                        <div class="stock-meter"><div class="stock-meter-fill" id="stockMeterFill"></div></div>
                    </div>
                    <div class="field" style="margin:0">
                        <label>Quantité minimale <span class="req">*</span></label>
                        <input type="number" name="quantite_minimale" value="{{ old('quantite_minimale', 5) }}" min="0">
                        <div class="input-hint">Alerte de réapprovisionnement</div>
                    </div>
                </div>

                <div class="field" style="margin-top:16px">
                    <label>Contenance carton</label>
                    <input type="number" name="contenance_carton" value="{{ old('contenance_carton') }}" min="1"
                           placeholder="Ex: 12 unités par carton">
                    <div class="input-hint">Laissez vide si pas de conditionnement carton</div>
                </div>

                <div class="section-divider"><span>Prix</span></div>

                <div class="field-row">
                    <div class="field" style="margin:0">
                        <label>Prix d'achat (DA)</label>
                        <input type="number" name="prix_achat" value="{{ old('prix_achat') }}" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="field" style="margin:0">
                        <label>Prix de vente (DA)</label>
                        <input type="number" name="prix_vente" value="{{ old('prix_vente') }}" step="0.01" min="0" placeholder="0.00">
                    </div>
                </div>

                <div class="field" style="margin-top:16px">
                    <label>Date de péremption</label>
                    <input type="date" name="date_peremption" value="{{ old('date_peremption') }}">
                </div>
            </div>
        </div>

    </div>{{-- fin colonne gauche --}}

    {{-- ════ COLONNE DROITE ════ --}}
    <div style="display:flex; flex-direction:column; gap:22px;">

        {{-- ── Scanner de codes-barres ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-header-icon green">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h2v12H4zm3 0h1v12H7zm2 0h2v12H9zm3 0h1v12h-1zm3 0h1v12h-1zm2 0h2v12h-2zM2 4v16a2 2 0 002 2h16a2 2 0 002-2V4a2 2 0 00-2-2H4a2 2 0 00-2 2z"/></svg>
                </div>
                <div>
                    <div class="card-header-title">Codes-Barres</div>
                    <div class="card-header-sub">Scanner ou saisir manuellement</div>
                </div>
            </div>
            <div class="card-body">

                {{-- Zone scanner --}}
                <div class="scanner-area" id="scannerArea">
                    <div id="scannerContainer" style="width:100%;height:100%;display:none;"></div>

                    <div class="scanner-overlay" id="scannerOverlay" style="display:none">
                        <div class="scan-frame">
                            <div class="scan-corner-bl"></div>
                            <div class="scan-corner-tr"></div>
                            <div class="scan-line"></div>
                        </div>
                        <div class="scan-hint-text">PLACEZ LE CODE-BARRES DANS LE CADRE</div>
                    </div>

                    <div class="scanner-idle" id="scannerIdle" onclick="startScanner()">
                        <div class="scan-idle-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h2v12H4zm3 0h1v12H7zm2 0h2v12H9zm3 0h1v12h-1zm3 0h1v12h-1zm2 0h2v12h-2zM2 4v16a2 2 0 002 2h16a2 2 0 002-2V4a2 2 0 00-2-2H4a2 2 0 00-2 2z"/></svg>
                        </div>
                        <div class="scan-idle-label">Activer le Scanner</div>
                        <div class="scan-idle-sub">Cliquez pour ouvrir la caméra</div>
                    </div>
                </div>

                {{-- Contrôles scanner --}}
                <div class="scanner-controls" id="scannerControls" style="display:none">
                    <button type="button" class="btn-scan-toggle-cam" onclick="switchCamera()">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M20 5h-3.17L15 3H9L7.17 5H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                        Changer caméra
                    </button>
                    <button type="button" class="btn-scan-stop" onclick="stopScanner()">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M6 6h12v12H6z"/></svg>
                        Arrêter
                    </button>
                </div>

                {{-- Saisie manuelle --}}
                <div class="section-divider" style="margin-top:14px"><span>Ou saisie manuelle</span></div>
                <div class="scan-add-manual">
                    <input type="text" id="manualBarcodeInput"
                           placeholder="Entrez un code-barres..."
                           style="font-family:'DM Mono',monospace; font-size:.85rem;"
                           onkeydown="if(event.key==='Enter'){event.preventDefault();addBarcodeManual();}">
                    <button type="button" class="btn-add-manual" onclick="addBarcodeManual()">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 13H13v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                        Ajouter
                    </button>
                </div>

                {{-- Liste des codes-barres --}}
                <div class="barcode-list" id="barcodeList" style="margin-top:16px"></div>

                @error('barcodes')
                    <div class="error-msg" style="margin-top:8px">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><circle cx="12" cy="12" r="10"/><path d="M12 7v5m0 4h.01" stroke="white" stroke-width="2" fill="none"/></svg>
                        {{ $message }}
                    </div>
                @enderror
                <div id="barcodesRequiredMsg" class="error-msg" style="display:none; margin-top:8px">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><circle cx="12" cy="12" r="10"/><path d="M12 7v5m0 4h.01" stroke="white" stroke-width="2" fill="none"/></svg>
                    Au moins un code-barres est requis.
                </div>
            </div>
        </div>

        {{-- ── Photo ── --}}
        <div class="card">
            <div class="card-header">
                <div class="card-header-icon blue">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                </div>
                <div>
                    <div class="card-header-title">Photo de l'article</div>
                    <div class="card-header-sub">JPEG, PNG, WEBP — max 2 Mo</div>
                </div>
            </div>
            <div class="card-body">
                <div class="photo-drop" id="photoDrop" ondragover="handleDragOver(event)" ondrop="handleDrop(event)">
                    <input type="file" name="photo" accept="image/*" id="photoInput" onchange="previewPhoto(this)">
                    <div class="photo-drop-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    </div>
                    <p><strong>Glissez une image</strong> ou cliquez pour parcourir</p>
                    <img id="photo-preview" src="#" alt="Prévisualisation">
                </div>
            </div>
        </div>

    </div>{{-- fin colonne droite --}}

    {{-- ── Barre de soumission ── --}}
    <div class="submit-bar">
        <div class="submit-info">
            Champs marqués <span>*</span> obligatoires — <span id="barcodeCount">0</span> code(s)-barres enregistré(s)
        </div>
        <div class="submit-actions">
            <a href="{{ route('articles.index') }}" class="btn-cancel">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                Annuler
            </a>
            <button type="submit" class="btn-submit" onclick="return validateForm()">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Enregistrer l'article
            </button>
        </div>
    </div>

</div>{{-- form-layout --}}
</form>

@stop

@section('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@400;500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

<style>
:root {
    --ink:        #0c0c0f;
    --ink-soft:   #3a3a4a;
    --ink-muted:  #7a7a90;
    --paper:      #f5f4f0;
    --paper-warm: #edecea;
    --surface:    #ffffff;
    --accent:     #e84f3c;
    --accent-2:   #f5a623;
    --scan-green: #00e676;
    --scan-dim:   #00c853;
    --border:     #e0dfd9;
    --radius:     10px;
    --shadow:     0 2px 12px rgba(0,0,0,.08), 0 1px 3px rgba(0,0,0,.06);
    --shadow-lg:  0 8px 32px rgba(0,0,0,.13), 0 2px 8px rgba(0,0,0,.08);
}

* { box-sizing: border-box; }

.page-header {
    background: var(--ink);
    color: white;
    padding: 28px 40px 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    border-bottom: 3px solid var(--accent);
    position: relative;
    overflow: hidden;
    margin: -10px -15px 0;
}
.page-header::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 180px; height: 180px;
    border: 2px solid rgba(255,255,255,.06);
    border-radius: 50%;
}
.page-header::after {
    content: '';
    position: absolute;
    top: -20px; right: -20px;
    width: 100px; height: 100px;
    border: 2px solid rgba(232,79,60,.25);
    border-radius: 50%;
}
.header-icon {
    width: 52px; height: 52px;
    background: var(--accent);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.header-icon svg { width: 26px; height: 26px; fill: white; }
.header-text h1 {
    font-family: 'Syne', sans-serif;
    font-size: 1.6rem; font-weight: 800;
    letter-spacing: -.01em;
    line-height: 1.1;
    margin: 0;
}
.header-text p { font-size: .82rem; color: rgba(255,255,255,.5); margin-top: 4px; margin-bottom: 0; }
.header-back {
    margin-left: auto;
    display: flex; align-items: center; gap: 8px;
    color: rgba(255,255,255,.6);
    text-decoration: none;
    font-size: .82rem;
    font-family: 'DM Mono', monospace;
    padding: 8px 16px;
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 6px;
    transition: all .2s;
    position: relative; z-index: 1;
}
.header-back:hover { color: white; border-color: rgba(255,255,255,.4); background: rgba(255,255,255,.07); }

.form-layout {
    max-width: 1080px;
    margin: 28px auto;
    padding: 0 0 40px;
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
    align-items: start;
}
@media (max-width: 860px) {
    .form-layout { grid-template-columns: 1fr; }
    .page-header { padding: 20px 20px 18px; margin: -10px -10px 0; }
}

.form-layout .card {
    background: var(--surface);
    border: 1px solid var(--border) !important;
    border-radius: var(--radius) !important;
    box-shadow: var(--shadow) !important;
    overflow: hidden;
    animation: slideUp .35s cubic-bezier(.22,1,.36,1) both;
    margin-bottom: 0 !important;
}
.form-layout .card:nth-child(2) { animation-delay: .06s; }
.form-layout .card:nth-child(3) { animation-delay: .12s; }
.form-layout .card:nth-child(4) { animation-delay: .18s; }

@keyframes slideUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

.form-layout .card-header {
    padding: 16px 20px 14px !important;
    border-bottom: 1px solid var(--border) !important;
    display: flex !important; align-items: center; gap: 12px;
    background: var(--paper-warm) !important;
}
.card-header-icon {
    width: 34px; height: 34px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.card-header-icon.red   { background: rgba(232,79,60,.12); color: var(--accent); }
.card-header-icon.amber { background: rgba(245,166,35,.15); color: var(--accent-2); }
.card-header-icon.green { background: rgba(0,200,83,.12);  color: var(--scan-dim); }
.card-header-icon.blue  { background: rgba(66,133,244,.12); color: #4285f4; }
.card-header-icon svg   { width: 17px; height: 17px; }

.card-header-title {
    font-family: 'Syne', sans-serif;
    font-size: .95rem; font-weight: 700;
    letter-spacing: .01em;
    margin: 0;
}
.card-header-sub { font-size: .72rem; color: var(--ink-muted); margin-top: 1px; }
.form-layout .card-body { padding: 20px !important; }

.field { margin-bottom: 16px; }
.field:last-child { margin-bottom: 0; }
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

.form-layout label {
    display: block;
    font-size: .75rem; font-weight: 600;
    letter-spacing: .04em; text-transform: uppercase;
    color: var(--ink-soft);
    margin-bottom: 6px;
}
label .req { color: var(--accent); margin-left: 2px; }

.form-layout input[type=text],
.form-layout input[type=number],
.form-layout input[type=date],
.form-layout select,
.form-layout textarea {
    width: 100%;
    padding: 9px 13px;
    border: 1.5px solid var(--border) !important;
    border-radius: 7px !important;
    font-family: 'DM Sans', sans-serif;
    font-size: .87rem;
    color: var(--ink);
    background: white !important;
    transition: border-color .18s, box-shadow .18s;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    box-shadow: none !important;
}
.form-layout input:focus,
.form-layout select:focus,
.form-layout textarea:focus {
    border-color: var(--accent) !important;
    box-shadow: 0 0 0 3px rgba(232,79,60,.1) !important;
}
.form-layout select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%237a7a90' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 12px center !important;
    padding-right: 36px;
}
.form-layout textarea { resize: vertical; min-height: 78px; }
.input-hint { font-size: .7rem; color: var(--ink-muted); margin-top: 4px; }
.is-invalid { border-color: var(--accent) !important; background: rgba(232,79,60,.03) !important; }
.error-msg { font-size: .72rem; color: var(--accent); margin-top: 5px; display: flex; align-items: center; gap: 4px; }

.photo-drop {
    border: 2px dashed var(--border);
    border-radius: var(--radius);
    padding: 26px 18px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    position: relative;
}
.photo-drop:hover, .photo-drop.drag-over {
    border-color: var(--accent);
    background: rgba(232,79,60,.03);
}
.photo-drop input[type=file] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
}
.photo-drop-icon {
    width: 42px; height: 42px;
    background: var(--paper);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 10px;
}
.photo-drop-icon svg { width: 20px; height: 20px; color: var(--ink-muted); }
.photo-drop p { font-size: .78rem; color: var(--ink-muted); line-height: 1.5; margin: 0; }
.photo-drop strong { color: var(--accent); font-weight: 600; }
#photo-preview {
    display: none; width: 100%; max-height: 150px;
    object-fit: contain; border-radius: 7px;
    margin-top: 12px; border: 1px solid var(--border);
}

/* Scanner */
.scanner-area {
    position: relative;
    background: #0a0a0a;
    border-radius: 10px;
    overflow: hidden;
    aspect-ratio: 4/3;
    border: 2px solid #1a1a1a;
}
.scanner-area video { width: 100%; height: 100%; object-fit: cover; display: block; }
.scanner-overlay {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    pointer-events: none;
}
.scan-frame { width: 72%; height: 55%; position: relative; }
.scan-frame::before, .scan-frame::after,
.scan-corner-tr, .scan-corner-bl {
    content: '';
    position: absolute;
    width: 28px; height: 28px;
    border-color: var(--scan-green);
    border-style: solid;
}
.scan-frame::before  { top: 0;    left: 0;  border-width: 3px 0 0 3px; border-radius: 3px 0 0 0; }
.scan-frame::after   { top: 0;    right: 0; border-width: 3px 3px 0 0; border-radius: 0 3px 0 0; }
.scan-corner-bl      { bottom: 0; left: 0;  border-width: 0 0 3px 3px; border-radius: 0 0 0 3px; }
.scan-corner-tr      { bottom: 0; right: 0; border-width: 0 3px 3px 0; border-radius: 0 0 3px 0; }
.scan-line {
    position: absolute; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, transparent, var(--scan-green), transparent);
    top: 10%;
    animation: scanMove 2.2s ease-in-out infinite;
    box-shadow: 0 0 8px var(--scan-green), 0 0 18px rgba(0,230,118,.4);
}
@keyframes scanMove {
    0%,100% { top: 10%; }
    50%      { top: 85%; }
}
.scan-hint-text {
    position: absolute; bottom: 10px; left: 0; right: 0;
    text-align: center;
    font-family: 'DM Mono', monospace;
    font-size: .66rem; color: rgba(255,255,255,.55); letter-spacing: .06em;
}
.scanner-idle {
    position: absolute; inset: 0;
    background: #0a0a0a;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 12px;
    cursor: pointer; transition: background .2s;
}
.scanner-idle:hover { background: #111; }
.scan-idle-icon {
    width: 60px; height: 60px;
    border: 2px solid rgba(0,230,118,.3);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    position: relative;
}
.scan-idle-icon svg { width: 28px; height: 28px; color: var(--scan-green); }
.scan-idle-icon::before {
    content: '';
    position: absolute; inset: -6px;
    border: 1px solid rgba(0,230,118,.1);
    border-radius: 16px;
    animation: pulse 2s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .4; transform: scale(1.08); }
}
.scan-idle-label {
    font-family: 'Syne', sans-serif;
    font-size: .82rem; font-weight: 700;
    color: rgba(255,255,255,.7); letter-spacing: .02em;
}
.scan-idle-sub { font-size: .67rem; color: rgba(255,255,255,.35); font-family: 'DM Mono', monospace; }

.scanner-controls { display: flex; gap: 8px; margin-top: 10px; }
.btn-scan-stop, .btn-scan-toggle-cam {
    flex: 1; padding: 9px; border: none; border-radius: 7px;
    font-family: 'DM Sans', sans-serif; font-size: .8rem; font-weight: 600;
    cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;
    transition: all .18s;
}
.btn-scan-stop { background: rgba(232,79,60,.12); color: var(--accent); border: 1px solid rgba(232,79,60,.2); }
.btn-scan-stop:hover { background: rgba(232,79,60,.2); }
.btn-scan-toggle-cam { background: var(--paper); color: var(--ink-soft); border: 1px solid var(--border); }
.btn-scan-toggle-cam:hover { background: var(--paper-warm); }

.barcode-list { display: flex; flex-direction: column; gap: 10px; margin-top: 14px; }
.barcode-item {
    display: flex; align-items: center; gap: 10px;
    background: var(--paper); border: 1.5px solid var(--border);
    border-radius: 8px; padding: 10px 12px;
    animation: itemIn .25s cubic-bezier(.22,1,.36,1) both;
}
.barcode-item.is-primary { border-color: var(--scan-dim); background: rgba(0,200,83,.04); }
@keyframes itemIn {
    from { opacity: 0; transform: translateX(-10px); }
    to   { opacity: 1; transform: translateX(0); }
}
.barcode-item-code {
    font-family: 'DM Mono', monospace; font-size: .82rem; font-weight: 500;
    color: var(--ink); flex: 1; border: none; background: transparent; padding: 0; outline: none;
}
.barcode-item-code:focus { color: var(--accent); }
.barcode-item-label-input {
    width: 100px; padding: 4px 8px;
    border: 1px solid var(--border); border-radius: 5px;
    font-size: .74rem; color: var(--ink-soft); background: white; outline: none;
}
.barcode-item-label-input:focus { border-color: var(--accent); }
.barcode-item-label-input::placeholder { color: var(--ink-muted); }
.badge-primary {
    font-size: .62rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
    background: var(--scan-dim); color: white; padding: 2px 7px; border-radius: 4px; flex-shrink: 0;
}
.btn-set-primary {
    background: none; border: none; cursor: pointer; color: var(--ink-muted); font-size: .68rem;
    display: flex; align-items: center; gap: 3px; padding: 3px 6px; border-radius: 4px;
    white-space: nowrap; transition: all .15s;
}
.btn-set-primary:hover { color: var(--scan-dim); background: rgba(0,200,83,.08); }
.btn-remove-barcode {
    width: 28px; height: 28px; flex-shrink: 0; background: none; border: none;
    cursor: pointer; border-radius: 6px; display: flex; align-items: center; justify-content: center;
    color: var(--ink-muted); transition: all .15s;
}
.btn-remove-barcode:hover { background: rgba(232,79,60,.1); color: var(--accent); }
.btn-remove-barcode svg { width: 14px; height: 14px; }

.scan-add-manual { display: flex; gap: 8px; margin-top: 10px; }
.scan-add-manual input { flex: 1; }
.btn-add-manual {
    padding: 10px 16px; background: var(--ink); color: white; border: none;
    border-radius: 7px; font-weight: 600; font-size: .82rem; cursor: pointer;
    white-space: nowrap; transition: background .15s;
    display: flex; align-items: center; gap: 6px;
}
.btn-add-manual:hover { background: #222; }
.btn-add-manual svg { width: 14px; height: 14px; }

.scan-flash {
    position: absolute; inset: 0; background: rgba(0,230,118,.15);
    border-radius: 10px; animation: flashIn .6s ease-out both; pointer-events: none;
}
@keyframes flashIn { 0% { opacity: 1; } 100% { opacity: 0; } }

/* Submit bar */
.submit-bar {
    grid-column: 1 / -1;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 18px 24px;
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
    box-shadow: var(--shadow);
}
.submit-info { font-size: .78rem; color: var(--ink-muted); }
.submit-info span { color: var(--accent); font-weight: 600; }
.submit-actions { display: flex; gap: 12px; }
.btn-cancel {
    padding: 10px 20px; background: white; border: 1.5px solid var(--border);
    border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: .85rem; font-weight: 500;
    cursor: pointer; color: var(--ink-soft); text-decoration: none;
    display: flex; align-items: center; gap: 7px; transition: all .18s;
}
.btn-cancel:hover { border-color: var(--ink-soft); color: var(--ink); }
.btn-submit {
    padding: 11px 28px; background: var(--accent); border: none; border-radius: 8px;
    font-family: 'Syne', sans-serif; font-size: .9rem; font-weight: 700;
    cursor: pointer; color: white; display: flex; align-items: center; gap: 8px;
    transition: all .2s; letter-spacing: .01em; position: relative; overflow: hidden;
}
.btn-submit::before {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,.15) 0%, transparent 60%);
}
.btn-submit:hover { background: #d43f2d; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(232,79,60,.35); }
.btn-submit:active { transform: translateY(0); }
.btn-submit svg { width: 17px; height: 17px; }

/* Alerts */
.alert { padding: 0; margin-bottom: 16px; }
.alert-inner {
    padding: 13px 16px; border-radius: 8px; font-size: .83rem;
    display: flex; align-items: flex-start; gap: 10px; border-left: 4px solid;
}
.alert-inner svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }
.alert-error  { background: rgba(232,79,60,.07); border-color: var(--accent); color: #c0392b; }
.alert-success { background: rgba(0,200,83,.07); border-color: var(--scan-dim); color: #1a8a47; }

/* Stock meter */
.stock-meter { height: 4px; background: var(--border); border-radius: 4px; margin-top: 8px; overflow: hidden; }
.stock-meter-fill {
    height: 100%; border-radius: 4px;
    background: linear-gradient(90deg, #4caf50, #8bc34a);
    transition: width .4s ease; width: 0%;
}

/* Section divider */
.section-divider { display: flex; align-items: center; gap: 12px; margin: 6px 0 14px; }
.section-divider span {
    font-size: .69rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .07em; color: var(--ink-muted); white-space: nowrap;
}
.section-divider::before, .section-divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }

/* Tooltip */
[data-tooltip] { position: relative; }
[data-tooltip]::after {
    content: attr(data-tooltip);
    position: absolute; bottom: calc(100% + 6px); left: 50%; transform: translateX(-50%);
    background: var(--ink); color: white; font-size: .68rem; white-space: nowrap;
    padding: 4px 8px; border-radius: 5px;
    pointer-events: none; opacity: 0; transition: opacity .18s; z-index: 9999;
}
[data-tooltip]:hover::after { opacity: 1; }

/* html5-qrcode override */
#scannerContainer > div:first-child {
    border: none !important;
    box-shadow: none !important;
}
#scannerContainer video {
    border-radius: 10px !important;
    object-fit: cover !important;
}
#scannerContainer img[src*="scanning"] {
    display: none !important;
}
</style>
@stop

@section('js')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
/* ══════════════════════════════════════════
   STATE
══════════════════════════════════════════ */
let barcodes        = [];
let scanActive      = false;
let html5QrCode     = null;
let currentCamera   = null;
let cameras         = [];

/* ══════════════════════════════════════════
   RENDER  (CORRIGÉ — template literals propres)
══════════════════════════════════════════ */
function renderBarcodes() {
    const list  = document.getElementById('barcodeList');
    const count = document.getElementById('barcodeCount');
    list.innerHTML = '';
    count.textContent = barcodes.length;

    if (barcodes.length === 0) {
        list.innerHTML = '<div style="text-align:center;padding:16px;color:var(--ink-muted);font-size:.78rem;font-family:DM Mono,monospace;border:1px dashed var(--border);border-radius:8px;">Aucun code-barres — scannez ou saisissez</div>';
        renderHiddenBarcodes();
        return;
    }

    barcodes.forEach((b, i) => {
        const item = document.createElement('div');
        item.className = 'barcode-item' + (b.isPrimary ? ' is-primary' : '');
        item.innerHTML =
            '<input type="text" class="barcode-item-code" value="' + escHtml(b.code) + '"' +
            ' oninput="updateCode(' + i + ', this.value)" placeholder="Code-barres">' +
            '<input type="text" class="barcode-item-label-input" value="' + escHtml(b.label || '') + '"' +
            ' oninput="updateLabel(' + i + ', this.value)" placeholder="Libellé…">' +
            (b.isPrimary
                ? '<span class="badge-primary">Principal</span>'
                : '<button type="button" class="btn-set-primary" onclick="setPrimary(' + i + ')">' +
                  '<svg viewBox="0 0 24 24" width="11" height="11" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>' +
                  ' Principal' +
                  '</button>') +
            '<button type="button" class="btn-remove-barcode" onclick="removeBarcode(' + i + ')">' +
            '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>' +
            '</button>';
        list.appendChild(item);
    });
    renderHiddenBarcodes();
}

function renderHiddenBarcodes() {
    document.querySelectorAll('.bc-hidden-group').forEach(el => el.remove());
    barcodes.forEach((b, i) => {
        const g = document.createElement('div');
        g.className = 'bc-hidden-group';
        g.style.display = 'none';
        g.innerHTML =
            '<input type="hidden" name="barcodes[' + i + '][code]"    value="' + escHtml(b.code) + '">' +
            '<input type="hidden" name="barcodes[' + i + '][label]"   value="' + escHtml(b.label || '') + '">' +
            '<input type="hidden" name="barcodes[' + i + '][primary]" value="' + (b.isPrimary ? '1' : '0') + '">';
        document.getElementById('articleForm').appendChild(g);
    });
}

/* ══════════════════════════════════════════
   BARCODE OPERATIONS
══════════════════════════════════════════ */
function addBarcode(code, fromScan) {
    const clean = code.trim();
    if (!clean) return;
    if (barcodes.find(b => b.code === clean)) {
        if (fromScan) flashArea('rgba(232,79,60,.15)');
        return;
    }
    barcodes.push({ code: clean, label: '', isPrimary: barcodes.length === 0 });
    renderBarcodes();
    if (fromScan) { flashArea('rgba(0,230,118,.15)'); playScanBeep(); }
    document.getElementById('barcodesRequiredMsg').style.display = 'none';
}

function removeBarcode(idx) {
    const wasPrimary = barcodes[idx].isPrimary;
    barcodes.splice(idx, 1);
    if (wasPrimary && barcodes.length > 0) barcodes[0].isPrimary = true;
    renderBarcodes();
}
function setPrimary(idx) {
    barcodes.forEach((b, i) => b.isPrimary = (i === idx));
    renderBarcodes();
}
function updateCode(idx, val)  { barcodes[idx].code  = val; renderHiddenBarcodes(); }
function updateLabel(idx, val) { barcodes[idx].label = val; renderHiddenBarcodes(); }

function flashArea(color) {
    const area  = document.getElementById('scannerArea');
    const flash = document.createElement('div');
    flash.className = 'scan-flash';
    flash.style.background = color;
    area.appendChild(flash);
    setTimeout(() => flash.remove(), 700);
}

/* ══════════════════════════════════════════
   MANUAL INPUT
══════════════════════════════════════════ */
function addBarcodeManual() {
    const inp = document.getElementById('manualBarcodeInput');
    addBarcode(inp.value);
    inp.value = '';
    inp.focus();
}

/* ══════════════════════════════════════════
   SCANNER — html5-qrcode (ultra robuste)
══════════════════════════════════════════ */
async function startScanner() {
    if (scanActive) return;

    if (typeof Html5Qrcode === 'undefined') {
        alert('Librairie scanner non chargée. Vérifiez votre connexion et rechargez la page.');
        return;
    }

    try {
        /* 1. Permissions + lister caméras */
        cameras = await Html5Qrcode.getCameras();
        if (!cameras || cameras.length === 0) {
            alert('Aucune caméra détectée sur cet appareil.');
            return;
        }

        /* 2. Choisir caméra arrière par défaut */
        let camId = cameras[0].id;
        const backCam = cameras.find(c => /back|rear|environment/i.test(c.label));
        if (backCam) camId = backCam.id;
        currentCamera = camId;

        /* 3. UI */
        document.getElementById('scannerIdle').style.display    = 'none';
        document.getElementById('scannerOverlay').style.display   = 'flex';
        document.getElementById('scannerControls').style.display  = 'flex';
        document.getElementById('scannerContainer').style.display = 'block';

        /* 4. Démarrer html5-qrcode */
        html5QrCode = new Html5Qrcode('scannerContainer');
        scanActive = true;

        await html5QrCode.start(
            camId,
            {
                fps: 10,
                qrbox: { width: 250, height: 180 },
                aspectRatio: 1.333,
                disableFlip: false
            },
            (decodedText) => {
                /* Succès scan */
                addBarcode(decodedText, true);
            },
            () => {
                /* Pas de code dans ce frame — on ignore silencieusement */
            }
        );

    } catch (e) {
        console.error(e);
        alert('Impossible d\'accéder à la caméra : ' + (e.message || 'Permission refusée'));
        stopScanner();
    }
}

function stopScanner() {
    if (html5QrCode && scanActive) {
        html5QrCode.stop().then(() => {
            html5QrCode = null;
        }).catch(() => {
            html5QrCode = null;
        });
    }
    scanActive = false;
    document.getElementById('scannerIdle').style.display     = 'flex';
    document.getElementById('scannerOverlay').style.display  = 'none';
    document.getElementById('scannerControls').style.display = 'none';
    document.getElementById('scannerContainer').style.display = 'none';
}

async function switchCamera() {
    if (!cameras || cameras.length <= 1) {
        alert('Une seule caméra disponible.');
        return;
    }
    const idx = cameras.findIndex(c => c.id === currentCamera);
    const next = cameras[(idx + 1) % cameras.length];
    stopScanner();
    currentCamera = next.id;
    await new Promise(r => setTimeout(r, 400));
    await startScanner();
}

/* ══════════════════════════════════════════
   BEEP
══════════════════════════════════════════ */
function playScanBeep() {
    try {
        const ctx  = new (window.AudioContext || window.webkitAudioContext)();
        const osc  = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.frequency.setValueAtTime(1047, ctx.currentTime);
        osc.frequency.setValueAtTime(1319, ctx.currentTime + 0.05);
        gain.gain.setValueAtTime(.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(.001, ctx.currentTime + .18);
        osc.start(ctx.currentTime); osc.stop(ctx.currentTime + .18);
    } catch(e) {}
}

/* ══════════════════════════════════════════
   SUBCATEGORIES (cascade sans AJAX)
══════════════════════════════════════════ */
function loadSubcats(parentId) {
    const sub = document.getElementById('subCat');
    sub.innerHTML = '<option value="">— Sélectionner —</option>';
    if (!parentId) return;
    document.querySelectorAll('#subCatData option').forEach(opt => {
        if (opt.dataset.parent == parentId) sub.appendChild(new Option(opt.textContent, opt.value));
    });
}

/* ══════════════════════════════════════════
   PHOTO
══════════════════════════════════════════ */
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const p = document.getElementById('photo-preview');
            p.src = e.target.result; p.style.display = 'block';
            document.querySelector('.photo-drop p').style.opacity = '.5';
            document.querySelector('.photo-drop-icon').style.opacity = '.4';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function handleDragOver(e) { e.preventDefault(); document.getElementById('photoDrop').classList.add('drag-over'); }
function handleDrop(e) {
    e.preventDefault(); document.getElementById('photoDrop').classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const dt = new DataTransfer(); dt.items.add(file);
        document.getElementById('photoInput').files = dt.files;
        previewPhoto(document.getElementById('photoInput'));
    }
}

/* ══════════════════════════════════════════
   STOCK METER
══════════════════════════════════════════ */
function updateStockMeter(val) {
    document.getElementById('stockMeterFill').style.width =
        Math.max(0, Math.min(parseInt(val)||0, 500) / 500 * 100) + '%';
}

/* ══════════════════════════════════════════
   VALIDATION
══════════════════════════════════════════ */
function validateForm() {
    if (barcodes.length === 0) {
        document.getElementById('barcodesRequiredMsg').style.display = 'flex';
        document.getElementById('barcodeList').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
    return true;
}

/* ══════════════════════════════════════════
   UTILS
══════════════════════════════════════════ */
function escHtml(str) {
    return String(str).replace(/[&<>"']/g, m =>
        ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}

/* ══════════════════════════════════════════
   INIT
══════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    /* Store caché pour les sous-catégories */
    const store  = document.createElement('select');
    store.id     = 'subCatData';
    store.style.display = 'none';
    Array.from(document.getElementById('subCat').options).forEach(o => {
        if (o.dataset.parent) store.appendChild(o.cloneNode(true));
    });
    document.body.appendChild(store);

    /* Pré-sélection si old() */
    @if(old('parent_categorie_id'))
        loadSubcats('{{ old("parent_categorie_id") }}');
        document.getElementById('subCat').value = '{{ old("categorie_id") }}';
    @endif

    updateStockMeter(document.getElementById('stockInput').value);

    /* Recharger les barcodes après erreur de validation */
    @if(old('barcodes'))
        @foreach(old('barcodes') as $i => $bc)
            barcodes.push({
                code: '{{ addslashes($bc["code"] ?? "") }}',
                label: '{{ addslashes($bc["label"] ?? "") }}',
                isPrimary: {{ !empty($bc['primary']) ? 'true' : ($i === 0 ? 'true' : 'false') }}
            });
        @endforeach
        renderBarcodes();
    @endif
});

window.addEventListener('beforeunload', stopScanner);
</script>
@stop