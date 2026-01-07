@extends('adminlte::page')

@section('title','Modifier une sortie')

@section('content_header')
    <h1>Modifier la sortie du {{ $sortie->date_sortie }}</h1>
@stop

@section('content')
    <form action="{{ route('sorties.update',$sortie) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="date_sortie">Date de sortie</label>
            <input type="date" name="date_sortie" class="form-control" value="{{ old('date_sortie',$sortie->date_sortie) }}" required>
        </div>

        <div class="form-group">
            <label for="destination">Destination</label>
            <input type="text" name="destination" class="form-control" value="{{ old('destination',$sortie->destination) }}">
        </div>

        <h4>Articles</h4>
        <div id="articles-container">
            @foreach($sortie->articles as $i => $article)
                <div class="form-row mb-2">
                    <div class="col">
                        <select name="articles[{{ $i }}][id]" class="form-control" required>
                            @foreach($articles as $a)
                                <option value="{{ $a->id }}" {{ $article->id == $a->id ? 'selected' : '' }}>
                                    {{ $a->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <input type="number" name="articles[{{ $i }}][quantite]" class="form-control" value="{{ $article->pivot->quantite }}" min="1" required>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-success">Mettre à jour</button>
        <a href="{{ route('sorties.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@stop
