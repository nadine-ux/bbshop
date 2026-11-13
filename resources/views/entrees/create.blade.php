@extends('adminlte::page')

@section('title', 'Nouvelle entrée')

@section('content_header')
    <h1>Créer une nouvelle Entrée</h1>
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

    <form action="{{ route('entrees.store') }}" method="POST" id="entree-form">
        @csrf

        {{-- EN-TÊTE --}}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Fournisseur</strong> <span class="text-danger">*</span></label>
                            <select name="fournisseur_id" class="form-control" required>
                                <option value="">-- Choisir un fournisseur --</option>
                                @foreach($fournisseurs as $f)
                                    <option value="{{ $f->id }}" {{ old('fournisseur_id') == $f->id ? 'selected' : '' }}>
                                        {{ $f->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Date de réception</strong> <span class="text-danger">*</span></label>
                            <input type="date" name="date_reception" class="form-control"
                                   value="{{ old('date_reception', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Remise globale (%)</strong></label>
                            <input type="number" name="remise_globale" id="remise-globale"
                                   class="form-control" value="{{ old('remise_globale', 0) }}"
                                   min="0" max="100" step="0.01">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label>Commentaire</label>
                    <textarea name="commentaire" class="form-control" rows="2">{{ old('commentaire') }}</textarea>
                </div>
            </div>
        </div>

        {{-- RECHERCHE ARTICLE --}}
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3 class="card-title"><i class="fas fa-search"></i> Ajouter un article</h3>
            </div>
            <div class="card-body">
                <div class="row align-items-start">
                    <div class="col-md-8" style="position:relative;">
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-warning"><i class="fas fa-barcode"></i></span>
                            </div>
                            <input type="text" id="search-article" class="form-control form-control-lg"
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

        {{-- TABLEAU --}}
        <div class="card">
            <div class="card-header bg-success text-white">
                <h3 class="card-title"><i class="fas fa-boxes"></i> Articles reçus</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center" width="10%">Code</th>
                                <th class="text-center" width="22%">Désignation</th>
                                <th class="text-center" width="9%">Cartons</th>
                                <th class="text-center" width="9%">Pièces</th>
                                <th class="text-center" width="9%">Total pcs</th>
                                <th class="text-center" width="12%">Prix unit. (DA)</th>
                                <th class="text-center" width="9%">Remise %</th>
                                <th class="text-center" width="15%">Montant</th>
                                <th class="text-center" width="5%"><i class="fas fa-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-articles"></tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="4" class="text-right">TOTAL PIÈCES :</td>
                                <td class="text-center"><span id="total-pieces">0</span></td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan="7" class="text-right">Sous-total :</td>
                                <td class="text-right"><span id="sous-total">0,00</span> DA</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="7" class="text-right">Remise globale :</td>
                                <td class="text-right">- <span id="montant-remise">0,00</span> DA</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="7" class="text-right">TOTAL À PAYER :</td>
                                <td class="text-right text-success"><span id="total-final">0,00</span> DA</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-right mb-4">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-save"></i> Enregistrer l'entrée
            </button>
            <a href="{{ route('entrees.index') }}" class="btn btn-secondary btn-lg">
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
    const articles      = @json($articlesData);
    const tbody         = document.getElementById('tbody-articles');
    const searchInput   = document.getElementById('search-article');
    const searchResults = document.getElementById('search-results');
    const remiseGlobale = document.getElementById('remise-globale');

    const fmt  = n => n.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const esc  = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
    const norm = s => String(s ?? '').trim().toLowerCase();

    // Un article correspond à un code s'il l'a parmi TOUS ses codes-barres (ou sa référence)
    const hasCode   = (a, c) => (a.barcodes || []).some(b => norm(b) === c) || norm(a.code_barres) === c || norm(a.reference) === c;
    const matchCode = (a, code) => hasCode(a, norm(code));

    // ── Recherche (nom, référence, n'importe quel code-barres) ──
    searchInput.addEventListener('input', function () {
        const q = norm(this.value);
        searchResults.innerHTML = '';
        if (!q) { searchResults.style.display = 'none'; return; }

        const found = articles.filter(a =>
            norm(a.nom).includes(q) ||
            norm(a.reference).includes(q) ||
            norm(a.code_barres).includes(q) ||
            (a.barcodes || []).some(b => norm(b).includes(q))
        );

        if (!found.length) {
            searchResults.innerHTML = '<div class="list-group-item text-muted">Aucun article trouvé</div>';
            searchResults.style.display = 'block';
            return;
        }

        found.slice(0, 10).forEach(art => {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'list-group-item list-group-item-action';
            b.innerHTML = `<strong>${esc(art.nom)}</strong>
                <span class="badge badge-secondary float-right">${esc(art.reference || art.code_barres)}</span>
                <br><small class="text-muted">Carton : ${art.contenance_carton || '—'} pcs</small>`;
            b.addEventListener('click', () => {
                ajouterOuFocus(art);
                searchInput.value = '';
                searchResults.style.display = 'none';
            });
            searchResults.appendChild(b);
        });
        searchResults.style.display = 'block';
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

    document.addEventListener('click', e => {
        if (!searchResults.contains(e.target) && e.target !== searchInput) {
            searchResults.style.display = 'none';
        }
    });

    // ── Résultat du scanner (caméra ou photo) ──
    BarcodeScan.onDetected = function (code) {
        const art = articles.find(a => matchCode(a, code));
        if (art) {
            ajouterOuFocus(art);
        } else {
            // code inconnu : on le met dans la recherche pour voir s'il y a une correspondance partielle
            searchInput.value = code;
            searchInput.dispatchEvent(new Event('input'));
        }
    };

    // Si l'article est déjà dans le tableau → on se place sur ses cartons, sinon on ajoute la ligne
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
        const contenance = art ? (parseInt(art.contenance_carton) || 0) : 0;

        tr.innerHTML = `
            <td>
                <input type="hidden" name="articles[${index}][article_id]" class="input-article-id" value="${art ? art.id : ''}">
                <input type="text" class="form-control form-control-sm text-center"
                       value="${esc(art ? (art.reference || art.code_barres) : '')}" readonly style="background:#f8f9fa;">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm input-nom"
                       value="${esc(art ? art.nom : '')}" placeholder="Choisir via la recherche" readonly style="background:#f8f9fa;">
            </td>
            <td><input type="number" name="articles[${index}][quantite_cartons]" class="form-control form-control-sm text-center input-cartons" value="0" min="0"></td>
            <td><input type="number" name="articles[${index}][quantite_pieces]" class="form-control form-control-sm text-center input-pieces" value="0" min="0"></td>
            <td class="text-center align-middle"><strong class="total-pieces">0</strong></td>
            <td><input type="number" name="articles[${index}][prix_unitaire]" class="form-control form-control-sm text-right input-prix" value="${art && art.prix_achat ? art.prix_achat : 0}" min="0" step="0.01"></td>
            <td><input type="number" name="articles[${index}][remise]" class="form-control form-control-sm text-center input-remise" value="0" min="0" max="100" step="0.01"></td>
            <td class="text-right align-middle"><strong class="montant-ligne">0,00</strong> DA</td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-danger btn-sm btn-supprimer"><i class="fas fa-trash"></i></button>
            </td>`;
        tr.dataset.contenance = contenance;
        tbody.appendChild(tr);

        tr.querySelectorAll('input[type=number]').forEach(i => i.addEventListener('input', calculer));
        tr.querySelector('.btn-supprimer').addEventListener('click', () => {
            if (confirm('Supprimer cet article ?')) { tr.remove(); calculer(); }
        });

        index++;
        calculer();
        return tr;
    }

    // ── Calculs ──
    function calculer() {
        let totalPieces = 0, sousTotal = 0;

        tbody.querySelectorAll('tr').forEach(tr => {
            const contenance = parseInt(tr.dataset.contenance) || 0;
            const cartons = parseInt(tr.querySelector('.input-cartons').value) || 0;
            const pieces  = parseInt(tr.querySelector('.input-pieces').value) || 0;
            const prix    = parseFloat(tr.querySelector('.input-prix').value) || 0;
            const remise  = parseFloat(tr.querySelector('.input-remise').value) || 0;

            const total   = cartons * contenance + pieces;
            const montant = total * prix * (1 - remise / 100);

            tr.querySelector('.total-pieces').textContent = total;
            tr.querySelector('.montant-ligne').textContent = fmt(montant);

            totalPieces += total;
            sousTotal   += montant;
        });

        const rg = parseFloat(remiseGlobale.value) || 0;
        const montantRemise = sousTotal * rg / 100;

        document.getElementById('total-pieces').textContent   = totalPieces;
        document.getElementById('sous-total').textContent     = fmt(sousTotal);
        document.getElementById('montant-remise').textContent = fmt(montantRemise);
        document.getElementById('total-final').textContent    = fmt(sousTotal - montantRemise);
    }

    remiseGlobale.addEventListener('input', calculer);
    document.getElementById('btn-add-manual').addEventListener('click', () => ajouterLigne());

    // ── Validation avant envoi ──
    document.getElementById('entree-form').addEventListener('submit', function (e) {
        if (!tbody.children.length) {
            e.preventDefault();
            alert('Ajoutez au moins un article.');
            return;
        }
        let invalid = false;
        tbody.querySelectorAll('.input-article-id').forEach(inp => {
            if (!inp.value) { invalid = true; inp.closest('tr').querySelector('.input-nom').classList.add('is-invalid'); }
        });
        if (invalid) {
            e.preventDefault();
            alert('Certaines lignes n\'ont pas d\'article. Sélectionnez-les via la recherche.');
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
</style>
@stop