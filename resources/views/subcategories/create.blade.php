@extends('adminlte::page')

@section('title','Nouvelle sous‑catégorie')

@section('content_header')
    <h1>Créer une nouvelle sous‑catégorie</h1>
@stop

@section('content')
    <form action="{{ route('subcategories.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nom">Nom de la sous‑catégorie</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="category_id">Catégorie</label>
            <select name="category_id" class="form-control" required>
                <option value="">-- Choisir une catégorie --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nom }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="{{ route('subcategories.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@stop
