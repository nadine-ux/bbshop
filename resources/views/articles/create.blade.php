@extends('adminlte::page')

@section('title','Créer un article')

@section('content_header')
    <h1>Créer un nouvel article</h1>
@stop

@section('content')
    <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="nom">Nom de l’article</label>
            <input type="text" name="nom" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="code_barres">Code-barres</label>
            <input type="text" name="code_barres" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="photo">Photo</label>
            <input type="file" name="photo" class="form-control">
        </div>

        <div class="form-group">
            <label for="date_peremption">Date de péremption</label>
            <input type="date" name="date_peremption" class="form-control">
        </div>

        <div class="form-group">
            <label for="quantite_minimale">Quantité minimale d’alerte</label>
            <input type="number" name="quantite_minimale" class="form-control" min="0" required>
        </div>

        <div class="form-group">
            <label for="prix_achat">Prix d’achat</label>
            <input type="number" step="0.01" name="prix_achat" class="form-control">
        </div>
       <div class="form-group">
    <label for="categorie_id">Catégorie / Sous‑catégorie</label>
    <select name="categorie_id" class="form-control" required>
        <option value="">-- Choisir --</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->nom }}</option>
            @foreach($cat->children as $child)
                <option value="{{ $child->id }}">-- {{ $child->nom }}</option>
                @foreach($child->children as $subchild)
                    <option value="{{ $subchild->id }}">---- {{ $subchild->nom }}</option>
                @endforeach
            @endforeach
        @endforeach
    </select>
</div>

        <div class="form-group">
            <label for="fournisseur_id">Fournisseur</label>
            <select name="fournisseur_id" class="form-control">
                <option value="">-- Choisir --</option>
                @foreach($fournisseurs as $fournisseur)
                    <option value="{{ $fournisseur->id }}">{{ $fournisseur->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="stock">Stock initial (en pièces)</label>
            <input type="number" name="stock" class="form-control" min="0" value="0" required>
        </div>

        <div class="form-group">
            <label for="contenance_carton">Contenance carton (ex: 12 pièces)</label>
            <input type="number" name="contenance_carton" class="form-control" min="1" value="1">
        </div>
        

        <div class="form-group">
            <label for="description">Description optionnelle</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="{{ route('articles.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@stop
