@extends('adminlte::page')

@section('title','Modifier sous‑catégorie')

@section('content_header')
    <h1>Modifier sous‑catégorie</h1>
@stop

@section('content')
    <form action="{{ route('subcategories.update', $sub->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" name="nom" class="form-control" value="{{ $sub->nom }}" required>
        </div>
        <div class="form-group">
            <label for="category_id">Catégorie</label>
            <select name="category_id" class="form-control" required>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $sub->category_id == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nom }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Mettre à jour</button>
        <a href="{{ route('subcategories.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@stop
