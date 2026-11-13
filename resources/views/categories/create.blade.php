@extends('adminlte::page')

@section('title','Nouvelle catégorie')

@section('plugins.Select2', true)

@section('content_header')
    <h1>Créer une nouvelle catégorie</h1>
@stop

@section('content')
<form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label for="nom">Nom de la catégorie</label>
        <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" required>
    </div>

    <div class="form-group">
        <label for="photo">Photo de la catégorie</label>
        <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
    </div>

    <div class="form-group">
        <label for="parent_id">Catégorie parente (optionnel)</label>
        <select name="parent_id" id="parent_id" class="form-control" style="width: 100%;">
            @if(old('parent_id'))
                <option value="{{ old('parent_id') }}" selected>
                    {{ \App\Models\Category::find(old('parent_id'))?->nom }}
                </option>
            @endif
        </select>
        @error('parent_id')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="btn btn-success">Enregistrer</button>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Annuler</a>
</form>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#parent_id').select2({
            theme: 'bootstrap4',
            placeholder: '-- Aucune (catégorie principale) --',
            allowClear: true,
            minimumInputLength: 1,
            language: {
                inputTooShort: () => 'Tapez au moins 1 caractère',
                noResults: () => 'Aucune catégorie trouvée',
                searching: () => 'Recherche en cours...'
            },
            ajax: {
                url: '{{ route("categories.search") }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => data,
                cache: true
            }
        });
    });
</script>
@stop