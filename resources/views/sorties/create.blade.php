@extends('adminlte::page')

@section('title','Nouvelle sortie')

@section('content_header')
    <h1>Créer une nouvelle Sortie</h1>
@stop

@section('content')
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('sorties.store') }}" method="POST" id="sortie-form">
        @csrf

        {{-- EN-TÊTE --}}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Destination</strong> <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="destination"
                                   class="form-control"
                                   value="{{ old('destination') }}"
                                   placeholder="Écrire la destination..."
                                   required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Motif</strong> <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="motif"
                                   class="form-control"
                                   value="{{ old('motif') }}"
                                   placeholder="Motif de la sortie..."
                                   required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Date et heure</strong></label>
                            <input type="text"
                                   id="date-heure-display"
                                   class="form-control bg-light"
                                   readonly>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Commentaire</label>
                    <textarea name="commentaire" class="form-control" rows="2">{{ old('commentaire') }}</textarea>
                </div>
            </div>
        </div>

        {{-- BARRE DE RECHERCHE D'ARTICLE --}}
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3 class="card-title">
                    <i class="fas fa-search"></i> Ajouter un article
                </h3>
            </div>
            <div class="card-body">
                <div class="row align-items-start">
                    <div class="col-md-8" style="position:relative;">
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-warning">
                                    <i class="fas fa-barcode"></i>
                                </span>
                            </div>
                            <input type="text"
                                   id="search-article"
                                   class="form-control form-control-lg"
                                   placeholder="Chercher par nom ou code-barres, ou scanner..."
                                   autocomplete="off">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-warning" id="btnCamera">
                                    <i class="fas fa-camera"></i> Caméra
                                </button>
                            </div>
                        </div>

                        <div id="search-results" class="list-group shadow"
                             style="display:none; max-height:250px; overflow-y:auto; position:absolute; z-index:1000; left:7.5px; right:7.5px;"></div>

                        {{-- Scanner caméra + photo (même principe que la page Articles) --}}
                        @include('partials.barcode-scan')
                    </div>

                    <div class="col-md-4">
                        <button type="button" class="btn btn-success btn-lg w-100" id="btn-add-manual">
                            <i class="fas fa-plus-circle"></i> Ajouter une ligne vide
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLEAU DES ARTICLES --}}
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h3 class="card-title">
                    <i class="fas fa-boxes"></i> Articles sortis
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0" id="table-articles">
                        <thead class="thead-dark">
                            <tr>
                                <th width="10%"  class="text-center">Code / Réf.</th>
                                <th width="38%"  class="text-center">Désignation (Nom d'article)</th>
                                <th width="13%"  class="text-center">Stock dispo</th>
                                <th width="13%"  class="text-center">Cartons</th>
                                <th width="13%"  class="text-center">Pièces</th>
                                <th width="13%"  class="text-center">Total pièces</th>
                                <th width="5%"   class="text-center"><i class="fas fa-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-articles">
                            {{-- lignes ajoutées dynamiquement --}}
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="5" class="text-right">TOTAL PIÈCES SORTIES :</td>
                                <td class="text-right text-dark"><span id="total-pieces-sorties">0</span> pcs</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-right">TOTAL CARTONS SORTIS :</td>
                                <td class="text-right text-dark"><span id="total-cartons-sortis">0</span> ctn</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- RÉCAPITULATIF --}}
        <div class="card">
            <div class="card-body">
                <div class="row align-items-stretch">

                    <div class="col-md-4">
                        <div class="card border-warning h-100">
                            <div class="card-header bg-warning text-dark text-center">
                                <strong><i class="fas fa-list"></i> Nombre d'articles</strong>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <div style="font-size:2em; font-weight:bold;" class="text-warning">
                                    <span id="nb-articles">0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-info h-100">
                            <div class="card-header bg-info text-white text-center">
                                <strong><i class="fas fa-cubes"></i> Total pièces sorties</strong>
                            </div>
                            <div class="card-body text-center d-flex align-items-center justify-content-center">
                                <div style="font-size:2em; font-weight:bold;" class="text-info">
                                    <span id="recap-total-pieces">0</span> <small style="font-size:.5em">pcs</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-danger text-white h-100" id="card-stock-alert">
                            <div class="card-header bg-danger text-white text-center border-0">
                                <strong><i class="fas fa-exclamation-triangle"></i> Alerte stock</strong>
                            </div>
                            <div class="card-body text-center d-flex align-items-center justify-content-center">
                                <div style="font-size:1.2em; font-weight:bold;" id="stock-alert-msg">
                                    Aucun dépassement
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="text-right mb-4">
            <button type="submit" class="btn btn-danger btn-lg">
                <i class="fas fa-save"></i> Enregistrer la sortie
            </button>
            <a href="{{ route('sorties.index') }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>
    </form>
@stop

@section('js')
@include('partials.barcode-scan-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let index = 0;
    const tbody         = document.getElementById('tbody-articles');
    const btnAddManual  = document.getElementById('btn-add-manual');
    const searchInput   = document.getElementById('search-article');
    const searchResults = document.getElementById('search-results');

    const articles = @json($articlesData);

    const esc  = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
    const norm = s => String(s ?? '').trim().toLowerCase();

    // Un article correspond à un code s'il l'a parmi TOUS ses codes-barres (ou sa référence)
    const matchCode = (a, code) => {
        const c = norm(code);
        return (a.barcodes || []).some(b => norm(b) === c) || norm(a.code_barres) === c || norm(a.reference) === c;
    };

    // ── Date/heure automatique ──
    function updateClock() {
        const now = new Date();
        document.getElementById('date-heure-display').value =
            now.toLocaleDateString('fr-DZ', { year: 'numeric', month: '2-digit', day: '2-digit' })
            + '  ' + now.toLocaleTimeString('fr-DZ');
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── Recherche (nom, référence, n'importe quel code-barres) ──
    searchInput.addEventListener('input', function () {
        const q = norm(this.value);
        searchResults.innerHTML = '';
        if (!q) { searchResults.style.display = 'none'; return; }

        const filtered = articles.filter(a =>
            norm(a.nom).includes(q) ||
            norm(a.reference).includes(q) ||
            norm(a.code_barres).includes(q) ||
            (a.barcodes || []).some(b => norm(b).includes(q))
        );

        if (!filtered.length) {
            searchResults.innerHTML = '<div class="list-group-item text-muted">Aucun article trouvé</div>';
            searchResults.style.display = 'block';
            return;
        }

        filtered.slice(0, 10).forEach(art => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'list-group-item list-group-item-action';
            btn.innerHTML = `
                <strong>${esc(art.nom)}</strong>
                <span class="badge badge-secondary float-right">${esc(art.reference || art.code_barres)}</span>
                <br><small class="text-muted">Stock: <strong class="${art.stock > 0 ? 'text-success' : 'text-danger'}">${art.stock} pcs</strong> — Carton: ${art.contenance_carton || '—'} pcs</small>`;
            btn.addEventListener('click', function () {
                ajouterOuFocus(art);
                searchInput.value = '';
                searchResults.style.display = 'none';
            });
            searchResults.appendChild(btn);
        });
        searchResults.style.display = 'block';
    });

    document.addEventListener('click', function (e) {
        if (!searchResults.contains(e.target) && e.target !== searchInput) {
            searchResults.style.display = 'none';
        }
    });

    // Entrée = code exact (douchette USB ou saisie)
    searchInput.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        const exact = articles.find(a => matchCode(a, this.value));
        if (exact) {
            ajouterOuFocus(exact);
            this.value = '';
            searchResults.style.display = 'none';
        }
    });

    // ── Résultat du scanner (caméra ou photo) ──
    BarcodeScan.onDetected = function (code) {
        const art = articles.find(a => matchCode(a, code));
        if (art) {
            ajouterOuFocus(art);
        } else {
            searchInput.value = code;
            searchInput.dispatchEvent(new Event('input'));
        }
    };

    // Article déjà dans le tableau → on se place sur ses cartons, sinon on ajoute la ligne
    function ajouterOuFocus(art) {
        const existing = Array.from(tbody.querySelectorAll('tr')).find(tr =>
            tr.querySelector('.input-article-id').value == art.id
        );
        const tr = existing || ajouterLigne(art);
        const inp = tr.querySelector('.input-cartons');
        inp.focus(); inp.select();
        tr.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // ── Ajouter une ligne ──
    function ajouterLigne(art = null) {
        const tr = document.createElement('tr');
        tr.setAttribute('data-index', index);

        const artId     = art ? art.id : '';
        const artRef    = art ? (art.reference || art.code_barres || '') : '';
        const artNom    = art ? art.nom : '';
        const artCarton = art ? (parseInt(art.contenance_carton) || 0) : 0;
        const artStock  = art ? (parseInt(art.stock) || 0) : 0;

        tr.innerHTML = `
            <td>
                <input type="hidden" name="articles[${index}][article_id]" class="input-article-id" value="${artId}">
                <input type="text"
                       class="form-control form-control-sm text-center input-ref"
                       value="${esc(artRef)}"
                       placeholder="Code/Réf."
                       data-contenance="${artCarton}"
                       data-stock="${artStock}"
                       readonly style="background:#f8f9fa;">
            </td>
            <td>
                <input type="text"
                       class="form-control form-control-sm input-nom"
                       value="${esc(artNom)}"
                       placeholder="Choisir via la recherche"
                       readonly style="background:#f8f9fa;">
            </td>
            <td class="text-center align-middle">
                <span class="badge badge-${artStock > 0 ? 'success' : 'danger'} p-2 stock-badge">${artStock} pcs</span>
            </td>
            <td>
                <input type="number" name="articles[${index}][quantite_cartons]"
                       class="form-control form-control-sm text-center input-cartons" value="0" min="0">
            </td>
            <td>
                <input type="number" name="articles[${index}][quantite_pieces]"
                       class="form-control form-control-sm text-center input-pieces" value="0" min="0">
            </td>
            <td class="text-center align-middle">
                <strong class="total-pieces text-primary">0</strong> <small>pcs</small>
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-danger btn-sm btn-supprimer"><i class="fas fa-trash"></i></button>
            </td>`;

        tbody.appendChild(tr);

        const inputRef     = tr.querySelector('.input-ref');
        const inputCartons = tr.querySelector('.input-cartons');
        const inputPieces  = tr.querySelector('.input-pieces');
        const spanTotal    = tr.querySelector('.total-pieces');
        const stockBadge   = tr.querySelector('.stock-badge');

        function calculer() {
            const contenance  = parseInt(inputRef.dataset.contenance || 0);
            const stock       = parseInt(inputRef.dataset.stock || 0);
            const cartons     = parseInt(inputCartons.value) || 0;
            const pieces      = parseInt(inputPieces.value) || 0;
            const totalPieces = (cartons * contenance) + pieces;

            spanTotal.textContent = totalPieces;

            if (stock > 0 && totalPieces > stock) {
                spanTotal.classList.add('text-danger');
                spanTotal.classList.remove('text-primary');
                stockBadge.classList.add('badge-danger');
                stockBadge.classList.remove('badge-success');
            } else {
                spanTotal.classList.remove('text-danger');
                spanTotal.classList.add('text-primary');
                if (stock > 0) {
                    stockBadge.classList.remove('badge-danger');
                    stockBadge.classList.add('badge-success');
                }
            }
            calculerTotaux();
        }

        [inputCartons, inputPieces].forEach(inp => inp.addEventListener('input', calculer));

        tr.querySelector('.btn-supprimer').addEventListener('click', function () {
            if (confirm('Supprimer cet article ?')) {
                tr.remove();
                calculerTotaux();
            }
        });

        index++;
        calculerTotaux();
        return tr;
    }

    // ── Totaux récapitulatifs ──
    function calculerTotaux() {
        let totalPieces = 0, totalCartons = 0, nbArticles = 0, stockDepasse = false;

        tbody.querySelectorAll('tr').forEach(tr => {
            const inputRef = tr.querySelector('.input-ref');
            const contenance = parseInt(inputRef?.dataset.contenance || 0);
            const stock      = parseInt(inputRef?.dataset.stock || 0);
            const cartons    = parseInt(tr.querySelector('.input-cartons')?.value || 0) || 0;
            const pieces     = parseInt(tr.querySelector('.input-pieces')?.value || 0) || 0;
            const total      = (cartons * contenance) + pieces;

            totalPieces  += total;
            totalCartons += cartons;
            nbArticles++;
            if (stock > 0 && total > stock) stockDepasse = true;
        });

        document.getElementById('total-pieces-sorties').textContent = totalPieces;
        document.getElementById('total-cartons-sortis').textContent = totalCartons;
        document.getElementById('recap-total-pieces').textContent   = totalPieces;
        document.getElementById('nb-articles').textContent          = nbArticles;

        const cardAlert = document.getElementById('card-stock-alert');
        const alertMsg  = document.getElementById('stock-alert-msg');
        if (stockDepasse) {
            cardAlert.className = 'card bg-danger text-white h-100';
            alertMsg.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Stock insuffisant !';
        } else {
            cardAlert.className = 'card bg-success text-white h-100';
            alertMsg.innerHTML = '<i class="fas fa-check-circle"></i> Stock suffisant';
        }
    }

    btnAddManual.addEventListener('click', () => ajouterLigne());

    // ── Validation à la soumission ──
    document.getElementById('sortie-form').addEventListener('submit', function (e) {
        if (tbody.children.length === 0) {
            e.preventDefault();
            alert('Vous devez ajouter au moins un article !');
            return false;
        }

        let hasError = false;
        tbody.querySelectorAll('.input-article-id').forEach(inp => {
            if (!inp.value) {
                hasError = true;
                inp.closest('tr').querySelector('.input-nom').classList.add('is-invalid');
            }
        });
        if (hasError) {
            e.preventDefault();
            alert('Certains articles ne sont pas valides. Veuillez les sélectionner via la recherche.');
            return false;
        }

        let stockDepasse = false;
        tbody.querySelectorAll('tr').forEach(tr => {
            const inputRef   = tr.querySelector('.input-ref');
            const stock      = parseInt(inputRef?.dataset.stock || 0);
            const contenance = parseInt(inputRef?.dataset.contenance || 0);
            const cartons    = parseInt(tr.querySelector('.input-cartons')?.value || 0) || 0;
            const pieces     = parseInt(tr.querySelector('.input-pieces')?.value || 0) || 0;
            if (stock > 0 && (cartons * contenance + pieces) > stock) stockDepasse = true;
        });

        if (stockDepasse) {
            e.preventDefault();
            alert('⚠️ Stock insuffisant pour un ou plusieurs articles ! Vérifiez les quantités en rouge.');
            return false;
        }
    });
});
</script>
@stop

@section('css')
<style>
    .table td, .table th { vertical-align: middle !important; }
    .is-invalid { border-color: #dc3545 !important; }
    .list-group-item:hover { background-color: #e9f5ff; cursor: pointer; }
    .card-header { font-size: 1em; }
    .stock-badge { font-size: 0.95em; }
</style>
@stop