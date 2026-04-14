@extends('adminlte::page')

@section('title','Nouvelle commande')

@section('content_header')
    <h1>Créer une nouvelle Commande</h1>
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

    <form action="{{ route('commandes.store') }}" method="POST" id="commande-form">
        @csrf

        {{-- BARRE DE RECHERCHE / SCAN --}}
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3 class="card-title">
                    <i class="fas fa-search"></i> Rechercher un article
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
                                   placeholder="Écrire le nom ou le code-barres..."
                                   autocomplete="off">
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Tapez le nom, la référence ou scannez le code-barres pour ajouter un article.
                        </small>
                    </div>
                    <div class="col-md-4" style="position:relative;">
                        <div id="search-results"
                             class="list-group shadow"
                             style="display:none; max-height:250px; overflow-y:auto;
                                    position:absolute; z-index:1000; width:100%; top:0;">
                        </div>
                        <button type="button" class="btn btn-warning btn-lg w-100" id="btn-scanner">
                            <i class="fas fa-qrcode"></i> Scanner
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLEAU DES ARTICLES --}}
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">
                    <i class="fas fa-boxes"></i> Articles à commander
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0" id="table-articles">
                        <thead class="thead-dark">
                            <tr>
                                <th class="text-center">Nom d'article</th>
                                <th class="text-center">Code AL</th>
                                <th class="text-center">Quantité reçue</th>
                                <th class="text-center">Quantité d'alerte</th>
                                <th class="text-center">Quantité à commander</th>
                                <th class="text-center"><i class="fas fa-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-articles">
                            {{-- lignes ajoutées dynamiquement --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- BOUTONS --}}
        <div class="text-right mb-4">
            <button type="submit" class="btn btn-success btn-lg px-5">
                <i class="fas fa-check"></i> Valider
            </button>
            <a href="{{ route('commandes.index') }}" class="btn btn-secondary btn-lg px-5">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>

    </form>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let index = 0;
    const tbody        = document.getElementById('tbody-articles');
    const searchInput  = document.getElementById('search-article');
    const searchResults= document.getElementById('search-results');
    const btnScanner   = document.getElementById('btn-scanner');

    // ── Articles data from PHP ─────────────────────────────────────────────
    const articles = @json($articlesData);

    // ── Barre de recherche ─────────────────────────────────────────────────
    searchInput.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        searchResults.innerHTML = '';
        if (q.length < 1) { searchResults.style.display = 'none'; return; }

        const filtered = articles.filter(a =>
            a.nom.toLowerCase().includes(q) ||
            (a.code_al    && a.code_al.toLowerCase().includes(q)) ||
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
            btn.type = 'button';
            btn.className = 'list-group-item list-group-item-action';
            btn.innerHTML = `
                <strong>${art.nom}</strong>
                <span class="badge badge-secondary float-right">${art.code_al || art.reference || art.code_barres || ''}</span>
                <br>
                <small class="text-muted">
                    Stock: <strong class="${art.stock > 0 ? 'text-success' : 'text-danger'}">${art.stock} pcs</strong>
                    — Alerte: <strong class="text-warning">${art.quantite_alerte ?? 0} pcs</strong>
                </small>
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

    // Fermer la liste si clic ailleurs
    document.addEventListener('click', function (e) {
        if (!searchResults.contains(e.target) && e.target !== searchInput) {
            searchResults.style.display = 'none';
        }
    });

    // Scanner : Entrée sur code-barres exact
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const q = this.value.trim().toLowerCase();
            const exact = articles.find(a =>
                (a.code_barres && a.code_barres.toLowerCase() === q) ||
                (a.code_al     && a.code_al.toLowerCase()     === q) ||
                (a.reference   && a.reference.toLowerCase()   === q)
            );
            if (exact) {
                ajouterLigne(exact);
                this.value = '';
                searchResults.style.display = 'none';
            }
        }
    });

    // Bouton Scanner : focus sur le champ (prêt pour douchette)
    btnScanner.addEventListener('click', function () {
        searchInput.focus();
        searchInput.select();
    });

    // ── Ajouter une ligne ──────────────────────────────────────────────────
    function ajouterLigne(art = null) {
        const tr = document.createElement('tr');
        tr.setAttribute('data-index', index);

        const artId      = art ? art.id : '';
        const artNom     = art ? art.nom : '';
        const artCodeAL  = art ? (art.code_al || art.reference || art.code_barres || '') : '';
        const artStock   = art ? (art.stock ?? 0) : 0;
        const artAlerte  = art ? (art.quantite_alerte ?? 0) : 0;

        // Suggestion auto : max(0, alerte - stock)
        const suggQte    = Math.max(0, artAlerte - artStock);

        tr.innerHTML = `
            <td>
                <input type="hidden" name="articles[${index}][article_id]" class="input-article-id" value="${artId}">
                <input type="text"
                       class="form-control form-control-sm input-nom"
                       value="${artNom}"
                       placeholder="Nom d'article..."
                       readonly style="background:#f8f9fa;">
            </td>
            <td>
                <input type="text"
                       class="form-control form-control-sm text-center input-code-al"
                       value="${artCodeAL}"
                       placeholder="Code AL"
                       readonly style="background:#f8f9fa;">
            </td>
            <td class="text-center align-middle">
                <span class="badge badge-${artStock > 0 ? 'success' : 'danger'} p-2">
                    ${artStock} pcs
                </span>
            </td>
            <td class="text-center align-middle">
                <span class="badge badge-warning p-2 text-dark">
                    ${artAlerte} pcs
                </span>
            </td>
            <td>
                <input type="number"
                       name="articles[${index}][quantite_commande]"
                       class="form-control form-control-sm text-center input-qte font-weight-bold"
                       value="${suggQte}" min="1">
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-danger btn-sm btn-supprimer">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        tr.querySelector('.btn-supprimer').addEventListener('click', function () {
            if (confirm('Supprimer cet article ?')) tr.remove();
        });

        // Highlight rouge si stock <= alerte
        if (artStock <= artAlerte && artAlerte > 0) {
            tr.classList.add('table-warning');
        }

        index++;
    }

    // ── Validation à la soumission ─────────────────────────────────────────
    document.getElementById('commande-form').addEventListener('submit', function (e) {
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

        // Vérifier quantités > 0
        let qteInvalide = false;
        tbody.querySelectorAll('.input-qte').forEach(inp => {
            if (parseInt(inp.value) < 1) qteInvalide = true;
        });
        if (qteInvalide) {
            e.preventDefault();
            alert('La quantité à commander doit être supérieure à 0 pour chaque article.');
            return false;
        }
    });
});
</script>
@stop

@section('css')
<style>
    .table td, .table th {
        vertical-align: middle !important;
    }
    .is-invalid {
        border-color: #dc3545 !important;
    }
    #search-results {
        border-radius: 0 0 8px 8px;
    }
    .list-group-item:hover {
        background-color: #e9f5ff;
        cursor: pointer;
    }
    .card-header {
        font-size: 1em;
    }
</style>
@stop