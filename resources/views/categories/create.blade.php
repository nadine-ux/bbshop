@extends('adminlte::page')

@section('title','Nouvelle catégorie')

@section('content_header')
    <h1>Créer une nouvelle catégorie</h1>
@stop

@section('content')
    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nom">Nom de la catégorie</label>
            <input type="text" name="nom" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="parent_id">Catégorie parente (optionnel)</label>
            <select name="parent_id" class="form-control">
                <option value="">-- Aucune (catégorie principale) --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nom }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@stop
