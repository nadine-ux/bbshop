@extends('adminlte::page')

@section('title','Articles')

@section('content_header')
    <h1>Articles</h1>
@stop

@section('content')
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('articles.create') }}" class="btn btn-primary">Ajouter un article</a>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('articles.index') }}" class="form-row">
                <div class="form-group col-md-2">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="{{ request('nom') }}" class="form-control" placeholder="Ex: Papier mouchoir">
                </div>
                <div class="form-group col-md-2">
                    <label for="code_barres">Code-barres</label>
                    <input type="text" id="code_barres" name="code_barres" value="{{ request('code_barres') }}" class="form-control" placeholder="EAN13">
                </div>
                <div class="form-group col-md-2">
                    <label for="fournisseur_id">Fournisseur</label>
                    <select id="fournisseur_id" name="fournisseur_id" class="form-control">
                        <option value="">-- Tous --</option>
                        @isset($fournisseurs)
                            @foreach($fournisseurs as $f)
                                <option value="{{ $f->id }}" {{ request('fournisseur_id')==$f->id ? 'selected' : '' }}>
                                    {{ $f->nom }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="categorie_id">Catégorie</label>
                    <select id="categorie_id" name="categorie_id" class="form-control">
                        <option value="">-- Toutes --</option>
                        @isset($categories)
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ request('categorie_id')==$c->id ? 'selected' : '' }}>
                                    {{ $c->nom }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="souscategorie_id">Sous‑catégorie</label>
                    <select id="souscategorie_id" name="souscategorie_id" class="form-control">
                        <option value="">-- Toutes --</option>
                        @isset($souscats)
                            @foreach($souscats as $sc)
                                <option value="{{ $sc->id }}" {{ request('souscategorie_id')==$sc->id ? 'selected' : '' }}>
                                    {{ $sc->nom }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                <div class="form-group col-md-2"></div>

                <div class="form-group col-md-2">
                    <label for="stock_min">Stock min</label>
                    <input type="number" id="stock_min" name="stock_min" value="{{ request('stock_min') }}" class="form-control" min="0">
                </div>
                <div class="form-group col-md-2">
                    <label for="stock_max">Stock max</label>
                    <input type="number" id="stock_max" name="stock_max" value="{{ request('stock_max') }}" class="form-control" min="0">
                </div>
                <div class="form-group col-md-2">
                    <label for="prix_min">Prix min</label>
                    <input type="number" step="0.01" id="prix_min" name="prix_min" value="{{ request('prix_min') }}" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label for="prix_max">Prix max</label>
                    <input type="number" step="0.01" id="prix_max" name="prix_max" value="{{ request('prix_max') }}" class="form-control">
                </div>
                <div class="form-group col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">Filtrer</button>
                    <a href="{{ route('articles.index') }}" class="btn btn-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Nom</th>
                        <th>Code-barres</th>
                        <th>Fournisseur</th>
                        <th>Quantité minimale</th>
                        <th>Prix d’achat</th>
                        <th>Catégorie</th>
                        <th>Sous‑catégorie</th>
                        <th>Stock</th>
                        <th style="width:180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($articles as $article)
                    @php
                        $c = $article->contenance_carton ?: 1;
                        $cartons = intdiv($article->stock, $c);
                        $reste   = $article->stock % $c;
                    @endphp
                    <tr>
                        <td>{{ $article->nom }}</td>
                        <td>{{ $article->code_barres }}</td>
                        <td>{{ $article->fournisseur->nom ?? '—' }}</td>
                        <td>{{ $article->quantite_minimale }}</td>
                        <td>{{ $article->prix_achat }}</td>
                        <td>{{ $article->category?->parent?->nom ?? '—' }}</td>
                        <td>{{ $article->category?->nom ?? '—' }}</td>
                        <td>
                            {{ $article->stock }} pièces
                            ({{ $cartons }} cartons et {{ $reste }} pièces)
                        </td>
                        <td class="text-center">
                            <a href="{{ route('articles.show',$article) }}" class="text-info mr-2" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('articles.edit',$article) }}" class="text-warning mr-2" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('articles.destroy',$article) }}" method="POST" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-0 m-0" onclick="return confirm('Supprimer cet article ?')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Aucun article trouvé</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
@stop
