@extends('adminlte::page')

@section('title','Nouvelle sous‑catégorie')

@section('plugins.Select2', true)

@section('content_header')
    <h1>Créer une nouvelle sous‑catégorie</h1>
@stop

@section('content')
    <form action="{{ route('subcategories.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nom">Nom de la sous‑catégorie</label>
            <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" required>
        </div>

        <div class="form-group">
            <label for="category_id">Catégorie</label>
            <select name="category_id" id="category_id" class="form-control" style="width: 100%;" required>
                @if(old('category_id'))
                    <option value="{{ old('category_id') }}" selected>
                        {{ \App\Models\Category::find(old('category_id'))?->nom }}
                    </option>
                @endif
            </select>
            @error('category_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="{{ route('subcategories.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#category_id').select2({
            theme: 'bootstrap4',
            placeholder: 'Rechercher une catégorie...',
            minimumInputLength: 1,
            allowClear: true,
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