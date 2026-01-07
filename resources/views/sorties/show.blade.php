@extends('adminlte::page')

@section('title','Bon de sortie')

@section('content_header')
    <h1 class="text-center">Bon de sortie N° {{ $sortie->numero_bon }}</h1>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <p><strong>Date sortie :</strong> {{ \Carbon\Carbon::parse($sortie->created_at)->format('d/m/Y') }}</p>
        <p><strong>Destination :</strong> {{ $sortie->destination ?? '—' }}</p>
        <p><strong>Motif :</strong> {{ $sortie->motif ?? '—' }}</p>
        <p><strong>Commentaire :</strong> {{ $sortie->commentaire ?? '—' }}</p>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Quantité totale</th>
                    <th>Cartons</th>
                    <th>Pièces</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sortie->articles as $article)
                    <tr>
                        <td>{{ $article->nom }}</td>
                        <td>{{ $article->pivot->quantite_total }}</td>
                        <td>{{ $article->pivot->quantite_cartons }}</td>
                        <td>{{ $article->pivot->quantite_pieces }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Aucun article enregistré</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="row mt-5">
            <div class="col-md-6">
                <strong>Signature responsable :</strong> ______________________
            </div>
            <div class="col-md-6 text-right">
                <strong>Signature client :</strong> ______________________
            </div>
        </div>
    </div>
</div>
@stop
