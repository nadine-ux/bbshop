@extends('adminlte::page')

@section('title', 'Modifier une marque')

@section('content_header')
    <div class="header-modern">
        <div class="header-left">
            <a href="{{ route('marques.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="header-info">
                <h1 class="page-title">
                    <i class="fas fa-edit"></i>
                    Modifier la marque
                </h1>
                <p class="page-subtitle">{{ $marque->nom }}</p>
            </div>
        </div>
    </div>
@stop

@section('content')

<div class="form-card-modern">
    <form action="{{ route('marques.update', $marque->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            {{-- Nom de la marque --}}
            <div class="col-md-12">
                <div class="form-group-modern">
                    <label>
                        <i class="fas fa-tag"></i> Nom de la marque <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="nom" 
                           class="form-control-modern @error('nom') is-invalid @enderror" 
                           placeholder="Ex: Samsung, Apple, Nike, LG, Sony..." 
                           value="{{ old('nom', $marque->nom) }}"
                           required
                           autofocus>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> Le nom de la marque doit être unique
                    </small>
                </div>
            </div>
        </div>

        {{-- Informations supplémentaires --}}
        @if($marque->articles->count() > 0)
        <div class="alert-info-modern">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Information :</strong> Cette marque est utilisée par <strong>{{ $marque->articles->count() }}</strong> article(s).
            </div>
        </div>
        @endif

        {{-- Boutons --}}
        <div class="form-actions">
            <a href="{{ route('marques.index') }}" class="btn-cancel">
                <i class="fas fa-times"></i>
                Annuler
            </a>
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i>
                Mettre à jour
            </button>
        </div>
    </form>
</div>

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

/* Formulaire */
.form-card-modern {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

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

/* Alert info */
.alert-info-modern {
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    border-left: 4px solid #2196f3;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.alert-info-modern i {
    font-size: 1.5rem;
    color: #2196f3;
}

/* Actions */
.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 2px solid #f0f0f0;
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
    .form-actions {
        flex-direction: column;
    }
    
    .btn-cancel,
    .btn-submit {
        width: 100%;
        justify-content: center;
    }
}
</style>
@stop