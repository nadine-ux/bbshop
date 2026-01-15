<div class="container-fluid">
    <div class="row">
        {{-- Colonne gauche: Photo --}}
        <div class="col-md-4 text-center">
            <img src="{{ $article->photo ? asset('storage/'.$article->photo) : asset('images/default-product.png') }}"
                 class="img-fluid rounded shadow"
                 style="max-height: 300px; object-fit: cover;">
        </div>

        {{-- Colonne droite: Informations --}}
        <div class="col-md-8">
            {{-- Nom --}}
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-tag mr-2 text-info"></i>Nom
                </div>
                <div class="detail-value h5 mb-0">{{ $article->nom }}</div>
            </div>

            {{-- Code à barre --}}
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-barcode mr-2 text-info"></i>Code à barre
                </div>
                <div class="detail-value">
                    <code>{{ $article->code_barres ?? 'N/A' }}</code>
                </div>
            </div>

            {{-- Quantité en stock --}}
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-boxes mr-2 text-info"></i>Quantité en stock
                </div>
                <div class="detail-value">
                    @if($article->stock <= $article->quantite_minimale)
                        <span class="badge badge-danger badge-lg">
                            {{ $article->stock }} pièces (Stock bas!)
                        </span>
                    @else
                        <span class="badge badge-success badge-lg">
                            {{ $article->stock }} pièces
                        </span>
                    @endif
                </div>
            </div>

            {{-- Prix d'achat --}}
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-dollar-sign mr-2 text-info"></i>Prix d'achat
                    <button class="btn btn-sm btn-outline-secondary ml-2" onclick="togglePrice()">
                        <i class="fas fa-eye" id="toggle-price-icon"></i>
                    </button>
                </div>
                <div class="detail-value">
                    <span id="price-value" class="price-hidden h5 mb-0">
                        {{ number_format($article->prix_achat ?? 0, 2) }} DA
                    </span>
                </div>
            </div>

            {{-- Catégorie --}}
            <div class="detail-row">
                <div class="detail-label">
                    <i class="fas fa-folder mr-2 text-info"></i>Catégorie
                </div>
                <div class="detail-value">
                    <span class="badge badge-primary">{{ $article->category->nom ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Historique des mouvements --}}
    <div class="row mt-4">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="fas fa-history mr-2 text-info"></i>
                Historique des mouvements (10 derniers)
            </h5>

            @if($mouvements->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    Aucun mouvement enregistré pour cet article.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Partenaire</th>
                                <th>Quantité</th>
                                <th>Détail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mouvements as $mouvement)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($mouvement['date'])->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($mouvement['type'] == 'Entrée')
                                            <span class="badge badge-success">
                                                <i class="fas fa-arrow-down mr-1"></i>Entrée
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-arrow-up mr-1"></i>Sortie
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $mouvement['partenaire'] }}</td>
                                    <td><strong>{{ $mouvement['quantite'] }}</strong> pièces</td>
                                    <td><small class="text-muted">{{ $mouvement['detail'] }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>