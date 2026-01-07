@extends('adminlte::page')

@section('title','Fiche article')

@section('content_header')
    <h1>Fiche article : {{ $article->nom }}</h1>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Nom</dt>
            <dd class="col-sm-9">{{ $article->nom }}</dd>

            <dt class="col-sm-3">Code-barres</dt>
            <dd class="col-sm-9">{{ $article->code_barres ?? '—' }}</dd>

            <dt class="col-sm-3">Fournisseur</dt>
            <dd class="col-sm-9">{{ $article->fournisseur->nom ?? '—' }}</dd>

            <dt class="col-sm-3">Quantité minimale</dt>
            <dd class="col-sm-9">{{ $article->quantite_minimale }}</dd>

            <dt class="col-sm-3">Prix d’achat</dt>
            <dd class="col-sm-9">{{ $article->prix_achat }}</dd>

            <dt class="col-sm-3">Catégorie</dt>
            <dd class="col-sm-9">{{ $article->category?->parent?->nom ?? '—' }}</dd>

            <dt class="col-sm-3">Sous‑catégorie</dt>
            <dd class="col-sm-9">{{ $article->category?->nom ?? '—' }}</dd>

            <dt class="col-sm-3">Stock</dt>
            @php
                $c = $article->contenance_carton ?: 1;
                $cartons = intdiv($article->stock, $c);
                $reste   = $article->stock % $c;
            @endphp
            <dd class="col-sm-9">
                {{ $article->stock }} pièces ({{ $cartons }} cartons et {{ $reste }} pièces)
            </dd>
        </dl>

        <div class="mt-3">
            <a href="{{ route('articles.edit',$article) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('articles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>
</div>
@stop
