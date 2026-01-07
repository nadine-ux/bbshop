@extends('adminlte::page')

@section('title','Nouvelle entrée')

@section('content_header')
    <h1>Créer une nouvelle entrée</h1>
@stop

@section('content')
    <form action="{{ route('entrees.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="date_reception">Date de réception</label>
            <input type="date" name="date_reception" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="fournisseur_id">Fournisseur</label>
            <select name="fournisseur_id" class="form-control" required>
                <option value="">-- Choisir --</option>
                @foreach($fournisseurs as $fournisseur)
                    <option value="{{ $fournisseur->id }}">{{ $fournisseur->nom }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="commentaire">Commentaire</label>
            <textarea name="commentaire" class="form-control"></textarea>
        </div>

        <h4>Articles reçus</h4>
        <div id="articles-container">
            <div class="article-row mb-3">
                <select name="articles[0][id]" class="form-control mb-2" required>
                    <option value="">-- Choisir un article --</option>
                    @foreach($articles as $article)
                        <option value="{{ $article->id }}" data-contenance="{{ $article->contenance_carton }}">
                            {{ $article->nom }} (Carton = {{ $article->contenance_carton }} pièces)
                        </option>
                    @endforeach
                </select>
                <input type="number" name="articles[0][cartons]" class="form-control mb-2" min="0" placeholder="Nombre de cartons">
                <input type="number" name="articles[0][pieces]" class="form-control mb-2" min="0" placeholder="Nombre de pièces" readonly>
                <input type="number" step="0.01" name="articles[0][prix_unitaire]" class="form-control mb-2" placeholder="Prix unitaire (optionnel)">
            </div>
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="{{ route('entrees.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.article-row').forEach(function(row) {
        let cartonsInput = row.querySelector('input[name*="[cartons]"]');
        let piecesInput  = row.querySelector('input[name*="[pieces]"]');
        let selectArticle = row.querySelector('select[name*="[id]"]');

        cartonsInput.addEventListener('input', function() {
            let contenance = selectArticle.selectedOptions[0].dataset.contenance;
            piecesInput.value = cartonsInput.value * contenance;
        });

        piecesInput.addEventListener('input', function() {
            let contenance = selectArticle.selectedOptions[0].dataset.contenance;
            cartonsInput.value = Math.floor(piecesInput.value / contenance);
        });
    });
});
</script>
@stop
