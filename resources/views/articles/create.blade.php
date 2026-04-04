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

                {{-- Code-barres avec générateur + scanner --}}
                <div class="form-group-modern">
                    <label>
                        <i class="fas fa-barcode"></i> Code-barres <span class="text-danger">*</span>
                    </label>

                    {{-- Input + boutons scan --}}
                    <div class="barcode-input-row">
                        <input type="text" 
                               id="code_barres"
                               name="code_barres" 
                               class="form-control-modern @error('code_barres') is-invalid @enderror" 
                               placeholder="Entrer, générer ou scanner un code-barres" 
                               value="{{ old('code_barres') }}"
                               required>
                        <button type="button" class="btn-scan-camera" id="btnScanCamera" title="Scanner avec la caméra">
                            <i class="fas fa-camera"></i>
                        </button>
                        <button type="button" class="btn-scan-file" id="btnScanFile" title="Scanner depuis une image">
                            <i class="fas fa-file-image"></i>
                        </button>
                        <input type="file" id="barcodeImageInput" accept="image/*" class="d-none" capture="environment">
                    </div>

                    {{-- Zone vidéo scanner caméra --}}
                    <div id="scannerContainer" class="scanner-container d-none">
                        <div class="scanner-header">
                            <span><i class="fas fa-camera"></i> Scanner en cours...</span>
                            <button type="button" class="btn-close-scanner" id="btnCloseScanner">
                                <i class="fas fa-times"></i> Fermer
                            </button>
                        </div>
                        <div class="scanner-viewport">
                            <video id="scannerVideo" autoplay playsinline muted></video>
                            <div class="scanner-overlay">
                                <div class="scanner-frame">
                                    <div class="scanner-line"></div>
                                </div>
                                <p class="scanner-hint">Pointez la caméra vers le code-barres</p>
                            </div>
                        </div>
                        <div id="scannerStatus" class="scanner-status">
                            <i class="fas fa-spinner fa-spin"></i> Initialisation...
                        </div>
                    </div>

                    @error('code_barres')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> Saisissez manuellement, générez ou scannez avec la caméra / une image
                    </small>
                </div>

                {{-- Catégorie par recherche --}}
                <div class="form-group-modern">
                    <label>
                        <i class="fas fa-tags"></i> Catégorie <span class="text-danger">*</span>
                    </label>
                    <input type="hidden" name="categorie_id" id="categorie_id" value="{{ old('categorie_id') }}" required>
                    
                    <div class="category-search-wrapper">
                        <div class="category-search-input-row">
                            <i class="fas fa-search category-search-icon"></i>
                            <input type="text"
                                   id="categorieSearch"
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

                {{-- ═══ MARQUE PAR RECHERCHE ═══ --}}
                <div class="form-group-modern">
                    <label>
                        <i class="fas fa-certificate"></i> Marque <span class="text-muted">(Optionnel)</span>
                    </label>
                    <input type="hidden" name="marque_id" id="marque_id" value="{{ old('marque_id') }}">

                    <div class="category-search-wrapper">
                        <div class="category-search-input-row">
                            <i class="fas fa-search category-search-icon"></i>
                            <input type="text"
                                   id="marqueSearch"
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
                    <input type="file" 
                           name="photo" 
                           id="photoInput" 
                           class="d-none" 
                           accept="image/*"
                           onchange="previewImage(this)">
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
            
            {{-- Stock et quantités --}}
            <div class="form-card-modern">
                <h5 class="card-section-title">
                    <i class="fas fa-warehouse"></i>
                    Stock et quantités
                </h5>

                <div class="row">
                    {{-- Stock initial --}}
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label>
                                <i class="fas fa-boxes"></i> Stock initial (pièces) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   name="stock" 
                                   class="form-control-modern @error('stock') is-invalid @enderror" 
                                   min="0" 
                                   value="{{ old('stock', 0) }}"
                                   required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Quantité minimale --}}
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label>
                                <i class="fas fa-exclamation-triangle"></i> Quantité minimale <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   name="quantite_minimale" 
                                   class="form-control-modern @error('quantite_minimale') is-invalid @enderror" 
                                   min="0" 
                                   value="{{ old('quantite_minimale') }}"
                                   required>
                            @error('quantite_minimale')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Seuil d'alerte</small>
                        </div>
                    </div>

                    {{-- Contenance carton --}}
                    <div class="col-md-12">
                        <div class="form-group-modern">
                            <label>
                                <i class="fas fa-box-open"></i> Contenance carton (pièces)
                            </label>
                            <input type="number" 
                                   name="contenance_carton" 
                                   class="form-control-modern @error('contenance_carton') is-invalid @enderror" 
                                   min="1" 
                                   value="{{ old('contenance_carton', 1) }}">
                            @error('contenance_carton')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Nombre de pièces par carton (ex: 12, 24...)
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Prix et dates --}}
            <div class="form-card-modern">
                <h5 class="card-section-title">
                    <i class="fas fa-dollar-sign"></i>
                    Prix et dates
                </h5>

                {{-- Prix d'achat --}}
                <div class="form-group-modern">
                    <label>
                        <i class="fas fa-money-bill-wave"></i> Prix d'achat (DZD) <span class="text-muted">(Optionnel)</span>
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="prix_achat" 
                           class="form-control-modern @error('prix_achat') is-invalid @enderror" 
                           placeholder="0.00"
                           value="{{ old('prix_achat') }}">
                    @error('prix_achat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Date de péremption --}}
                <div class="form-group-modern">
                    <label>
                        <i class="fas fa-calendar-times"></i> Date de péremption <span class="text-muted">(Optionnel)</span>
                    </label>
                    <input type="date" 
                           name="date_peremption" 
                           class="form-control-modern @error('date_peremption') is-invalid @enderror"
                           value="{{ old('date_peremption') }}">
                    @error('date_peremption')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Boutons d'action --}}
    <div class="form-actions-fixed">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="actions-wrapper">
                        <a href="{{ route('articles.index') }}" class="btn-cancel">
                            <i class="fas fa-times"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i>
                            Enregistrer l'article
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@stop

@section('css')
<style>
/* Header */
.header-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}
.header-left { display: flex; align-items: center; gap: 1rem; }
.btn-back {
    width: 48px; height: 48px;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%);
    color: white; border-radius: 12px; text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(230, 0, 0, 0.3);
}
.btn-back:hover { transform: translateX(-4px); color: white; text-decoration: none; }
.header-info { display: flex; flex-direction: column; }
.page-title { font-size: 1.75rem; font-weight: 800; color: #2c3e50; margin: 0; display: flex; align-items: center; gap: 0.75rem; }
.page-title i { color: #FF6B35; }
.page-subtitle { color: #7f8c8d; font-size: 0.95rem; margin: 0.25rem 0 0 0; }

/* Form cards */
.form-card-modern {
    background: white; padding: 1.75rem; border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06); margin-bottom: 1.5rem;
}
.card-section-title {
    font-size: 1.1rem; font-weight: 700; color: #2c3e50;
    margin-bottom: 1.5rem; padding-bottom: 0.75rem;
    border-bottom: 2px solid #f0f0f0;
    display: flex; align-items: center; gap: 0.5rem;
}
.card-section-title i { color: #FF6B35; }

/* Form groups */
.form-group-modern { margin-bottom: 1.5rem; }
.form-group-modern label { font-weight: 600; color: #2c3e50; margin-bottom: 0.5rem; display: block; }
.form-group-modern label i { color: #FF6B35; margin-right: 0.25rem; }
.form-control-modern {
    width: 100%; border: 2px solid #e9ecef; border-radius: 10px;
    padding: 0.875rem 1rem; font-size: 1rem; transition: all 0.3s ease;
}
.form-control-modern:focus { border-color: #FF6B35; box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.1); outline: none; }
.form-control-modern.is-invalid { border-color: #e74c3c; }
.invalid-feedback { display: block; color: #e74c3c; font-size: 0.875rem; margin-top: 0.25rem; }

/* BARCODE */
.barcode-input-row {
    display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.75rem;
}
.barcode-input-row .form-control-modern { flex: 1; margin: 0; }
.btn-scan-camera, .btn-scan-file {
    width: 46px; height: 46px; min-width: 46px;
    border: none; border-radius: 10px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; transition: all 0.3s ease;
}
.btn-scan-camera {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    color: white; box-shadow: 0 3px 10px rgba(39,174,96,0.3);
}
.btn-scan-camera:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(39,174,96,0.4); }
.btn-scan-file {
    background: linear-gradient(135deg, #8e44ad, #9b59b6);
    color: white; box-shadow: 0 3px 10px rgba(142,68,173,0.3);
}
.btn-scan-file:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(142,68,173,0.4); }

/* Scanner */
.scanner-container {
    border-radius: 14px; overflow: hidden;
    border: 2px solid #27ae60; margin-bottom: 0.75rem; background: #000;
}
.scanner-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.6rem 1rem; background: #27ae60; color: white; font-weight: 600;
}
.btn-close-scanner {
    background: rgba(255,255,255,0.2); color: white; border: none;
    border-radius: 8px; padding: 0.3rem 0.75rem; cursor: pointer;
    font-size: 0.85rem; transition: background 0.2s;
}
.btn-close-scanner:hover { background: rgba(255,255,255,0.35); }
.scanner-viewport { position: relative; width: 100%; }
#scannerVideo { width: 100%; max-height: 260px; display: block; object-fit: cover; }
.scanner-overlay {
    position: absolute; top: 0; left: 0; right: 0; bottom: 0;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    pointer-events: none;
}
.scanner-frame {
    width: 70%; max-width: 280px; height: 100px;
    border: 3px solid #2ecc71; border-radius: 10px;
    position: relative; overflow: hidden;
    box-shadow: 0 0 0 2000px rgba(0,0,0,0.45);
}
.scanner-line {
    position: absolute; top: 0; left: 0; right: 0;
    height: 3px; background: #2ecc71; box-shadow: 0 0 8px #2ecc71;
    animation: scanLine 1.8s linear infinite;
}
@keyframes scanLine { 0% { top: 0; } 100% { top: 100%; } }
.scanner-hint {
    margin-top: 0.75rem; color: white; font-size: 0.85rem;
    background: rgba(0,0,0,0.5); padding: 0.3rem 0.75rem;
    border-radius: 20px; text-align: center;
}
.scanner-status { padding: 0.5rem 1rem; background: #111; color: #aaa; font-size: 0.85rem; }

/* CATEGORY / MARQUE SEARCH — styles partagés */
.category-search-wrapper { position: relative; }
.category-search-input-row { display: flex; align-items: center; position: relative; }
.category-search-icon {
    position: absolute; left: 14px; color: #FF6B35; z-index: 2; pointer-events: none;
}
.category-search-input {
    padding-left: 2.5rem !important;
    padding-right: 2.5rem !important;
}
.btn-clear-category {
    position: absolute; right: 10px; background: none; border: none;
    color: #aaa; cursor: pointer; padding: 4px 8px; font-size: 0.9rem;
    transition: color 0.2s; z-index: 2;
}
.btn-clear-category:hover { color: #e74c3c; }
.category-dropdown {
    position: absolute; top: calc(100% + 4px); left: 0; right: 0;
    background: white; border: 2px solid #FF6B35; border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.12); z-index: 1000;
    max-height: 260px; overflow-y: auto;
}
.category-dropdown::-webkit-scrollbar { width: 6px; }
.category-dropdown::-webkit-scrollbar-thumb { background: #ddd; border-radius: 3px; }
.category-item {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.75rem 1rem; cursor: pointer;
    transition: background 0.15s; border-bottom: 1px solid #f5f5f5; font-size: 0.95rem;
}
.category-item:last-child { border-bottom: none; }
.category-item:hover { background: #fff5f2; }
.category-item.depth-1 { padding-left: 1.5rem; font-size: 0.92rem; color: #555; }
.category-item.depth-2 { padding-left: 2.25rem; font-size: 0.88rem; color: #777; }
.category-item .cat-icon { color: #FF6B35; font-size: 0.8rem; }
.category-item .cat-match { font-weight: 700; color: #FF6B35; }
.category-empty { padding: 1.25rem; text-align: center; color: #aaa; font-size: 0.9rem; }
.category-selected-badge {
    display: flex; align-items: center; gap: 0.5rem;
    margin-top: 0.5rem; padding: 0.5rem 0.875rem;
    background: #f0fdf4; border: 1.5px solid #27ae60;
    border-radius: 8px; color: #27ae60; font-weight: 600; font-size: 0.9rem;
}
.category-selected-badge i { font-size: 1rem; }

/* Upload */
.upload-area {
    position: relative; border: 3px dashed #e9ecef; border-radius: 12px;
    padding: 2rem; text-align: center; cursor: pointer; transition: all 0.3s ease;
}
.upload-area:hover { border-color: #FF6B35; background: #fff5f2; }
.upload-placeholder i { font-size: 3rem; color: #FF6B35; margin-bottom: 1rem; }
.upload-placeholder p { font-weight: 600; color: #2c3e50; margin-bottom: 0.5rem; }
.upload-placeholder small { color: #7f8c8d; }
.upload-preview { position: relative; }
.upload-preview img { max-width: 100%; max-height: 300px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.btn-remove-img {
    position: absolute; top: 10px; right: 10px; width: 36px; height: 36px;
    background: #e74c3c; color: white; border: none; border-radius: 50%;
    cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;
}
.btn-remove-img:hover { background: #c0392b; transform: scale(1.1); }

/* Actions fixes */
.form-actions-fixed {
    position: sticky; bottom: 0; left: 0; right: 0;
    background: white; padding: 1.5rem 0;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.1); z-index: 1000; margin-top: 2rem;
}
.actions-wrapper { display: flex; gap: 1rem; justify-content: flex-end; }
.btn-cancel {
    background: #6c757d; color: white; padding: 0.875rem 2rem;
    border-radius: 12px; border: none; font-weight: 600;
    display: inline-flex; align-items: center; gap: 0.5rem;
    text-decoration: none; transition: all 0.3s ease;
}
.btn-cancel:hover { background: #5a6268; transform: translateY(-2px); color: white; text-decoration: none; }
.btn-submit {
    background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%);
    color: white; padding: 0.875rem 2rem; border-radius: 12px; border: none;
    font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;
    cursor: pointer; transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(230,0,0,0.3);
}
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 25px rgba(230,0,0,0.4); }

@media (max-width: 768px) {
    .actions-wrapper { flex-direction: column; }
    .btn-cancel, .btn-submit { width: 100%; justify-content: center; }
}
.content-wrapper { background: #f5f6fa !important; padding-bottom: 100px !important; }
</style>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxing-js/0.21.1/zxing.min.js"></script>
<script>
// ═══════════════════════════════════════════════
//  DONNÉES CATÉGORIES
// ═══════════════════════════════════════════════
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

// ═══════════════════════════════════════════════
//  DONNÉES MARQUES
// ═══════════════════════════════════════════════
const allMarques = {!! json_encode($marques->map(fn($m) => ['id' => $m->id, 'nom' => $m->nom])->values()) !!};

// ═══════════════════════════════════════════════
//  HELPER : créer un moteur de recherche réutilisable
// ═══════════════════════════════════════════════
function makeSearchWidget(config) {
    const {
        searchInputId, dropdownId, listId, emptyId,
        hiddenInputId, selectedBadgeId, selectedNameId, clearBtnId,
        data, iconClass, renderItem
    } = config;

    const searchInput   = document.getElementById(searchInputId);
    const dropdown      = document.getElementById(dropdownId);
    const list          = document.getElementById(listId);
    const empty         = document.getElementById(emptyId);
    const hiddenInput   = document.getElementById(hiddenInputId);
    const selectedBadge = document.getElementById(selectedBadgeId);
    const selectedName  = document.getElementById(selectedNameId);
    const clearBtn      = document.getElementById(clearBtnId);

    function selectItem(item) {
        hiddenInput.value    = item.id;
        searchInput.value    = item.nom;
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
                div.className = renderItem ? renderItem(item).className : 'category-item';
                const highlighted = item.nom.replace(
                    new RegExp(`(${q})`, 'gi'),
                    '<span class="cat-match">$1</span>'
                );
                const icon = renderItem ? renderItem(item).icon : `<i class="${iconClass} cat-icon"></i>`;
                const prefix = renderItem ? renderItem(item).prefix : '';
                div.innerHTML = `${icon} ${prefix}${highlighted}`;
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

    // Fermer en cliquant ailleurs
    document.addEventListener('click', e => {
        if (!e.target.closest(`#${searchInputId}`) &&
            !e.target.closest(`#${dropdownId}`) &&
            !e.target.closest(`#${clearBtnId}`)) {
            dropdown.classList.add('d-none');
        }
    });

    return { selectItem };
}

// ═══════════════════════════════════════════════
//  INIT CATÉGORIE
// ═══════════════════════════════════════════════
const catWidget = makeSearchWidget({
    searchInputId:  'categorieSearch',
    dropdownId:     'categoryDropdown',
    listId:         'categoryList',
    emptyId:        'categoryEmpty',
    hiddenInputId:  'categorie_id',
    selectedBadgeId:'categorySelected',
    selectedNameId: 'categorySelectedName',
    clearBtnId:     'btnClearCategory',
    data: allCategories,
    renderItem: (cat) => ({
        className: `category-item depth-${cat.depth}`,
        icon: cat.depth === 0
            ? '<i class="fas fa-folder cat-icon"></i>'
            : '<i class="fas fa-folder-open cat-icon"></i>',
        prefix: cat.depth > 0 ? '└─ ' : ''
    })
});

@if(old('categorie_id'))
const preSelectedCat = allCategories.find(c => c.id == {{ old('categorie_id') }});
if (preSelectedCat) catWidget.selectItem(preSelectedCat);
@endif

// ═══════════════════════════════════════════════
//  INIT MARQUE
// ═══════════════════════════════════════════════
const marqueWidget = makeSearchWidget({
    searchInputId:  'marqueSearch',
    dropdownId:     'marqueDropdown',
    listId:         'marqueList',
    emptyId:        'marqueEmpty',
    hiddenInputId:  'marque_id',
    selectedBadgeId:'marqueSelected',
    selectedNameId: 'marqueSelectedName',
    clearBtnId:     'btnClearMarque',
    data: allMarques,
    iconClass: 'fas fa-certificate',
    renderItem: () => ({
        className: 'category-item',
        icon: '<i class="fas fa-certificate cat-icon"></i>',
        prefix: ''
    })
});

@if(old('marque_id'))
const preSelectedMarque = allMarques.find(m => m.id == {{ old('marque_id') }});
if (preSelectedMarque) marqueWidget.selectItem(preSelectedMarque);
@endif

// ═══════════════════════════════════════════════
//  SCANNER CAMÉRA (ZXing)
// ═══════════════════════════════════════════════
const btnScanCamera    = document.getElementById('btnScanCamera');
const btnCloseScanner  = document.getElementById('btnCloseScanner');
const scannerContainer = document.getElementById('scannerContainer');
const scannerVideo     = document.getElementById('scannerVideo');
const scannerStatus    = document.getElementById('scannerStatus');
let zxingCameraReader  = null;

btnScanCamera.addEventListener('click', async function () {
    scannerContainer.classList.remove('d-none');
    scannerStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Initialisation de la caméra...';
    try {
        if (typeof ZXing === 'undefined') {
            scannerStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Librairie ZXing non chargée.';
            return;
        }
        zxingCameraReader = new ZXing.BrowserMultiFormatReader();
        const videoInputDevices = await ZXing.BrowserCodeReader.listVideoInputDevices();
        let deviceId = videoInputDevices.length > 0 ? videoInputDevices[0].deviceId : undefined;
        const backCamera = videoInputDevices.find(d =>
            d.label.toLowerCase().includes('back') ||
            d.label.toLowerCase().includes('arrière') ||
            d.label.toLowerCase().includes('environment')
        );
        if (backCamera) deviceId = backCamera.deviceId;
        scannerStatus.innerHTML = '<i class="fas fa-camera"></i> Pointez vers le code-barres...';
        await zxingCameraReader.decodeFromVideoDevice(deviceId, 'scannerVideo', (result, err) => {
            if (result) {
                const code = result.getText();
                document.getElementById('code_barres').value = code;
                scannerStatus.innerHTML = `<i class="fas fa-check-circle" style="color:#27ae60"></i> Code détecté : <strong>${code}</strong>`;
                flashSuccess();
                showToast('✅ Code détecté : ' + code);
                setTimeout(closeScanner, 1200);
            }
        });
    } catch (err) {
        scannerStatus.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Erreur caméra : ${err.message}`;
    }
});

function closeScanner() {
    if (zxingCameraReader) { zxingCameraReader.reset(); zxingCameraReader = null; }
    scannerContainer.classList.add('d-none');
}
btnCloseScanner.addEventListener('click', closeScanner);

// ═══════════════════════════════════════════════
//  SCANNER IMAGE FILE (ZXing)
// ═══════════════════════════════════════════════
const btnScanFile       = document.getElementById('btnScanFile');
const barcodeImageInput = document.getElementById('barcodeImageInput');

btnScanFile.addEventListener('click', () => barcodeImageInput.click());

barcodeImageInput.addEventListener('change', async function () {
    if (!this.files || !this.files[0]) return;
    const url = URL.createObjectURL(this.files[0]);
    try {
        if (typeof ZXing === 'undefined') { showToast('❌ Librairie ZXing non chargée.', 'error'); return; }
        const reader = new ZXing.BrowserMultiFormatReader();
        const result = await reader.decodeFromImageUrl(url);
        const code   = result.getText();
        document.getElementById('code_barres').value = code;
        flashSuccess();
        showToast('✅ Code détecté : ' + code);
    } catch (e) {
        showToast('❌ Aucun code-barres trouvé dans l\'image.', 'error');
    }
    URL.revokeObjectURL(url);
    this.value = '';
});

function flashSuccess() {
    const input = document.getElementById('code_barres');
    input.style.borderColor = '#27ae60';
    input.style.background  = '#f0fdf4';
    setTimeout(() => { input.style.borderColor = ''; input.style.background = ''; }, 1500);
}

function showToast(msg, type = 'success') {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position:fixed; bottom:100px; left:50%; transform:translateX(-50%);
        background:${type === 'error' ? '#e74c3c' : '#27ae60'};
        color:white; padding:0.75rem 1.5rem; border-radius:10px;
        font-weight:600; z-index:9999; box-shadow:0 4px 20px rgba(0,0,0,0.2);
    `;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// ═══════════════════════════════════════════════
//  PHOTO PREVIEW
// ═══════════════════════════════════════════════
document.getElementById('uploadArea').addEventListener('click', function () {
    document.getElementById('photoInput').click();
});

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

// ═══════════════════════════════════════════════
//  VALIDATION FORMULAIRE
// ═══════════════════════════════════════════════
document.querySelector('form').addEventListener('submit', function (e) {
    const catIdInput = document.getElementById('categorie_id');
    if (!catIdInput.value) {
        e.preventDefault();
        document.getElementById('categorieSearch').focus();
        document.getElementById('categorieSearch').style.borderColor = '#e74c3c';
        showToast('❌ Veuillez sélectionner une catégorie.', 'error');
        return;
    }
    const stock      = parseInt(document.querySelector('input[name="stock"]').value);
    const quantiteMin = parseInt(document.querySelector('input[name="quantite_minimale"]').value);
    if (stock < quantiteMin) {
        if (!confirm('⚠️ Attention: Le stock initial est inférieur à la quantité minimale.\n\nVoulez-vous continuer ?')) {
            e.preventDefault();
        }
    }
});
</script>
@stop