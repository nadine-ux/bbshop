@extends('adminlte::page')

@section('title','Mouvements de stock')

@section('content_header')
    <h1>📦 Historique des mouvements</h1>
@stop

@section('content')
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('mouvements.index') }}" class="form-inline">
                <div class="form-group mr-2">
                    <label for="type" class="mr-2">Type</label>
                    <select name="type" id="type" class="form-control">
                        <option value="">-- Tous --</option>
                        <option value="Entrée" {{ request('type')=='Entrée' ? 'selected' : '' }}>Entrée</option>
                        <option value="Sortie" {{ request('type')=='Sortie' ? 'selected' : '' }}>Sortie</option>
                    </select>
                </div>
                <div class="form-group mr-2">
                    <label for="date" class="mr-2">Date</label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                </div>
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('mouvements.index') }}" class="btn btn-secondary ml-2">Réinitialiser</a>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Fournisseur / Destination</th>
                        <th>Articles</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mouvements as $mouvement)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($mouvement['date'])->format('d/m/Y') }}</td>
                            <td>
                                @if($mouvement['type']=='Entrée')
                                    <span class="badge badge-success">Entrée</span>
                                @else
                                    <span class="badge badge-danger">Sortie</span>
                                @endif
                            </td>
                            <td>{{ $mouvement['partenaire'] }}</td>
                            <td>
                                <ul class="mb-0">
                                    @foreach(explode(',', $mouvement['articles']) as $article)
                                        <li>{{ $article }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Aucun mouvement enregistré.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $mouvements->links() }}
            </div>
        </div>
    </div>
@stop
