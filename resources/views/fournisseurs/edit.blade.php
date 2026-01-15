@extends('adminlte::page')

@section('title', 'Modifier le fournisseur')

@section('content_header')
    <h1>Modifier le fournisseur</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Nom --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nom">Nom du fournisseur <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nom" 
                               id="nom"
                               class="form-control @error('nom') is-invalid @enderror" 
                               value="{{ old('nom', $supplier->nom) }}"
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
                               value="{{ old('marque', $supplier->marque) }}">
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
                               value="{{ old('telephone', $supplier->telephone) }}"
                               placeholder="05XX XX XX XX">
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Photo actuelle --}}
            @if($supplier->photo)
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Photo actuelle</label>
                        <div class="text-center mb-3">
                            <img src="{{ asset('storage/' . $supplier->photo) }}" 
                                 alt="{{ $supplier->nom }}"
                                 class="img-thumbnail"
                                 id="current-photo"
                                 style="max-width: 300px; max-height: 300px; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Nouvelle photo --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="photo">Changer la photo</label>
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

            {{-- Prévisualisation nouvelle photo --}}
            <div class="row" id="preview-container" style="display: none;">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Nouvelle photo (aperçu)</label>
                        <div class="text-center">
                            <img id="preview-image" 
                                 class="img-thumbnail" 
                                 style="max-width: 300px; max-height: 300px; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Supprimer la photo --}}
            @if($supplier->photo)
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" 
                                   class="custom-control-input" 
                                   id="remove_photo" 
                                   name="remove_photo"
                                   value="1">
                            <label class="custom-control-label text-danger" for="remove_photo">
                                <i class="fas fa-trash mr-1"></i> Supprimer la photo actuelle
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Boutons --}}
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save mr-1"></i> Mettre à jour
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
    const currentPhoto = document.getElementById('current-photo');
    const removeCheckbox = document.getElementById('remove_photo');
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
            
            // Masquer la photo actuelle
            if (currentPhoto) {
                currentPhoto.style.opacity = '0.5';
            }
            
            // Décocher "supprimer"
            if (removeCheckbox) {
                removeCheckbox.checked = false;
            }
        };
        
        reader.readAsDataURL(file);
        
        // Mettre à jour le label
        const fileName = file.name;
        $(event.target).next('.custom-file-label').html(fileName);
    }
}

// Gérer le checkbox "supprimer"
$(document).ready(function() {
    $('#remove_photo').on('change', function() {
        const currentPhoto = $('#current-photo');
        const photoInput = $('#photo');
        
        if ($(this).is(':checked')) {
            currentPhoto.css('opacity', '0.3');
            photoInput.val('');
            photoInput.next('.custom-file-label').html('Choisir une image...');
            $('#preview-container').hide();
        } else {
            currentPhoto.css('opacity', '1');
        }
    });
});
</script>
@stop