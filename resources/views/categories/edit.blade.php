@extends('adminlte::page')

@section('title','Modifier catégorie')

@section('content_header')
    <h1>Modifier catégorie</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                {{-- Nom de la catégorie --}}
                <div class="form-group">
                    <label for="nom">Nom de la catégorie</label>
                    <input type="text" 
                           name="nom" 
                           id="nom"
                           class="form-control @error('nom') is-invalid @enderror" 
                           value="{{ old('nom', $category->nom) }}" 
                           required>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Photo actuelle --}}
                <div class="form-group">
                    <label>Photo actuelle</label>
                    <div class="mb-3">
                        @if($category->photo)
                            <img src="{{ asset('storage/' . $category->photo) }}" 
                                 alt="{{ $category->nom }}"
                                 class="img-thumbnail"
                                 id="current-photo"
                                 style="max-width: 200px; max-height: 200px; object-fit: cover;">
                        @else
                            <img src="{{ asset('images/default-category.png') }}" 
                                 alt="Pas de photo"
                                 class="img-thumbnail"
                                 id="current-photo"
                                 style="max-width: 200px; max-height: 200px; object-fit: cover;">
                            <p class="text-muted mt-2">Aucune photo</p>
                        @endif
                    </div>
                </div>

                {{-- Nouvelle photo --}}
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

                {{-- Prévisualisation de la nouvelle photo --}}
                <div class="form-group" id="preview-container" style="display: none;">
                    <label>Nouvelle photo (aperçu)</label>
                    <div>
                        <img id="preview-image" 
                             class="img-thumbnail" 
                             style="max-width: 200px; max-height: 200px; object-fit: cover;">
                    </div>
                </div>

                {{-- Supprimer la photo --}}
                @if($category->photo)
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" 
                                   class="custom-control-input" 
                                   id="remove_photo" 
                                   name="remove_photo"
                                   value="1">
                            <label class="custom-control-label text-danger" for="remove_photo">
                                Supprimer la photo actuelle
                            </label>
                        </div>
                    </div>
                @endif

                {{-- Boutons --}}
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
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
            
            // Masquer la photo actuelle si une nouvelle est sélectionnée
            if (currentPhoto) {
                currentPhoto.style.opacity = '0.5';
            }
            
            // Décocher "supprimer la photo" si une nouvelle est sélectionnée
            if (removeCheckbox) {
                removeCheckbox.checked = false;
            }
        };
        
        reader.readAsDataURL(file);
        
        // Mettre à jour le label du input file
        const fileName = file.name;
        $(event.target).next('.custom-file-label').html(fileName);
    }
}

// Gérer le checkbox "supprimer la photo"
$(document).ready(function() {
    $('#remove_photo').on('change', function() {
        const currentPhoto = $('#current-photo');
        const photoInput = $('#photo');
        
        if ($(this).is(':checked')) {
            currentPhoto.css('opacity', '0.3');
            // Réinitialiser l'input file
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