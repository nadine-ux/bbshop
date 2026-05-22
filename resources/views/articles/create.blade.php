
@extends('adminlte::page')

@section('title', 'Créer un article')

@section('content_header')
    <div class="header-modern">
        <div class="header-left">
            <a href="{{ route('articles.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="header-info">
                <h1 class="page-title">
                    <i class="fas fa-box"></i>
                    Créer un nouvel article
                </h1>
                <p class="page-subtitle">Ajouter un article au stock</p>
            </div>
        </div>
    </div>
@stop

@section('content')

<form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row">
        {{-- Colonne gauche --}}
        <div class="col-md-6">

            {{-- Informations de base --}}
            <div class="form-card-modern">
                <h5 class="card-section-title">
                    <i class="fas fa-info-circle"></i>
                    Informations de base
                </h5>

                {{-- Nom --}}
                <div class="form-group-modern">
                    <label>
                        <i class="fas fa-tag"></i> Nom de l'article <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="nom"
                           class="form-control-modern @error('nom') is-invalid @enderror"
                           placeholder="Ex: Coca-Cola 1.5L, Samsung Galaxy A54..."
                           value="{{ old('nom') }}"
                           required>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Codes-barres --}}
                <div class="form-group-modern">
                    <label>
                        <i class="fas fa-barcode"></i> Codes-barres <span class="text-danger">*</span>
                    </label>

                    <div id="barcodeList"></div>

                    <button type="button" class="btn-add-barcode" id="btnAddBarcode">
                        <i class="fas fa-plus"></i> Ajouter un code-barres
                    </button>

                    <small class="form-text text-muted mt-1">
                        <i class="fas fa-info-circle"></i>
                        Le code marqué <strong>Principal</strong> sera utilisé par défaut.
                    </small>
                </div>

                {{-- Catégorie --}}
                <div class="form-group-modern">
                    <label>
                        <i class="fas fa-tags"></i> Catégorie <span class="text-danger">*</span>
                    </label>
                    <input type="hidden" name="categorie_id" id="categorie_id" value="{{ old('categorie_id') }}" required>
                    <div class="category-search-wrapper">
                        <div class="category-search-input-row">
                            <i class="fas fa-search category-search-icon"></i>
                            <input type="text" id="categorieSearch"
                                   class="form-control-modern category-search-input @error('categorie_id') is-invalid @enderror"
                                   placeholder="Rechercher une catégorie..."
                                   autocomplete="off">
                            <button type="button" class="btn-clear-category d-none" id="btnClearCategory" title="Effacer">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div id="categoryDropdown" class="category-dropdown d-none">
                            <div id="categoryList"></div>
                            <div id="categoryEmpty" class="category-empty d-none">
                                <i class="fas fa-search"></i> Aucune catégorie trouvée
                            </div>
                        </div>
                        <div id="categorySelected" class="category-selected-badge d-none">
                            <i class="fas fa-check-circle"></i>
                            <span id="categorySelectedName"></span>
                        </div>
                    </div>
                    @error('categorie_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Marque --}}
                <div class="form-group-modern">
                    <label>
                        <i class="fas fa-certificate"></i> Marque <span class="text-muted">(Optionnel)</span>
                    </label>
                    <input type="hidden" name="marque_id" id="marque_id" value="{{ old('marque_id') }}">
                    <div class="category-search-wrapper">
                        <div class="category-search-input-row">
                            <i class="fas fa-search category-search-icon"></i>
                            <input type="text" id="marqueSearch"
                                   class="form-control-modern category-search-input @error('marque_id') is-invalid @enderror"
                                   placeholder="Rechercher une marque..."
                                   autocomplete="off">
                            <button type="button" class="btn-clear-category d-none" id="btnClearMarque" title="Effacer">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div id="marqueDropdown" class="category-dropdown d-none">
                            <div id="marqueList"></div>
                            <div id="marqueEmpty" class="category-empty d-none">
                                <i class="fas fa-search"></i> Aucune marque trouvée
                            </div>
                        </div>
                        <div id="marqueSelected" class="category-selected-badge d-none">
                            <i class="fas fa-check-circle"></i>
                            <span id="marqueSelectedName"></span>
                        </div>
                    </div>
                    @error('marque_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="form-group-modern">
                    <label>
                        <i class="fas fa-align-left"></i> Description <span class="text-muted">(Optionnel)</span>
                    </label>
                    <textarea name="description"
                              class="form-control-modern @error('description') is-invalid @enderror"
                              rows="3"
                              placeholder="Décrivez l'article...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Photo --}}
            <div class="form-card-modern">
                <h5 class="card-section-title">
                    <i class="fas fa-image"></i>
                    Photo de l'article
                </h5>
                <div class="upload-area" id="uploadArea">
                    <input type="file" name="photo" id="photoInput" class="d-none"
                           accept="image/*" onchange="previewImage(this)">
                    <div class="upload-placeholder" id="uploadPlaceholder">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Cliquez pour télécharger une photo</p>
                        <small>JPG, PNG, GIF - Max 2MB</small>
                    </div>
                    <div class="upload-preview d-none" id="uploadPreview">
                        <img id="previewImg" src="" alt="Preview">
                        <button type="button" class="btn-remove-img" onclick="removeImage()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne droite --}}
        <div class="col-md-6">

            {{-- Stock --}}
            <div class="form-card-modern">
                <h5 class="card-section-title">
                    <i class="fas fa-warehouse"></i>
                    Stock et quantités
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label><i class="fas fa-boxes"></i> Stock initial (pièces) <span class="text-danger">*</span></label>
                            <input type="number" name="stock"
                                   class="form-control-modern @error('stock') is-invalid @enderror"
                                   min="0" value="{{ old('stock', 0) }}" required>
                            @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label><i class="fas fa-exclamation-triangle"></i> Quantité minimale <span class="text-danger">*</span></label>
                            <input type="number" name="quantite_minimale"
                                   class="form-control-modern @error('quantite_minimale') is-invalid @enderror"
                                   min="0" value="{{ old('quantite_minimale') }}" required>
                            @error('quantite_minimale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">Seuil d'alerte</small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group-modern">
                            <label><i class="fas fa-box-open"></i> Contenance carton (pièces)</label>
                            <input type="number" name="contenance_carton"
                                   class="form-control-modern @error('contenance_carton') is-invalid @enderror"
                                   min="1" value="{{ old('contenance_carton', 1) }}">
                            @error('contenance_carton')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted"><i class="fas fa-info-circle"></i> Nombre de pièces par carton</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Prix --}}
            <div class="form-card-modern">
                <h5 class="card-section-title">
                    <i class="fas fa-dollar-sign"></i>
                    Prix et dates
                </h5>
                <div class="form-group-modern">
                    <label><i class="fas fa-money-bill-wave"></i> Prix d'achat (DZD) <span class="text-muted">(Optionnel)</span></label>
                    <input type="number" step="0.01" name="prix_achat"
                           class="form-control-modern @error('prix_achat') is-invalid @enderror"
                           placeholder="0.00" value="{{ old('prix_achat') }}">
                    @error('prix_achat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group-modern">
                    <label><i class="fas fa-calendar-times"></i> Date de péremption <span class="text-muted">(Optionnel)</span></label>
                    <input type="date" name="date_peremption"
                           class="form-control-modern @error('date_peremption') is-invalid @enderror"
                           value="{{ old('date_peremption') }}">
                    @error('date_peremption')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="form-actions-fixed">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="actions-wrapper">
                        <a href="{{ route('articles.index') }}" class="btn-cancel">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Enregistrer l'article
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- ═══════════════════════════════════════════════
     MODAL SCANNER — en dehors du form pour éviter
     tout conflit de soumission accidentelle
════════════════════════════════════════════════ --}}
<div id="scanModal" class="scan-modal d-none">
    <div class="scan-box">

        {{-- Header --}}
        <div class="scan-header">
            <span><i class="fas fa-barcode"></i> Scanner un code-barres</span>
            <button type="button" id="btnCloseScan" class="btn-scan-close">
                <i class="fas fa-times"></i> Fermer
            </button>
        </div>

        {{-- Choix méthode --}}
        <div class="scan-methods" id="scanMethods">
            <button type="button" class="btn-method" id="btnMethodCamera" onclick="scanStart('camera')">
                <i class="fas fa-camera"></i><br>Caméra
            </button>
            <button type="button" class="btn-method" id="btnMethodFile" onclick="scanStart('file')">
                <i class="fas fa-file-image"></i><br>Image
            </button>
            <button type="button" class="btn-method" id="btnMethodManual" onclick="scanStart('manual')">
                <i class="fas fa-keyboard"></i><br>Manuel
            </button>
        </div>

        {{-- Section caméra --}}
        <div id="scanCamSection" class="d-none">
            <div class="scan-viewport">
                <video id="scanVideo" playsinline autoplay muted></video>
                <div class="scan-overlay">
                    <div class="scan-frame"><div class="scan-line"></div></div>
                    <p class="scan-hint">Pointez vers le code-barres</p>
                </div>
            </div>
            <div id="scanStatus" class="scan-status">
                <i class="fas fa-spinner fa-spin"></i> Démarrage caméra...
            </div>
        </div>

        {{-- Section image --}}
        <div id="scanFileSection" class="d-none" style="padding:1.25rem;text-align:center">
            <input type="file" id="scanImageInput" accept="image/*" capture="environment" style="display:none">
            <button type="button" class="btn-pick-image" onclick="document.getElementById('scanImageInput').click()">
                <i class="fas fa-upload"></i> Choisir une image
            </button>
            <p id="scanFileStatus" style="margin-top:.75rem;color:#aaa;font-size:.85rem"></p>
        </div>

        {{-- Section manuelle --}}
        <div id="scanManualSection" class="d-none" style="padding:1.25rem">
            <label style="font-size:.85rem;color:#aaa;display:block;margin-bottom:.4rem">
                Saisir le code manuellement :
            </label>
            <div style="display:flex;gap:.5rem">
                <input type="text" id="scanManualInput" inputmode="numeric"
                       placeholder="Ex: 3760091721367"
                       style="flex:1;height:42px;border-radius:8px;border:1.5px solid #444;
                              background:#1a1a1a;color:white;padding:0 .75rem;font-size:1rem">
                <button type="button" onclick="scanConfirmManual()"
                        style="background:#27ae60;color:white;border:none;border-radius:8px;
                               padding:0 1.1rem;font-weight:600;cursor:pointer">
                    OK
                </button>
            </div>
        </div>

    </div>
</div>

{{-- Input fichier image pour ZXing --}}
<input type="file" id="scanImageInput" accept="image/*" style="display:none">

@stop

@section('css')
<style>
/* ── Header ────────────────────────────────── */
.header-modern{display:flex;justify-content:space-between;align-items:center;
    margin-bottom:2rem;background:white;padding:1.5rem;border-radius:16px;
    box-shadow:0 2px 12px rgba(0,0,0,.06)}
.header-left{display:flex;align-items:center;gap:1rem}
.btn-back{width:48px;height:48px;display:flex;align-items:center;justify-content:center;
    background:linear-gradient(135deg,#E60000,#FF3333);color:white;border-radius:12px;
    text-decoration:none;transition:all .3s;box-shadow:0 4px 12px rgba(230,0,0,.3)}
.btn-back:hover{transform:translateX(-4px);color:white;text-decoration:none}
.page-title{font-size:1.75rem;font-weight:800;color:#2c3e50;margin:0;
    display:flex;align-items:center;gap:.75rem}
.page-title i{color:#FF6B35}
.page-subtitle{color:#7f8c8d;font-size:.95rem;margin:.25rem 0 0}

/* ── Cards ─────────────────────────────────── */
.form-card-modern{background:white;padding:1.75rem;border-radius:16px;
    box-shadow:0 2px 12px rgba(0,0,0,.06);margin-bottom:1.5rem}
.card-section-title{font-size:1.1rem;font-weight:700;color:#2c3e50;
    margin-bottom:1.5rem;padding-bottom:.75rem;border-bottom:2px solid #f0f0f0;
    display:flex;align-items:center;gap:.5rem}
.card-section-title i{color:#FF6B35}

/* ── Form controls ──────────────────────────── */
.form-group-modern{margin-bottom:1.5rem}
.form-group-modern label{font-weight:600;color:#2c3e50;margin-bottom:.5rem;display:block}
.form-group-modern label i{color:#FF6B35;margin-right:.25rem}
.form-control-modern{width:100%;border:2px solid #e9ecef;border-radius:10px;
    padding:.875rem 1rem;font-size:1rem;transition:all .3s}
.form-control-modern:focus{border-color:#FF6B35;box-shadow:0 0 0 .2rem rgba(255,107,53,.1);outline:none}
.form-control-modern.is-invalid{border-color:#e74c3c}
.invalid-feedback{display:block;color:#e74c3c;font-size:.875rem;margin-top:.25rem}

/* ── Lignes barcode ─────────────────────────── */
.barcode-row{display:flex;gap:.5rem;align-items:center;margin-bottom:.75rem;
    padding:.65rem;border-radius:10px;background:#f8f9fa;border:1.5px solid #e9ecef;
    transition:border-color .2s}
.barcode-row.is-primary{border-color:#27ae60;background:#f0fdf4}
.bc-input{flex:2;min-width:0;border:1.5px solid #e9ecef;border-radius:8px;
    padding:.55rem .75rem;font-size:.95rem;background:white;transition:border-color .2s}
.bc-input:focus{outline:none;border-color:#FF6B35}
.bc-label-input{flex:1;min-width:0;border:1.5px solid #e9ecef;border-radius:8px;
    padding:.55rem .75rem;font-size:.85rem;background:white}
.bc-actions{display:flex;gap:.3rem;align-items:center;flex-shrink:0}
.btn-bc{width:38px;height:38px;border:none;border-radius:8px;cursor:pointer;
    display:flex;align-items:center;justify-content:center;font-size:.9rem;
    color:white;transition:opacity .2s}
.btn-bc:hover{opacity:.85}
.btn-bc-cam{background:#27ae60}
.btn-bc-file{background:#8e44ad}
.btn-bc-remove{background:#e74c3c}
.btn-primary-badge{height:36px;padding:0 .65rem;border-radius:8px;
    border:1.5px solid #ccc;background:white;color:#888;cursor:pointer;
    font-size:.75rem;font-weight:600;white-space:nowrap;transition:all .2s}
.btn-primary-badge.active{background:#27ae60;border-color:#27ae60;color:white}
.btn-primary-badge:hover:not(.active){border-color:#27ae60;color:#27ae60}
.btn-add-barcode{display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.1rem;
    border-radius:10px;background:linear-gradient(135deg,#3498db,#2980b9);color:white;
    border:none;font-weight:600;cursor:pointer;font-size:.9rem;transition:all .2s;margin-top:.25rem}
.btn-add-barcode:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(52,152,219,.35)}

/* ── Modal scanner ──────────────────────────── */
.scan-modal{position:fixed;inset:0;background:rgba(0,0,0,.72);z-index:9999;
    display:flex;align-items:center;justify-content:center}
.scan-box{background:#111;border-radius:18px;overflow:hidden;
    width:min(95vw,430px);box-shadow:0 24px 64px rgba(0,0,0,.6)}
.scan-header{display:flex;justify-content:space-between;align-items:center;
    padding:.75rem 1rem;background:#27ae60;color:white;font-weight:600}
.btn-scan-close{background:rgba(255,255,255,.2);color:white;border:none;
    border-radius:8px;padding:.35rem .85rem;cursor:pointer;font-size:.85rem}
.btn-scan-close:hover{background:rgba(255,255,255,.35)}

.scan-methods{display:flex;gap:.75rem;padding:1rem;background:#0d0d0d}
.btn-method{flex:1;padding:.75rem .5rem;border:2px solid #333;border-radius:12px;
    background:transparent;color:#ccc;cursor:pointer;font-size:.82rem;font-weight:600;
    transition:all .2s;text-align:center;line-height:1.6}
.btn-method i{font-size:1.3rem;display:block;margin-bottom:.2rem}
.btn-method:hover,.btn-method.active{border-color:#27ae60;color:#27ae60;background:rgba(39,174,96,.1)}

.scan-viewport{position:relative;width:100%;background:#000}
#scanVideo{width:100%;max-height:280px;display:block;object-fit:cover}
.scan-overlay{position:absolute;inset:0;display:flex;flex-direction:column;
    align-items:center;justify-content:center;pointer-events:none}
.scan-frame{width:72%;max-width:280px;height:110px;border:3px solid #2ecc71;
    border-radius:10px;position:relative;overflow:hidden;
    box-shadow:0 0 0 2000px rgba(0,0,0,.45)}
.scan-line{position:absolute;top:0;left:0;right:0;height:3px;
    background:#2ecc71;animation:scanLine 1.8s linear infinite}
@keyframes scanLine{0%{top:0}100%{top:100%}}
.scan-hint{margin-top:.75rem;color:white;font-size:.82rem;
    background:rgba(0,0,0,.5);padding:.3rem .75rem;border-radius:20px}
.scan-status{padding:.6rem 1rem;background:#0a0a0a;color:#9ca3af;
    font-size:.82rem;text-align:center;min-height:36px}

.btn-pick-image{display:inline-flex;align-items:center;gap:.5rem;
    padding:.7rem 1.4rem;border-radius:10px;background:#8e44ad;color:white;
    border:none;font-weight:600;cursor:pointer;font-size:.9rem}

/* ── Catégorie / Marque ──────────────────────── */
.category-search-wrapper{position:relative}
.category-search-input-row{display:flex;align-items:center;position:relative}
.category-search-icon{position:absolute;left:14px;color:#FF6B35;z-index:2;pointer-events:none}
.category-search-input{padding-left:2.5rem!important;padding-right:2.5rem!important}
.btn-clear-category{position:absolute;right:10px;background:none;border:none;
    color:#aaa;cursor:pointer;padding:4px 8px;font-size:.9rem;transition:color .2s;z-index:2}
.btn-clear-category:hover{color:#e74c3c}
.category-dropdown{position:absolute;top:calc(100% + 4px);left:0;right:0;
    background:white;border:2px solid #FF6B35;border-radius:12px;
    box-shadow:0 8px 30px rgba(0,0,0,.12);z-index:1000;max-height:260px;overflow-y:auto}
.category-item{display:flex;align-items:center;gap:.5rem;padding:.75rem 1rem;
    cursor:pointer;transition:background .15s;border-bottom:1px solid #f5f5f5;font-size:.95rem}
.category-item:last-child{border-bottom:none}
.category-item:hover{background:#fff5f2}
.category-item.depth-1{padding-left:1.5rem;font-size:.92rem;color:#555}
.category-item.depth-2{padding-left:2.25rem;font-size:.88rem;color:#777}
.cat-icon{color:#FF6B35;font-size:.8rem}
.cat-match{font-weight:700;color:#FF6B35}
.category-empty{padding:1.25rem;text-align:center;color:#aaa;font-size:.9rem}
.category-selected-badge{display:flex;align-items:center;gap:.5rem;margin-top:.5rem;
    padding:.5rem .875rem;background:#f0fdf4;border:1.5px solid #27ae60;
    border-radius:8px;color:#27ae60;font-weight:600;font-size:.9rem}

/* ── Upload photo ────────────────────────────── */
.upload-area{position:relative;border:3px dashed #e9ecef;border-radius:12px;
    padding:2rem;text-align:center;cursor:pointer;transition:all .3s}
.upload-area:hover{border-color:#FF6B35;background:#fff5f2}
.upload-placeholder i{font-size:3rem;color:#FF6B35;margin-bottom:1rem}
.upload-placeholder p{font-weight:600;color:#2c3e50;margin-bottom:.5rem}
.upload-placeholder small{color:#7f8c8d}
.upload-preview{position:relative}
.upload-preview img{max-width:100%;max-height:300px;border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,.1)}
.btn-remove-img{position:absolute;top:10px;right:10px;width:36px;height:36px;
    background:#e74c3c;color:white;border:none;border-radius:50%;cursor:pointer;
    display:flex;align-items:center;justify-content:center;transition:all .3s}
.btn-remove-img:hover{background:#c0392b;transform:scale(1.1)}

/* ── Actions fixes ───────────────────────────── */
.form-actions-fixed{position:sticky;bottom:0;background:white;padding:1.5rem 0;
    box-shadow:0 -4px 20px rgba(0,0,0,.1);z-index:1000;margin-top:2rem}
.actions-wrapper{display:flex;gap:1rem;justify-content:flex-end}
.btn-cancel{background:#6c757d;color:white;padding:.875rem 2rem;border-radius:12px;
    border:none;font-weight:600;display:inline-flex;align-items:center;gap:.5rem;
    text-decoration:none;transition:all .3s}
.btn-cancel:hover{background:#5a6268;transform:translateY(-2px);color:white;text-decoration:none}
.btn-submit{background:linear-gradient(135deg,#E60000,#FF3333);color:white;
    padding:.875rem 2rem;border-radius:12px;border:none;font-weight:600;
    display:inline-flex;align-items:center;gap:.5rem;cursor:pointer;
    transition:all .3s;box-shadow:0 4px 15px rgba(230,0,0,.3)}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 6px 25px rgba(230,0,0,.4)}

/* ── Toast ──────────────────────────────────── */
.toast-notif{position:fixed;bottom:90px;left:50%;transform:translateX(-50%);
    padding:.7rem 1.5rem;border-radius:10px;color:white;font-weight:600;
    font-size:.9rem;z-index:99999;pointer-events:none;
    box-shadow:0 4px 20px rgba(0,0,0,.2)}
.toast-notif.success{background:#27ae60}
.toast-notif.error{background:#e74c3c}

@media(max-width:768px){
    .actions-wrapper{flex-direction:column}
    .btn-cancel,.btn-submit{width:100%;justify-content:center}
}
.content-wrapper{background:#f5f6fa!important;padding-bottom:100px!important}
</style>
@stop

@section('js')
{{--
    SCANNER : ZXing uniquement
    - Fonctionne sur iOS Safari, Android Chrome, Desktop
    - Gestion correcte du stream caméra (stop propre à chaque fermeture)
    - Fallback image (ZXing decode depuis fichier)
    - Fallback saisie manuelle
--}}
<script src="https://unpkg.com/@zxing/library@0.18.6/umd/index.min.js"></script>
<script>

// ── Données PHP → JS ──────────────────────────────────────
@php
    if (!function_exists('flattenCategories')) {
        function flattenCategories($cats, $depth = 0) {
            $result = [];
            foreach ($cats as $cat) {
                $result[] = ['id' => $cat->id, 'nom' => $cat->nom, 'depth' => $depth];
                if ($cat->children && count($cat->children)) {
                    $result = array_merge($result, flattenCategories($cat->children, $depth + 1));
                }
            }
            return $result;
        }
    }
    $flatCategories = flattenCategories($categories);
@endphp
const allCategories = {!! json_encode($flatCategories) !!};
const allMarques    = {!! json_encode($marques->map(fn($m) => ['id'=>$m->id,'nom'=>$m->nom])->values()) !!};

// ── State global ──────────────────────────────────────────
let bcCounter   = 0;
let activeRowId = null;   // id de la ligne en cours de scan
let camStream   = null;   // MediaStream actif
let zxReader    = null;   // instance ZXing BrowserMultiFormatReader

// ══════════════════════════════════════════════════════════
//  DOMContentLoaded
// ══════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {

    // ── Init lignes barcode (old values ou 1 vide) ──────
    @if(old('barcodes'))
        const oldBarcodes = Object.values({!! json_encode(old('barcodes')) !!});
        oldBarcodes.forEach((b, i) => addBarcodeRow(i === 0, b));
        const primaryIdx = oldBarcodes.findIndex(b => b.primary === '1' || b.primary === 1);
        if (primaryIdx > 0) setPrimary(primaryIdx + 1);
    @else
        addBarcodeRow(true);
    @endif

    // ── Bouton ajouter ──────────────────────────────────
    document.getElementById('btnAddBarcode')
            .addEventListener('click', () => addBarcodeRow(false));

    // ── Fermer modal ────────────────────────────────────
    document.getElementById('btnCloseScan')
            .addEventListener('click', closeScan);

    // ── Clic hors modal → fermer ────────────────────────
    document.getElementById('scanModal')
            .addEventListener('click', function (e) {
                if (e.target === this) closeScan();
            });

    // ── Fichier image → ZXing decode ───────────────────
    document.getElementById('scanImageInput')
            .addEventListener('change', async function () {
        if (!this.files?.[0] || activeRowId === null) return;
        const statusEl = document.getElementById('scanFileStatus');
        statusEl.textContent = '⏳ Analyse en cours...';
        try {
            const reader = new ZXing.BrowserMultiFormatReader();
            const url    = URL.createObjectURL(this.files[0]);
            const result = await reader.decodeFromImageUrl(url);
            URL.revokeObjectURL(url);
            applyCode(result.getText());
        } catch {
            statusEl.textContent = '❌ Aucun code-barres détecté dans cette image.';
        }
        this.value = '';
    });

    // ── Photo upload ────────────────────────────────────
    document.getElementById('uploadArea')
            .addEventListener('click', () => document.getElementById('photoInput').click());

    // ── Widgets recherche catégorie & marque ────────────
    const catWidget = makeSearchWidget({
        searchInputId:'categorieSearch', dropdownId:'categoryDropdown',
        listId:'categoryList', emptyId:'categoryEmpty',
        hiddenInputId:'categorie_id', selectedBadgeId:'categorySelected',
        selectedNameId:'categorySelectedName', clearBtnId:'btnClearCategory',
        data: allCategories,
        renderItem: (cat) => ({
            className: 'category-item depth-' + cat.depth,
            icon: cat.depth === 0
                ? '<i class="fas fa-folder cat-icon"></i>'
                : '<i class="fas fa-folder-open cat-icon"></i>',
            prefix: cat.depth > 0 ? '└─ ' : ''
        })
    });

    @if(old('categorie_id'))
    const preSelCat = allCategories.find(c => c.id == {{ old('categorie_id') }});
    if (preSelCat) catWidget.selectItem(preSelCat);
    @endif

    const marqueWidget = makeSearchWidget({
        searchInputId:'marqueSearch', dropdownId:'marqueDropdown',
        listId:'marqueList', emptyId:'marqueEmpty',
        hiddenInputId:'marque_id', selectedBadgeId:'marqueSelected',
        selectedNameId:'marqueSelectedName', clearBtnId:'btnClearMarque',
        data: allMarques,
        renderItem: () => ({
            className: 'category-item',
            icon: '<i class="fas fa-certificate cat-icon"></i>',
            prefix: ''
        })
    });

    @if(old('marque_id'))
    const preSelMarque = allMarques.find(m => m.id == {{ old('marque_id') }});
    if (preSelMarque) marqueWidget.selectItem(preSelMarque);
    @endif

    // ── Validation formulaire ───────────────────────────
    document.querySelector('form').addEventListener('submit', function (e) {

        if (!document.getElementById('categorie_id').value) {
            e.preventDefault();
            document.getElementById('categorieSearch').focus();
            showToast('❌ Veuillez sélectionner une catégorie.', 'error');
            return;
        }

        const bcInputs = document.querySelectorAll('[id^="bc-input-"]');
        let bcOk = true;
        bcInputs.forEach(inp => {
            if (!inp.value.trim()) { inp.style.borderColor = '#e74c3c'; bcOk = false; }
        });
        if (!bcOk) {
            e.preventDefault();
            showToast('❌ Remplissez tous les codes-barres ou supprimez les lignes vides.', 'error');
            return;
        }

        const codes = [...bcInputs].map(i => i.value.trim().toLowerCase());
        if (new Set(codes).size !== codes.length) {
            e.preventDefault();
            showToast('❌ Deux codes-barres identiques détectés.', 'error');
            return;
        }

        const stock    = parseInt(document.querySelector('input[name="stock"]').value)              || 0;
        const stockMin = parseInt(document.querySelector('input[name="quantite_minimale"]').value)  || 0;
        if (stock < stockMin) {
            if (!confirm('⚠️ Le stock initial est inférieur à la quantité minimale.\nContinuer ?')) {
                e.preventDefault();
            }
        }
    });

}); // fin DOMContentLoaded


// ══════════════════════════════════════════════════════════
//  GESTION LIGNES BARCODE
// ══════════════════════════════════════════════════════════

function addBarcodeRow(isPrimary = false, data = {}) {
    const id  = ++bcCounter;
    const row = document.createElement('div');
    row.className = 'barcode-row' + (isPrimary ? ' is-primary' : '');
    row.id = 'bc-row-' + id;

    row.innerHTML =
        '<input type="hidden" name="barcodes[' + id + '][id]" value="' + escHtml(data.id || '') + '">' +

        '<input type="text"' +
               ' name="barcodes[' + id + '][code]"' +
               ' id="bc-input-' + id + '"' +
               ' class="bc-input"' +
               ' placeholder="Code-barres…"' +
               ' value="' + escHtml(data.code || '') + '"' +
               ' required>' +

        '<input type="text"' +
               ' name="barcodes[' + id + '][label]"' +
               ' class="bc-label-input"' +
               ' placeholder="Libellé (optionnel)"' +
               ' value="' + escHtml(data.label || '') + '">' +

        '<div class="bc-actions">' +
            '<button type="button"' +
                    ' class="btn-primary-badge' + (isPrimary ? ' active' : '') + '"' +
                    ' id="bc-primary-btn-' + id + '"' +
                    ' onclick="setPrimary(' + id + ')">' +
                '<i class="fas fa-star"></i> Principal' +
            '</button>' +

            '<button type="button" class="btn-bc btn-bc-cam"' +
                    ' onclick="openScanModal(' + id + ', \'camera\')"' +
                    ' title="Scanner avec caméra">' +
                '<i class="fas fa-camera"></i>' +
            '</button>' +

            '<button type="button" class="btn-bc btn-bc-file"' +
                    ' onclick="openScanModal(' + id + ', \'file\')"' +
                    ' title="Scanner depuis image">' +
                '<i class="fas fa-file-image"></i>' +
            '</button>' +

            '<button type="button" class="btn-bc btn-bc-remove"' +
                    ' onclick="removeRow(' + id + ')"' +
                    ' title="Supprimer">' +
                '<i class="fas fa-trash"></i>' +
            '</button>' +
        '</div>' +

        '<input type="hidden"' +
               ' name="barcodes[' + id + '][primary]"' +
               ' id="bc-primary-val-' + id + '"' +
               ' value="' + (isPrimary ? '1' : '') + '">';

    document.getElementById('barcodeList').appendChild(row);
    ensureOnePrimary();
}

function setPrimary(id) {
    document.querySelectorAll('#barcodeList .barcode-row').forEach(r => r.classList.remove('is-primary'));
    document.querySelectorAll('#barcodeList .btn-primary-badge').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('[id^="bc-primary-val-"]').forEach(i => i.value = '');

    document.getElementById('bc-row-' + id).classList.add('is-primary');
    document.getElementById('bc-primary-btn-' + id).classList.add('active');
    document.getElementById('bc-primary-val-' + id).value = '1';
}

function removeRow(id) {
    const list = document.getElementById('barcodeList');
    if (list.children.length <= 1) {
        showToast('⚠️ Il faut au minimum un code-barres.', 'error');
        return;
    }
    const wasPrimary = document.getElementById('bc-primary-val-' + id).value === '1';
    document.getElementById('bc-row-' + id).remove();
    if (wasPrimary) ensureOnePrimary();
}

function ensureOnePrimary() {
    if (!document.querySelector('#barcodeList .btn-primary-badge.active')) {
        const firstBtn = document.querySelector('#barcodeList .btn-primary-badge');
        if (firstBtn) {
            const m = firstBtn.id.match(/bc-primary-btn-(\d+)/);
            if (m) setPrimary(parseInt(m[1]));
        }
    }
}


// ══════════════════════════════════════════════════════════
//  SCANNER ZXing
// ══════════════════════════════════════════════════════════

/**
 * Ouvre la modal et démarre directement la méthode souhaitée
 * @param {number} rowId - id de la ligne barcode
 * @param {string} method - 'camera' | 'file' | 'manual'
 */
function openScanModal(rowId, method) {
    activeRowId = rowId;
    resetScanSections();
    document.getElementById('scanModal').classList.remove('d-none');
    if (method) scanStart(method);
}

function resetScanSections() {
    stopCamStream();
    ['scanCamSection', 'scanFileSection', 'scanManualSection'].forEach(id => {
        document.getElementById(id).classList.add('d-none');
    });
    document.querySelectorAll('.btn-method').forEach(b => b.classList.remove('active'));
    document.getElementById('scanFileStatus').textContent = '';
    document.getElementById('scanManualInput').value = '';
}

function closeScan() {
    stopCamStream();
    document.getElementById('scanModal').classList.add('d-none');
    activeRowId = null;
}

/**
 * Arrête proprement le stream caméra et ZXing
 */
function stopCamStream() {
    // Stopper ZXing
    if (zxReader) {
        try { zxReader.reset(); } catch(e) {}
        zxReader = null;
    }
    // Stopper les pistes MediaStream
    if (camStream) {
        camStream.getTracks().forEach(t => t.stop());
        camStream = null;
    }
    // Vider la vidéo
    const video = document.getElementById('scanVideo');
    if (video) { video.srcObject = null; }
}

/**
 * Active une méthode de scan
 */
function scanStart(method) {
    resetScanSections();

    const btnMap = { camera:'btnMethodCamera', file:'btnMethodFile', manual:'btnMethodManual' };
    const el = document.getElementById(btnMap[method]);
    if (el) el.classList.add('active');

    if (method === 'camera') {
        document.getElementById('scanCamSection').classList.remove('d-none');
        startCamera();
    } else if (method === 'file') {
        document.getElementById('scanFileSection').classList.remove('d-none');
    } else {
        document.getElementById('scanManualSection').classList.remove('d-none');
        setTimeout(() => document.getElementById('scanManualInput').focus(), 100);
    }
}

/**
 * Démarre la caméra via ZXing BrowserMultiFormatReader
 * Compatible iOS Safari (getUserMedia + decodeFromStream)
 */
async function startCamera() {
    const statusEl = document.getElementById('scanStatus');
    const video    = document.getElementById('scanVideo');
    statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Accès à la caméra...';

    // Vérifier que getUserMedia est disponible
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        statusEl.textContent = '❌ Caméra non disponible sur ce navigateur.';
        return;
    }

    try {
        // 1. Obtenir le stream manuellement (donne la main sur les contraintes)
        camStream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: { ideal: 'environment' },  // caméra arrière sur mobile
                width:  { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        });

        // 2. Affecter le stream à la vidéo
        video.srcObject = camStream;
        await video.play();

        statusEl.innerHTML = '<i class="fas fa-camera"></i> Pointez vers le code-barres...';

        // 3. Créer le reader ZXing et décoder en continu
        zxReader = new ZXing.BrowserMultiFormatReader();

        // Système de vote : 2 lectures identiques consécutives pour valider
        let lastCode = null;
        let votes    = 0;

        // decodeFromStream est la méthode correcte pour un stream déjà actif
        zxReader.decodeFromStream(camStream, video, (result, err) => {
            if (!result) return;  // pas encore de code détecté, on attend

            const code = result.getText();

            if (code === lastCode) {
                votes++;
            } else {
                lastCode = code;
                votes    = 1;
            }

            statusEl.textContent = 'Vérification... (' + votes + '/2) — ' + code;

            if (votes >= 2) {
                applyCode(code);
            }
        });

    } catch (err) {
        console.error('Erreur caméra:', err);
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            statusEl.textContent = '❌ Permission caméra refusée. Autorisez l\'accès dans les réglages.';
        } else if (err.name === 'NotFoundError') {
            statusEl.textContent = '❌ Aucune caméra détectée sur cet appareil.';
        } else {
            statusEl.textContent = '❌ Erreur : ' + (err.message || err.name);
        }
    }
}

/**
 * Applique le code détecté dans le champ barcode actif
 */
function applyCode(code) {
    if (activeRowId === null) return;

    const input = document.getElementById('bc-input-' + activeRowId);
    if (input) {
        input.value = code;
        // Flash vert
        input.style.borderColor = '#27ae60';
        input.style.background  = '#f0fdf4';
        setTimeout(() => { input.style.borderColor = ''; input.style.background = ''; }, 1500);
    }

    showToast('✅ Code détecté : ' + code);
    closeScan();
}

function scanConfirmManual() {
    const val = document.getElementById('scanManualInput').value.trim();
    if (!val) {
        showToast('❌ Veuillez saisir un code.', 'error');
        return;
    }
    applyCode(val);
}

// Touche Entrée dans le champ manuel
document.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !document.getElementById('scanModal').classList.contains('d-none')) {
        const section = document.getElementById('scanManualSection');
        if (!section.classList.contains('d-none')) {
            e.preventDefault();
            scanConfirmManual();
        }
    }
});


// ══════════════════════════════════════════════════════════
//  WIDGET RECHERCHE (catégorie & marque)
// ══════════════════════════════════════════════════════════
function makeSearchWidget(config) {
    const {
        searchInputId, dropdownId, listId, emptyId,
        hiddenInputId, selectedBadgeId, selectedNameId, clearBtnId,
        data, renderItem
    } = config;

    const searchInput   = document.getElementById(searchInputId);
    const dropdown      = document.getElementById(dropdownId);
    const list          = document.getElementById(listId);
    const empty         = document.getElementById(emptyId);
    const hiddenInput   = document.getElementById(hiddenInputId);
    const selectedBadge = document.getElementById(selectedBadgeId);
    const selectedName  = document.getElementById(selectedNameId);
    const clearBtn      = document.getElementById(clearBtnId);

    if (!searchInput || !dropdown || !hiddenInput) {
        console.warn('makeSearchWidget: élément introuvable pour', searchInputId);
        return { selectItem: () => {} };
    }

    function selectItem(item) {
        hiddenInput.value = item.id;
        searchInput.value = item.nom;
        dropdown.classList.add('d-none');
        selectedBadge.classList.remove('d-none');
        selectedName.textContent = item.nom;
        clearBtn.classList.remove('d-none');
    }

    searchInput.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        clearBtn.classList.toggle('d-none', q === '');
        if (q.length < 1) { dropdown.classList.add('d-none'); return; }

        const filtered = data.filter(item => item.nom.toLowerCase().includes(q));
        list.innerHTML = '';

        if (filtered.length === 0) {
            empty.classList.remove('d-none');
        } else {
            empty.classList.add('d-none');
            filtered.forEach(item => {
                const div = document.createElement('div');
                const ri  = renderItem ? renderItem(item) : {};
                div.className = ri.className || 'category-item';
                const highlighted = item.nom.replace(
                    new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')', 'gi'),
                    '<span class="cat-match">$1</span>'
                );
                div.innerHTML = (ri.icon || '') + ' ' + (ri.prefix || '') + highlighted;
                div.addEventListener('click', () => selectItem(item));
                list.appendChild(div);
            });
        }
        dropdown.classList.remove('d-none');
    });

    clearBtn.addEventListener('click', function () {
        hiddenInput.value = '';
        searchInput.value = '';
        selectedBadge.classList.add('d-none');
        clearBtn.classList.add('d-none');
        searchInput.focus();
    });

    searchInput.addEventListener('keydown', e => {
        if (e.key === 'Escape') dropdown.classList.add('d-none');
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('#' + searchInputId) &&
            !e.target.closest('#' + dropdownId) &&
            !e.target.closest('#' + clearBtnId)) {
            dropdown.classList.add('d-none');
        }
    });

    return { selectItem };
}


// ══════════════════════════════════════════════════════════
//  PHOTO
// ══════════════════════════════════════════════════════════
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('uploadPlaceholder').classList.add('d-none');
            document.getElementById('uploadPreview').classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    document.getElementById('photoInput').value = '';
    document.getElementById('previewImg').src   = '';
    document.getElementById('uploadPlaceholder').classList.remove('d-none');
    document.getElementById('uploadPreview').classList.add('d-none');
}


// ══════════════════════════════════════════════════════════
//  HELPERS
// ══════════════════════════════════════════════════════════
function escHtml(str) {
    return String(str).replace(/[&<>"']/g,
        c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

function showToast(msg, type = 'success') {
    const t = document.createElement('div');
    t.className = 'toast-notif ' + type;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}
test 
</script>
@stop
