@extends('adminlte::page')

@section('title','Nouvelle sortie')

@section('content_header')
    <h1>Créer une nouvelle sortie</h1>
@stop

@section('content')
    <form action="{{ route('sorties.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="destination">Destination</label>
            <input type="text" name="destination" class="form-control">
        </div>

        <div class="form-group">
            <label for="motif">Motif</label>
            <input type="text" name="motif" class="form-control">
        </div>

      
        <div class="form-group">
            <label for="commentaire">Commentaire</label>
            <textarea name="commentaire" class="form-control"></textarea>
        </div>

        <h4>Articles sortis</h4>
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
            </div>
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="{{ route('sorties.index') }}" class="btn btn-secondary">Annuler</a>
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
