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

     @php
    $rows = old('barcodes', $article->barcodes->map(fn ($b) => [
        'id' => $b->id, 'code' => $b->code_barres, 'label' => $b->label, 'primary' => $b->is_primary,
    ])->all());
@endphp

<div class="form-group">
    <label>Codes-barres</label>
    @if($errors->has('barcodes') || $errors->has('barcodes.*'))
        <div class="text-danger small">{{ $errors->first('barcodes') ?: collect($errors->get('barcodes.*'))->flatten()->first() }}</div>
    @endif

    <div id="barcodeRows">
        @foreach($rows as $i => $b)
            <div class="input-group mb-2 barcode-row">
                <div class="input-group-prepend">
                    <div class="input-group-text" title="Code principal">
                        <input type="radio" name="primary_index" value="{{ $i }}"
                               {{ !empty($b['primary']) && $b['primary'] !== '0' ? 'checked' : '' }}>
                    </div>
                </div>
                <input type="hidden" name="barcodes[{{ $i }}][id]" value="{{ $b['id'] ?? '' }}">
                <input type="text" name="barcodes[{{ $i }}][code]" class="form-control" placeholder="Code-barres" value="{{ $b['code'] ?? '' }}" required>
                <input type="text" name="barcodes[{{ $i }}][label]" class="form-control" placeholder="Libellé" value="{{ $b['label'] ?? '' }}">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger" onclick="removeRow(this)">&times;</button>
                </div>
            </div>
        @endforeach
    </div>

    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()">+ Ajouter un code</button>
    <small class="form-text text-muted">Le bouton radio indique le code principal.</small>
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
@section('js')
<script>
var nextIdx = {{ count($rows) }};

function addRow() {
    var i = nextIdx++;
    var div = document.createElement('div');
    div.className = 'input-group mb-2 barcode-row';
    div.innerHTML =
        '<div class="input-group-prepend"><div class="input-group-text"><input type="radio" name="primary_index" value="' + i + '"></div></div>' +
        '<input type="hidden" name="barcodes[' + i + '][id]" value="">' +
        '<input type="text" name="barcodes[' + i + '][code]" class="form-control" placeholder="Code-barres" required>' +
        '<input type="text" name="barcodes[' + i + '][label]" class="form-control" placeholder="Libellé">' +
        '<div class="input-group-append"><button type="button" class="btn btn-danger" onclick="removeRow(this)">&times;</button></div>';
    document.getElementById('barcodeRows').appendChild(div);
}

function removeRow(btn) {
    var rows = document.querySelectorAll('.barcode-row');
    if (rows.length <= 1) { alert('Au moins un code-barres est requis.'); return; }
    btn.closest('.barcode-row').remove();
}
</script>
@stop