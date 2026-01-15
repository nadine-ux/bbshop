@extends('adminlte::page')

@section('title', 'Ajouter un fournisseur')

@section('content_header')
    <h1>Ajouter un fournisseur</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('suppliers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                {{-- Nom --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nom">Nom du fournisseur <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nom" 
                               id="nom"
                               class="form-control @error('nom') is-invalid @enderror" 
                               value="{{ old('nom') }}"
                               required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Marque --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="marque">Marque</label>
                        <input type="text" 
                               name="marque" 
                               id="marque"
                               class="form-control @error('marque') is-invalid @enderror" 
                               value="{{ old('marque') }}">
                        @error('marque')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Téléphone --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="text" 
                               name="telephone" 
                               id="telephone"
                               class="form-control @error('telephone') is-invalid @enderror" 
                               value="{{ old('telephone') }}"
                               placeholder="05XX XX XX XX">
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Photo --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="photo">Photo du fournisseur</label>
                        <div class="custom-file">
                            <input type="file" 
                                   name="photo" 
                                   id="photo"
                                   class="custom-file-input @error('photo') is-invalid @enderror"
                                   accept="image/*"
                                   onchange="previewImage(event)">
                            <label class="custom-file-label" for="photo">Choisir une image...</label>
                            @error('photo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">
                            Formats acceptés : JPG, PNG, GIF (Max: 2MB)
                        </small>
                    </div>
                </div>
            </div>

            {{-- Prévisualisation de la photo --}}
            <div class="row" id="preview-container" style="display: none;">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Aperçu de la photo</label>
                        <div class="text-center">
                            <img id="preview-image" 
                                 class="img-thumbnail" 
                                 style="max-width: 300px; max-height: 300px; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Boutons --}}
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save mr-1"></i> Enregistrer
                </button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
// Prévisualisation de l'image
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview-image');
    const previewContainer = document.getElementById('preview-container');
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        
        reader.readAsDataURL(file);
        
        // Mettre à jour le label du input file
        const fileName = file.name;
        $(event.target).next('.custom-file-label').html(fileName);
    }
}
</script>
@stop