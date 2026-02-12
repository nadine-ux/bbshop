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

                {{-- Code-barres avec générateur --}}
                <div class="form-group-modern">
                    <label>
                        <i class="fas fa-barcode"></i> Code-barres <span class="text-danger">*</span>
                    </label>
                    <div class="input-group-barcode">
                        <input type="text" 
                               id="code_barres"
                               name="code_barres" 
                               class="form-control-modern @error('code_barres') is-invalid @enderror" 
                               placeholder="Entrer ou générer un code-barres" 
                               value="{{ old('code_barres') }}"
                               required>
                        <div class="barcode-actions">
                            <button type="button" class="btn-generate" onclick="generateBarcode('EAN13')">
                                <i class="fas fa-magic"></i> EAN-13
                            </button>
                            <button type="button" class="btn-generate" onclick="generateBarcode('EAN8')">
                                <i class="fas fa-magic"></i> EAN-8
                            </button>
                            <button type="button" class="btn-generate" onclick="generateBarcode('CODE128')">
                                <i class="fas fa-magic"></i> Code-128
                            </button>
                        </div>
                    </div>
                    @error('code_barres')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> Générez automatiquement un code-barres ou entrez-le manuellement
                    </small>
                </div>

                {{-- Catégorie --}}
                <div class="form-group-modern">
                    <label>
                        <i class="fas fa-tags"></i> Catégorie <span class="text-danger">*</span>
                    </label>
                    <select name="categorie_id" class="form-control-modern @error('categorie_id') is-invalid @enderror" required>
                        <option value="">-- Sélectionner une catégorie --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('categorie_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nom }}
                            </option>
                            @foreach($cat->children as $child)
                                <option value="{{ $child->id }}" {{ old('categorie_id') == $child->id ? 'selected' : '' }}>
                                    &nbsp;&nbsp;└─ {{ $child->nom }}
                                </option>
                                @foreach($child->children as $subchild)
                                    <option value="{{ $subchild->id }}" {{ old('categorie_id') == $subchild->id ? 'selected' : '' }}>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└─ {{ $subchild->nom }}
                                    </option>
                                @endforeach
                            @endforeach
                        @endforeach
                    </select>
                    @error('categorie_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Marque --}}
                <div class="form-group-modern">
                    <label>
                        <i class="fas fa-certificate"></i> Marque <span class="text-muted">(Optionnel)</span>
                    </label>
                    <select name="marque_id" class="form-control-modern @error('marque_id') is-invalid @enderror">
                        <option value="">-- Sélectionner une marque --</option>
                        @foreach($marques as $marque)
                            <option value="{{ $marque->id }}" {{ old('marque_id') == $marque->id ? 'selected' : '' }}>
                                {{ $marque->nom }}
                            </option>
                        @endforeach
                    </select>
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

.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.btn-back {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%);
    color: white;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.btn-back:hover {
    transform: translateX(-4px);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
    color: white;
    text-decoration: none;
}

.header-info {
    display: flex;
    flex-direction: column;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 800;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.page-title i {
    color: #FF6B35;
}

.page-subtitle {
    color: #7f8c8d;
    font-size: 0.95rem;
    margin: 0.25rem 0 0 0;
}

/* Form cards */
.form-card-modern {
    background: white;
    padding: 1.75rem;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    margin-bottom: 1.5rem;
}

.card-section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-section-title i {
    color: #FF6B35;
}

/* Form groups */
.form-group-modern {
    margin-bottom: 1.5rem;
}

.form-group-modern label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    display: block;
}

.form-group-modern label i {
    color: #FF6B35;
    margin-right: 0.25rem;
}

.form-control-modern {
    width: 100%;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.875rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control-modern:focus {
    border-color: #FF6B35;
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.1);
    outline: none;
}

.form-control-modern.is-invalid {
    border-color: #e74c3c;
}

.invalid-feedback {
    display: block;
    color: #e74c3c;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Barcode generator */
.input-group-barcode {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.barcode-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-generate {
    flex: 1;
    min-width: 100px;
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-generate:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.btn-generate i {
    font-size: 0.9rem;
}

/* Upload area */
.upload-area {
    position: relative;
    border: 3px dashed #e9ecef;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-area:hover {
    border-color: #FF6B35;
    background: #fff5f2;
}

.upload-placeholder i {
    font-size: 3rem;
    color: #FF6B35;
    margin-bottom: 1rem;
}

.upload-placeholder p {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.upload-placeholder small {
    color: #7f8c8d;
}

.upload-preview {
    position: relative;
}

.upload-preview img {
    max-width: 100%;
    max-height: 300px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.btn-remove-img {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 36px;
    height: 36px;
    background: #e74c3c;
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.btn-remove-img:hover {
    background: #c0392b;
    transform: scale(1.1);
}

/* Fixed action buttons */
.form-actions-fixed {
    position: sticky;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    padding: 1.5rem 0;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    margin-top: 2rem;
}

.actions-wrapper {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.btn-cancel {
    background: #6c757d;
    color: white;
    padding: 0.875rem 2rem;
    border-radius: 12px;
    border: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-cancel:hover {
    background: #5a6268;
    transform: translateY(-2px);
    color: white;
    text-decoration: none;
}

.btn-submit {
    background: linear-gradient(135deg, #E60000 0%, #FF0000 50%, #FF3333 100%);
    color: white;
    padding: 0.875rem 2rem;
    border-radius: 12px;
    border: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(255, 107, 53, 0.4);
}

/* Responsive */
@media (max-width: 768px) {
    .barcode-actions {
        flex-direction: column;
    }
    
    .btn-generate {
        width: 100%;
    }
    
    .actions-wrapper {
        flex-direction: column;
    }
    
    .btn-cancel,
    .btn-submit {
        width: 100%;
        justify-content: center;
    }
}

/* Content wrapper */
.content-wrapper {
    background: #f5f6fa !important;
    padding-bottom: 100px !important;
}
</style>
@stop

@section('js')
<script>
// Générateur de code-barres
function generateBarcode(type) {
    let barcode = '';
    
    switch(type) {
        case 'EAN13':
            // Génère un code EAN-13 (13 chiffres)
            barcode = generateEAN13();
            break;
        case 'EAN8':
            // Génère un code EAN-8 (8 chiffres)
            barcode = generateEAN8();
            break;
        case 'CODE128':
            // Génère un code-128 alphanumérique
            barcode = generateCODE128();
            break;
    }
    
    document.getElementById('code_barres').value = barcode;
    
    // Animation de succès
    const input = document.getElementById('code_barres');
    input.classList.add('border-success');
    setTimeout(() => {
        input.classList.remove('border-success');
    }, 1000);
}

function generateEAN13() {
    // Génère 12 chiffres aléatoires
    let code = '';
    for (let i = 0; i < 12; i++) {
        code += Math.floor(Math.random() * 10);
    }
    
    // Calcul du chiffre de contrôle EAN-13
    let sum = 0;
    for (let i = 0; i < 12; i++) {
        const digit = parseInt(code[i]);
        sum += (i % 2 === 0) ? digit : digit * 3;
    }
    const checkDigit = (10 - (sum % 10)) % 10;
    
    return code + checkDigit;
}

function generateEAN8() {
    // Génère 7 chiffres aléatoires
    let code = '';
    for (let i = 0; i < 7; i++) {
        code += Math.floor(Math.random() * 10);
    }
    
    // Calcul du chiffre de contrôle EAN-8
    let sum = 0;
    for (let i = 0; i < 7; i++) {
        const digit = parseInt(code[i]);
        sum += (i % 2 === 0) ? digit * 3 : digit;
    }
    const checkDigit = (10 - (sum % 10)) % 10;
    
    return code + checkDigit;
}

function generateCODE128() {
    // Génère un code alphanumérique de 12 caractères
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = 'ART';
    for (let i = 0; i < 9; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return code;
}

// Preview de l'image
document.getElementById('uploadArea').addEventListener('click', function() {
    document.getElementById('photoInput').click();
});

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('uploadPlaceholder').classList.add('d-none');
            document.getElementById('uploadPreview').classList.remove('d-none');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    document.getElementById('photoInput').value = '';
    document.getElementById('previewImg').src = '';
    document.getElementById('uploadPlaceholder').classList.remove('d-none');
    document.getElementById('uploadPreview').classList.add('d-none');
}

// Validation avant soumission
document.querySelector('form').addEventListener('submit', function(e) {
    const stock = parseInt(document.querySelector('input[name="stock"]').value);
    const quantiteMin = parseInt(document.querySelector('input[name="quantite_minimale"]').value);
    
    if (stock < quantiteMin) {
        if (!confirm('⚠️ Attention: Le stock initial est inférieur à la quantité minimale.\n\nVoulez-vous continuer ?')) {
            e.preventDefault();
        }
    }
});
</script>
@stop