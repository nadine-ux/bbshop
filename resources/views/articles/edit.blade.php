@extends('adminlte::page')

@section('title','Modifier un article')

@section('content_header')
    <h1>Modifier l’article : {{ $article->nom }}</h1>
@stop

@section('content')
    <form action="{{ route('articles.update',$article) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nom">Nom de l’article</label>
            <input type="text" name="nom" class="form-control" value="{{ old('nom',$article->nom) }}" required>
        </div>

        <div class="form-group">
            <label for="code_barres">Code-barres</label>
            <input type="text" name="code_barres" class="form-control" value="{{ old('code_barres',$article->code_barres) }}" required>
        </div>

        <div class="form-group">
            <label for="photo">Photo</label>
            <input type="file" name="photo" class="form-control">
            @if($article->photo)
                <p>Image actuelle :</p>
                <img src="{{ asset('storage/'.$article->photo) }}" width="120" class="img-thumbnail">
            @endif
        </div>

        <div class="form-group">
            <label for="date_peremption">Date de péremption</label>
            <input type="date" name="date_peremption" class="form-control" value="{{ old('date_peremption',$article->date_peremption) }}">
        </div>

        <div class="form-group">
            <label for="quantite_minimale">Quantité minimale d’alerte</label>
            <input type="number" name="quantite_minimale" class="form-control" min="0" value="{{ old('quantite_minimale',$article->quantite_minimale) }}" required>
        </div>

        <div class="form-group">
            <label for="prix_achat">Prix d’achat</label>
            <input type="number" step="0.01" name="prix_achat" class="form-control" value="{{ old('prix_achat',$article->prix_achat) }}">
        </div>

        <div class="form-group">
            <label for="fournisseur_id">Fournisseur</label>
            <select name="fournisseur_id" class="form-control">
                <option value="">-- Choisir --</option>
                @foreach($fournisseurs as $fournisseur)
                    <option value="{{ $fournisseur->id }}" {{ $article->fournisseur_id == $fournisseur->id ? 'selected' : '' }}>
                        {{ $fournisseur->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="contenance_carton">Contenance carton</label>
            <input type="number" name="contenance_carton" class="form-control" min="1" value="{{ old('contenance_carton',$article->contenance_carton) }}">
        </div>

        <div class="form-group">
            <label for="description">Description optionnelle</label>
            <textarea name="description" class="form-control">{{ old('description',$article->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Mettre à jour</button>
        <a href="{{ route('articles.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@stop
