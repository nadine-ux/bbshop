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
                                   placeholder="Écrire la destination..."
                                   value="{{ old('destination') }}"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Motif</strong> <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="motif"
                                   class="form-control"
                                   placeholder="Motif de la sortie..."
                                   value="{{ old('motif') }}"
                                   required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {{-- ✅ FIX : hidden input date_sortie envoyé au controller --}}
                            <label><strong>Date et heure</strong></label>
                            <input type="text"
                                   id="date-heure-display"
                                   class="form-control bg-light"
                                   readonly>
                            <input type="hidden" name="date_sortie" id="date_sortie_input">
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
                                   placeholder="🔍 Chercher un article par Nom ou Code-barres — ou Scanner..."
                                   autocomplete="off">
                            {{-- ✅ AJOUT : bouton caméra comme dans les entrées --}}
                            <div class="input-group-append">
                                <button type="button" class="btn btn-warning" id="btn-scan-sortie">
                                    <i class="fas fa-camera"></i> Caméra
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Tapez le nom, la référence ou scannez le code-barres pour ajouter un article directement dans le tableau.
                        </small>

                        {{-- ✅ AJOUT : box scanner ZXing --}}
                        <div id="scanBoxSortie" class="d-none mt-2"
                             style="border-radius:12px;overflow:hidden;border:2px solid #e67e22;background:#000;">
                            <div style="display:flex;justify-content:space-between;align-items:center;
                                        padding:.55rem 1rem;background:#e67e22;color:white;
                                        font-weight:600;font-size:.88rem;">
                                <span><i class="fas fa-camera"></i> Scanner en cours...</span>
                                <button type="button" id="btnScanSortieClose"
                                        style="background:rgba(255,255,255,.2);color:white;border:none;
                                               border-radius:7px;padding:.25rem .65rem;cursor:pointer;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <video id="scanVideoSortie" autoplay playsinline muted
                                   style="width:100%;max-height:200px;display:block;object-fit:cover;background:#000;"></video>
                            <div id="scanStatusSortie"
                                 style="padding:.4rem 1rem;background:#111;color:#aaa;font-size:.78rem;">
                                Initialisation...
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4" style="position:relative;">
                        <div id="search-results" class="list-group shadow"
                             style="display:none; max-height:250px; overflow-y:auto; position:absolute; z-index:1000; width:100%; top:0;"></div>
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
<script src="{{ asset('js/zxing.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let index = 0;
    const tbody        = document.getElementById('tbody-articles');
    const btnAddManual = document.getElementById('btn-add-manual');
    const searchInput  = document.getElementById('search-article');
    const searchResults= document.getElementById('search-results');

    const articles = @json($articlesData);

    // ── ✅ Date/heure avec hidden input pour le controller ─────────────────
    function updateClock() {
        const now = new Date();
        const formatted = now.toLocaleDateString('fr-DZ', {
            year: 'numeric', month: '2-digit', day: '2-digit'
        }) + '  ' + now.toLocaleTimeString('fr-DZ');
        document.getElementById('date-heure-display').value = formatted;
        document.getElementById('date_sortie_input').value  = now.toISOString().slice(0, 10);
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── Barre de recherche article ──────────────────────────────────────────
    searchInput.addEventListener('input', function() {
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
                <span class="badge badge-secondary float-right">${art.reference || art.code_barres || ''}</span>
                <br><small class="text-muted">Stock: <strong class="${art.stock > 0 ? 'text-success' : 'text-danger'}">${art.stock} pcs</strong> — Carton: ${art.contenance_carton} pcs</small>
            `;
            btn.addEventListener('click', function() {
                ajouterLigne(art);
                searchInput.value = '';
                searchResults.style.display = 'none';
            });
            searchResults.appendChild(btn);
        });
        searchResults.style.display = 'block';
    });

    document.addEventListener('click', function(e) {
        if (!searchResults.contains(e.target) && e.target !== searchInput) {
            searchResults.style.display = 'none';
        }
    });

    // Enter = correspondance exacte code-barres
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const q = this.value.trim().toLowerCase();
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
        const artCarton = art ? art.contenance_carton : 0;
        const artStock  = art ? art.stock : 0;

        tr.innerHTML = `
            <td>
                <input type="hidden" name="articles[${index}][article_id]" class="input-article-id" value="${artId}">
                <input type="text"
                       class="form-control form-control-sm text-center input-ref"
                       value="${artRef}"
                       placeholder="Code/Réf."
                       data-contenance="${artCarton}"
                       data-stock="${artStock}"
                       readonly style="background:#f8f9fa;">
            </td>
            <td>
                <input type="text"
                       class="form-control form-control-sm input-nom"
                       value="${artNom}"
                       placeholder="Nom d'article..."
                       readonly style="background:#f8f9fa;">
            </td>
            <td class="text-center align-middle">
                <span class="badge badge-${artStock > 0 ? 'success' : 'danger'} p-2 stock-badge">
                    ${artStock} pcs
                </span>
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
            <td class="text-center align-middle">
                <strong class="total-pieces text-primary">0</strong> <small>pcs</small>
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
        const spanTotal    = tr.querySelector('.total-pieces');
        const stockBadge   = tr.querySelector('.stock-badge');

        function calculer() {
            const contenance  = parseInt(inputRef.dataset.contenance || 0);
            const stock       = parseInt(inputRef.dataset.stock || 0);
            const cartons     = parseInt(inputCartons.value) || 0;
            const pieces      = parseInt(inputPieces.value)  || 0;
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

        tr.querySelector('.btn-supprimer').addEventListener('click', function() {
            if (confirm('Supprimer cet article ?')) {
                tr.remove();
                calculerTotaux();
            }
        });

        index++;
        calculerTotaux();
    }

    // ── Calcul des totaux ──────────────────────────────────────────────────
    function calculerTotaux() {
        let totalPieces  = 0;
        let totalCartons = 0;
        let nbArticles   = 0;
        let stockDepasse = false;

        tbody.querySelectorAll('tr').forEach(tr => {
            const inputRef     = tr.querySelector('.input-ref');
            const inputCartons = tr.querySelector('.input-cartons');
            const inputPieces  = tr.querySelector('.input-pieces');
            const contenance   = parseInt(inputRef?.dataset.contenance || 0);
            const stock        = parseInt(inputRef?.dataset.stock || 0);
            const cartons      = parseInt(inputCartons?.value || 0);
            const pieces       = parseInt(inputPieces?.value  || 0);
            const total        = (cartons * contenance) + pieces;

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
            alertMsg.innerHTML  = '<i class="fas fa-exclamation-triangle"></i> Stock insuffisant !';
        } else {
            cardAlert.className = 'card bg-success text-white h-100';
            alertMsg.innerHTML  = '<i class="fas fa-check-circle"></i> Stock suffisant';
        }
    }

    btnAddManual.addEventListener('click', () => ajouterLigne());

    // ── ✅ Scanner ZXing — même principe que les entrées ───────────────────
    let zxReaderSortie  = null;
    let camStreamSortie = null;

    const btnScanSortie      = document.getElementById('btn-scan-sortie');
    const btnScanSortieClose = document.getElementById('btnScanSortieClose');
    const scanBoxSortie      = document.getElementById('scanBoxSortie');
    const scanStatusSortie   = document.getElementById('scanStatusSortie');
    const scanVideoSortie    = document.getElementById('scanVideoSortie');

    btnScanSortie.addEventListener('click', startScanSortie);
    btnScanSortieClose.addEventListener('click', stopScanSortie);

    async function startScanSortie() {
        scanBoxSortie.classList.remove('d-none');
        scanStatusSortie.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Accès à la caméra...';

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            scanStatusSortie.textContent = '❌ Caméra non disponible sur ce navigateur.';
            return;
        }

        try {
            camStreamSortie = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' },
                    width:  { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            });

            scanVideoSortie.srcObject = camStreamSortie;
            await scanVideoSortie.play();

            scanStatusSortie.innerHTML = '<i class="fas fa-camera"></i> Pointez vers le code-barres...';

            zxReaderSortie = new ZXing.BrowserMultiFormatReader();
            let lastCode = null, votes = 0;

            zxReaderSortie.decodeFromStream(camStreamSortie, scanVideoSortie, (result) => {
                if (!result) return;
                const code = result.getText();

                if (code === lastCode) { votes++; }
                else { lastCode = code; votes = 1; }

                scanStatusSortie.textContent = `Vérification... (${votes}/2) — ${code}`;

                if (votes >= 2) {
                    stopScanSortie();
                    const exact = articles.find(a =>
                        (a.code_barres && a.code_barres.toLowerCase() === code.toLowerCase()) ||
                        (a.reference   && a.reference.toLowerCase()   === code.toLowerCase())
                    );
                    if (exact) {
                        ajouterLigne(exact);
                        searchInput.value = '';
                        searchResults.style.display = 'none';
                    } else {
                        // Article non trouvé → on met le code dans la recherche
                        searchInput.value = code;
                        searchInput.dispatchEvent(new Event('input'));
                        searchInput.focus();
                    }
                }
            });

        } catch (err) {
            console.error('Erreur caméra:', err);
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                scanStatusSortie.textContent = '❌ Permission caméra refusée. Autorisez l\'accès dans les réglages.';
            } else if (err.name === 'NotFoundError') {
                scanStatusSortie.textContent = '❌ Aucune caméra détectée.';
            } else {
                scanStatusSortie.textContent = '❌ Erreur : ' + (err.message || err.name);
            }
        }
    }

    function stopScanSortie() {
        if (zxReaderSortie)  { try { zxReaderSortie.reset(); } catch(e) {} zxReaderSortie = null; }
        if (camStreamSortie) { camStreamSortie.getTracks().forEach(t => t.stop()); camStreamSortie = null; }
        scanVideoSortie.srcObject = null;
        scanBoxSortie.classList.add('d-none');
    }

    // ── Validation à la soumission ─────────────────────────────────────────
    document.getElementById('sortie-form').addEventListener('submit', function(e) {
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
            const inputRef     = tr.querySelector('.input-ref');
            const inputCartons = tr.querySelector('.input-cartons');
            const inputPieces  = tr.querySelector('.input-pieces');
            const stock        = parseInt(inputRef?.dataset.stock || 0);
            const contenance   = parseInt(inputRef?.dataset.contenance || 0);
            const cartons      = parseInt(inputCartons?.value || 0);
            const pieces       = parseInt(inputPieces?.value  || 0);
            const total        = (cartons * contenance) + pieces;
            if (stock > 0 && total > stock) stockDepasse = true;
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
    #search-results { border-radius: 0 0 8px 8px; }
    .list-group-item:hover { background-color: #e9f5ff; cursor: pointer; }
    .card-header { font-size: 1em; }
    .stock-badge { font-size: 0.95em; }
    #scanBoxSortie video { background: #000; }
</style>
@stop