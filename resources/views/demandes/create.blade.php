@extends('adminlte::page')

@section('title','Nouvelle demande')

@section('content_header')
    <h1>Créer une nouvelle demande</h1>
@stop

@section('content')
<form action="{{ route('demandes.store') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="article_id">Article</label>
        <select name="article_id" id="article_id" class="form-control" required>
            <option value="">-- Choisir un article --</option>
            @foreach($articles as $article)
                <option value="{{ $article->id }}" 
                        data-contenance="{{ $article->contenance_carton }}" 
                        data-image="{{ $article->photo }}">
                    {{ $article->nom }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <img id="article-preview" src="" alt="Aperçu" style="max-width:150px; display:none;">
    </div>

    <div class="form-group">
        <label for="quantite_cartons">Nombre de cartons</label>
        <input type="number" name="quantite_cartons" id="quantite_cartons" class="form-control" min="0" value="0">
    </div>

    <div class="form-group">
        <label for="quantite_pieces">Nombre de pièces</label>
        <input type="number" name="quantite_pieces" id="quantite_pieces" class="form-control" min="0" value="0">
    </div>

    <div class="form-group">
        <label>Quantité totale (pièces)</label>
        <input type="text" id="quantite_total" class="form-control" readonly>
    </div>
<div class="form-group">
    <label for="gestionnaire_id">Envoyer à :</label>
    <select name="gestionnaire_id" id="gestionnaire_id" class="form-control" required>
        @foreach(\App\Models\User::role('Gestionnaire')->get() as $gestionnaire)
            <option value="{{ $gestionnaire->id }}">{{ $gestionnaire->name }}</option>
        @endforeach
    </select>
</div>

    <div class="form-group">
        <label for="remarque">Remarque</label>
        <textarea name="remarque" class="form-control"></textarea>
    </div>

    <button type="submit" class="btn btn-success">Envoyer la demande</button>
</form>
@stop

@section('js')
<script>
function updateTotal() {
    let cartons = parseInt(document.getElementById('quantite_cartons').value) || 0;
    let pieces  = parseInt(document.getElementById('quantite_pieces').value) || 0;
    let contenance = parseInt(document.getElementById('article_id').selectedOptions[0]?.dataset.contenance) || 0;

    let total = (cartons * contenance) + pieces;
    document.getElementById('quantite_total').value = total;
}

document.getElementById('quantite_cartons').addEventListener('input', updateTotal);
document.getElementById('quantite_pieces').addEventListener('input', updateTotal);
document.getElementById('article_id').addEventListener('change', function() {
    // Aperçu image
    let image = this.selectedOptions[0].dataset.image;
    let preview = document.getElementById('article-preview');
    if(image) {
        preview.src = image;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
    updateTotal();
});
</script>
@stop
