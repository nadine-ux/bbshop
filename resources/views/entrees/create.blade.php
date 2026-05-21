
@extends('adminlte::page')

@section('title','Nouvelle entrée')

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

    <form action="{{ route('entrees.store') }}" method="POST" id="entree-form">
        @csrf

        {{-- EN-TÊTE --}}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Fournisseur</strong> <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="fournisseur_nom"
                                   id="fournisseur_nom"
                                   class="form-control"
                                   placeholder="Écrire le nom du fournisseur..."
                                   required
                                   autocomplete="off"
                                   list="fournisseurs-list">
                            <datalist id="fournisseurs-list">
                                @foreach($fournisseurs as $f)
                                    <option value="{{ $f->nom }}" data-id="{{ $f->id }}">
                                @endforeach
                            </datalist>
                            <input type="hidden" name="fournisseur_id" id="fournisseur_id">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Date et heure</strong></label>
                            <input type="text" id="date-heure-display" class="form-control bg-light" readonly>
                            <input type="hidden" name="date_reception" id="date_reception_input">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>N° de Bon</strong></label>
                            <input type="text"
                                   class="form-control bg-light font-weight-bold text-primary"
                                   value="{{ $nextBonNumber ?? 'AUTO-' . date('YmdHis') }}"
                                   readonly>
                            <input type="hidden" name="numero_bon" value="{{ $nextBonNumber ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Commentaire</label>
                    <textarea name="commentaire" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>

        {{-- BARRE DE RECHERCHE D'ARTICLE --}}
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3 class="card-title">
                    <i class="fas fa-search"></i> Barre de recherche — Ajouter un article
                </h3>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-warning">
                                    <i class="fas fa-barcode"></i>
                                </span>
                            </div>
                            <input type="text"
                                   id="search-article"
                                   class="form-control form-control-lg"
                                   placeholder="🔍 Chercher par Nom ou Code-barres — ou Scanner..."
                                   autocomplete="off">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-success" id="btn-scan-entree">
                                    <i class="fas fa-camera"></i> Caméra
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Tapez le nom, la référence ou scannez le code-barres pour ajouter un article directement dans le tableau.
                        </small>

                        {{-- Camera box --}}
                        <div id="scanBoxEntree" class="d-none mt-2"
                             style="border-radius:12px;overflow:hidden;border:2px solid #27ae60;background:#000;">
                            <div style="display:flex;justify-content:space-between;align-items:center;
                                        padding:.55rem 1rem;background:#27ae60;color:white;
                                        font-weight:600;font-size:.88rem;">
                                <span><i class="fas fa-camera"></i> Scanner en cours...</span>
                                <button type="button" id="btnScanEntreeClose"
                                        style="background:rgba(255,255,255,.2);color:white;border:none;
                                               border-radius:7px;padding:.25rem .65rem;cursor:pointer;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <video id="scanVideoEntree" autoplay playsinline muted
                                   style="width:100%;max-height:200px;display:block;object-fit:cover;background:#000;"></video>
                            <div id="scanStatusEntree"
                                 style="padding:.4rem 1rem;background:#111;color:#aaa;font-size:.78rem;">
                                Initialisation...
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div id="search-results" class="list-group shadow"
                             style="display:none;max-height:250px;overflow-y:auto;position:absolute;z-index:1000;width:100%;"></div>
                        <button type="button" class="btn btn-success btn-lg w-100" id="btn-add-manual">
                            <i class="fas fa-plus-circle"></i> Ajouter une ligne vide
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLEAU DES ARTICLES --}}
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title"><i class="fas fa-boxes"></i> Articles reçus</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0" id="table-articles">
                        <thead class="thead-dark">
                            <tr>
                                <th width="8%"  class="text-center">Code / Réf.</th>
                                <th width="28%" class="text-center">Désignation (Nom d'article)</th>
                                <th width="8%"  class="text-center">Cartons</th>
                                <th width="8%"  class="text-center">Pièces</th>
                                <th width="12%" class="text-center">Prix Unitaire (DZD)</th>
                                <th width="8%"  class="text-center">Remise (%)</th>
                                <th width="13%" class="text-center">Total Général</th>
                                <th width="5%"  class="text-center"><i class="fas fa-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-articles"></tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="6" class="text-right">TOTAL BRUT :</td>
                                <td class="text-right text-dark"><span id="total-brut">0.00</span> DZD</td>
                                <td></td>
                            </tr>
                            <tr class="text-danger" id="row-remises-articles" style="display:none;">
                                <td colspan="6" class="text-right">Total Remise articles :</td>
                                <td class="text-right">- <span id="total-remises-articles">0.00</span> DZD</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-right">Sous-total après remises :</td>
                                <td class="text-right"><span id="sous-total">0.00</span> DZD</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- REMISE GLOBALE + TOTAUX FINAUX --}}
        <div class="card">
            <div class="card-body">
                <div class="row align-items-stretch">
                    <div class="col-md-4">
                        <div class="card border-warning h-100">
                            <div class="card-header bg-warning text-dark">
                                <i class="fas fa-percent"></i> <strong>Remise Globale (%) — facultatif</strong>
                            </div>
                            <div class="card-body d-flex align-items-center">
                                <div class="input-group input-group-lg w-100">
                                    <input type="number"
                                           name="remise_globale"
                                           id="remise-globale"
                                           class="form-control text-center font-weight-bold"
                                           step="0.01" value="0" min="0" max="100" placeholder="0.00">
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-warning">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-info h-100">
                            <div class="card-header bg-info text-white text-center">
                                <strong>Nouveau Total</strong>
                            </div>
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <div style="font-size:1.5em;font-weight:bold;" class="text-info">
                                    <span id="nouveau-total">0.00</span> DZD
                                </div>
                                <div id="info-remise-globale" class="text-danger mt-1" style="display:none;">
                                    <small>Remise globale : - <span id="montant-remise-globale">0.00</span> DZD</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white h-100">
                            <div class="card-header bg-success text-white text-center border-0">
                                <strong>TOTAL GÉNÉRAL À PAYER</strong>
                            </div>
                            <div class="card-body text-center d-flex align-items-center justify-content-center">
                                <div style="font-size:2em;font-weight:bold;">
                                    <span id="total-net-final">0.00</span> DZD
                                </div>
                            </div>
                        </div>
                    </div>
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
<script src="{{ asset('js/zxing.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    let index = 0;
    const tbody              = document.getElementById('tbody-articles');
    const btnAddManual       = document.getElementById('btn-add-manual');
    const inputRemiseGlobale = document.getElementById('remise-globale');
    const searchInput        = document.getElementById('search-article');
    const searchResults      = document.getElementById('search-results');

    // ── Articles data from PHP ─────────────────────────────────────────────
    const articles = @json($articlesData);
    const articlesMap = {};
    articles.forEach(a => articlesMap[a.id] = a);

    // ── Date / heure automatique ───────────────────────────────────────────
    function updateClock() {
        const now = new Date();
        const formatted = now.toLocaleDateString('fr-DZ', {
            year: 'numeric', month: '2-digit', day: '2-digit'
        }) + '  ' + now.toLocaleTimeString('fr-DZ');
        document.getElementById('date-heure-display').value  = formatted;
        document.getElementById('date_reception_input').value = now.toISOString().slice(0, 10);
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── Fournisseur : résoudre id depuis datalist ──────────────────────────
    document.getElementById('fournisseur_nom').addEventListener('change', function () {
        const val     = this.value.trim().toLowerCase();
        const options = document.querySelectorAll('#fournisseurs-list option');
        let found = null;
        options.forEach(opt => {
            if (opt.value.trim().toLowerCase() === val) found = opt;
        });
        document.getElementById('fournisseur_id').value = found ? found.dataset.id : '';
    });

    // ── Barre de recherche article ─────────────────────────────────────────
    searchInput.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        searchResults.innerHTML = '';
        if (q.length < 1) { searchResults.style.display = 'none'; return; }

        const filtered = articles.filter(a =>
            a.nom.toLowerCase().includes(q) ||
            (a.code_barres && a.code_barres.toLowerCase().includes(q)) ||
            (a.reference   && a.reference.toLowerCase().includes(q))
        );

        if (filtered.length === 0) {
            searchResults.innerHTML = '<div class="list-group-item text-muted">Aucun article trouvé</div>';
            searchResults.style.display = 'block';
            return;
        }

        filtered.slice(0, 10).forEach(art => {
            const btn = document.createElement('button');
            btn.type      = 'button';
            btn.className = 'list-group-item list-group-item-action';
            btn.innerHTML = `
                <strong>${art.nom}</strong>
                <span class="badge badge-secondary float-right">${art.reference ?? art.code_barres ?? ''}</span>
                <br><small class="text-muted">Prix: ${parseFloat(art.prix_achat).toFixed(2)} DZD — Carton: ${art.contenance_carton} pcs</small>
            `;
            btn.addEventListener('click', function () {
                ajouterLigne(art);
                searchInput.value = '';
                searchResults.style.display = 'none';
            });
            searchResults.appendChild(btn);
        });
        searchResults.style.display = 'block';
    });

    // Fermer les résultats si on clique ailleurs
    document.addEventListener('click', function (e) {
        if (!searchResults.contains(e.target) && e.target !== searchInput) {
            searchResults.style.display = 'none';
        }
    });

    // Enter = correspondance exacte par code-barres
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const q     = this.value.trim().toLowerCase();
            const exact = articles.find(a =>
                (a.code_barres && a.code_barres.toLowerCase() === q) ||
                (a.reference   && a.reference.toLowerCase()   === q)
            );
            if (exact) {
                ajouterLigne(exact);
                this.value = '';
                searchResults.style.display = 'none';
            }
        }
    });

    // ── Ajouter une ligne ──────────────────────────────────────────────────
    function ajouterLigne(art = null) {
        const tr = document.createElement('tr');
        tr.setAttribute('data-index', index);

        const artId     = art ? art.id : '';
        const artRef    = art ? (art.reference || art.code_barres || '') : '';
        const artNom    = art ? art.nom : '';
        const artPrix   = art ? parseFloat(art.prix_achat).toFixed(2) : '0.00';
        const artCarton = art ? art.contenance_carton : 0;

        tr.innerHTML = `
            <td>
                <input type="hidden" name="articles[${index}][article_id]" class="input-article-id" value="${artId}">
                <input type="text"
                       class="form-control form-control-sm text-center input-ref"
                       value="${artRef}" placeholder="Code/Réf."
                       data-contenance="${artCarton}"
                       readonly style="background:#f8f9fa;">
            </td>
            <td>
                <input type="text"
                       class="form-control form-control-sm input-nom"
                       value="${artNom}" placeholder="Nom d'article..."
                       readonly style="background:#f8f9fa;">
            </td>
            <td>
                <input type="number" name="articles[${index}][quantite_cartons]"
                       class="form-control form-control-sm text-center input-cartons"
                       value="0" min="0">
            </td>
            <td>
                <input type="number" name="articles[${index}][quantite_pieces]"
                       class="form-control form-control-sm text-center input-pieces"
                       value="0" min="0">
            </td>
            <td>
                <input type="number" name="articles[${index}][prix_unitaire]"
                       class="form-control form-control-sm text-right input-prix"
                       step="0.01" value="${artPrix}" min="0">
            </td>
            <td>
                <input type="number" name="articles[${index}][remise]"
                       class="form-control form-control-sm text-center input-remise"
                       step="0.01" value="0" min="0" max="100" placeholder="0">
            </td>
            <td class="text-right align-middle">
                <strong class="montant-ligne text-success">0.00</strong> <small>DZD</small>
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-danger btn-sm btn-supprimer">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        const inputRef     = tr.querySelector('.input-ref');
        const inputCartons = tr.querySelector('.input-cartons');
        const inputPieces  = tr.querySelector('.input-pieces');
        const inputPrix    = tr.querySelector('.input-prix');
        const inputRemise  = tr.querySelector('.input-remise');
        const spanMontant  = tr.querySelector('.montant-ligne');

        function calculer() {
            const contenance  = parseInt(inputRef.dataset.contenance || 0);
            const cartons     = parseInt(inputCartons.value) || 0;
            const pieces      = parseInt(inputPieces.value)  || 0;
            const prix        = parseFloat(inputPrix.value)  || 0;
            const remise      = parseFloat(inputRemise.value) || 0;
            const totalPieces = (cartons * contenance) + pieces;
            const prixNet     = prix * (1 - remise / 100);
            spanMontant.textContent = (totalPieces * prixNet).toFixed(2);
            calculerTotaux();
        }

        [inputCartons, inputPieces, inputPrix, inputRemise].forEach(inp => {
            inp.addEventListener('input', calculer);
        });

        tr.querySelector('.btn-supprimer').addEventListener('click', function () {
            if (confirm('Supprimer cet article ?')) {
                tr.remove();
                calculerTotaux();
            }
        });

        if (art) calculer();
        index++;
    }

    // ── Calcul des totaux ──────────────────────────────────────────────────
    function calculerTotaux() {
        let totalBrut = 0, sousTotal = 0;

        tbody.querySelectorAll('tr').forEach(tr => {
            const inputRef     = tr.querySelector('.input-ref');
            const inputPrix    = tr.querySelector('.input-prix');
            const inputCartons = tr.querySelector('.input-cartons');
            const inputPieces  = tr.querySelector('.input-pieces');
            const montant      = parseFloat(tr.querySelector('.montant-ligne')?.textContent || 0);
            const contenance   = parseInt(inputRef?.dataset.contenance || 0);
            const cartons      = parseInt(inputCartons?.value || 0);
            const pieces       = parseInt(inputPieces?.value || 0);
            const prix         = parseFloat(inputPrix?.value || 0);

            totalBrut += ((cartons * contenance) + pieces) * prix;
            sousTotal += montant;
        });

        const remisesArticles      = totalBrut - sousTotal;
        const remiseGlobale        = parseFloat(inputRemiseGlobale.value) || 0;
        const montantRemiseGlobale = sousTotal * (remiseGlobale / 100);
        const totalNetFinal        = sousTotal - montantRemiseGlobale;

        document.getElementById('total-brut').textContent      = totalBrut.toFixed(2);
        document.getElementById('sous-total').textContent      = sousTotal.toFixed(2);
        document.getElementById('nouveau-total').textContent   = sousTotal.toFixed(2);
        document.getElementById('total-net-final').textContent = totalNetFinal.toFixed(2);

        const rowRemisesArticles = document.getElementById('row-remises-articles');
        if (remisesArticles > 0.001) {
            rowRemisesArticles.style.display = '';
            document.getElementById('total-remises-articles').textContent = remisesArticles.toFixed(2);
        } else {
            rowRemisesArticles.style.display = 'none';
        }

        const infoRemiseGlobale = document.getElementById('info-remise-globale');
        if (remiseGlobale > 0) {
            infoRemiseGlobale.style.display = '';
            document.getElementById('montant-remise-globale').textContent = montantRemiseGlobale.toFixed(2);
        } else {
            infoRemiseGlobale.style.display = 'none';
        }
    }

    inputRemiseGlobale.addEventListener('input', calculerTotaux);
    btnAddManual.addEventListener('click', () => ajouterLigne());

    // ── Scanner ZXing ──────────────────────────────────────────────────────
    let zxReaderEntree  = null;
    let camStreamEntree = null;

    const btnScanEntree      = document.getElementById('btn-scan-entree');
    const btnScanEntreeClose = document.getElementById('btnScanEntreeClose');
    const scanBoxEntree      = document.getElementById('scanBoxEntree');
    const scanStatusEntree   = document.getElementById('scanStatusEntree');
    const scanVideoEntree    = document.getElementById('scanVideoEntree');

    btnScanEntree.addEventListener('click', startScanEntree);
    btnScanEntreeClose.addEventListener('click', stopScanEntree);

    async function startScanEntree() {
        scanBoxEntree.classList.remove('d-none');
        scanStatusEntree.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Accès à la caméra...';

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            scanStatusEntree.textContent = '❌ Caméra non disponible sur ce navigateur.';
            return;
        }

        try {
            camStreamEntree = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' },
                    width:  { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            });

            scanVideoEntree.srcObject = camStreamEntree;
            await scanVideoEntree.play();

            scanStatusEntree.innerHTML = '<i class="fas fa-camera"></i> Pointez vers le code-barres...';

            zxReaderEntree = new ZXing.BrowserMultiFormatReader();
            let lastCode = null, votes = 0;

            zxReaderEntree.decodeFromStream(camStreamEntree, scanVideoEntree, (result) => {
                if (!result) return;
                const code = result.getText();

                if (code === lastCode) { votes++; }
                else { lastCode = code; votes = 1; }

                scanStatusEntree.textContent = `Vérification... (${votes}/2) — ${code}`;

                if (votes >= 2) {
                    stopScanEntree();
                    const exact = articles.find(a =>
                        (a.code_barres && a.code_barres.toLowerCase() === code.toLowerCase()) ||
                        (a.reference   && a.reference.toLowerCase()   === code.toLowerCase())
                    );
                    if (exact) {
                        ajouterLigne(exact);
                        searchInput.value = '';
                        searchResults.style.display = 'none';
                    } else {
                        searchInput.value = code;
                        searchInput.dispatchEvent(new Event('input'));
                        searchInput.focus();
                    }
                }
            });

        } catch (err) {
            console.error('Erreur caméra:', err);
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                scanStatusEntree.textContent = '❌ Permission caméra refusée. Autorisez l\'accès dans les réglages.';
            } else if (err.name === 'NotFoundError') {
                scanStatusEntree.textContent = '❌ Aucune caméra détectée.';
            } else {
                scanStatusEntree.textContent = '❌ Erreur : ' + (err.message || err.name);
            }
        }
    }

    function stopScanEntree() {
        if (zxReaderEntree)  { try { zxReaderEntree.reset(); } catch (e) {} zxReaderEntree = null; }
        if (camStreamEntree) { camStreamEntree.getTracks().forEach(t => t.stop()); camStreamEntree = null; }
        scanVideoEntree.srcObject = null;
        scanBoxEntree.classList.add('d-none');
    }

    // ── Validation à la soumission ─────────────────────────────────────────
    document.getElementById('entree-form').addEventListener('submit', function (e) {
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
    });

}); // fin DOMContentLoaded
</script>
@stop

@section('css')
<style>
    .table td, .table th { vertical-align: middle !important; }
    .is-invalid          { border-color: #dc3545 !important; }
    #total-net-final     { font-size: 2em; }
    #search-results      { border-radius: 0 0 8px 8px; }
    .list-group-item:hover { background-color: #e9f5ff; cursor: pointer; }
    .card-header         { font-size: 1em; }
    #scanBoxEntree video  { background: #000; }
</style>
@stop
