@extends('adminlte::page')

@section('title','Nouvelle entrée')

@section('content_header')
    <h1>Créer une nouvelle entrée</h1>
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

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date de réception <span class="text-danger">*</span></label>
                            <input type="date" name="date_reception" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fournisseur <span class="text-danger">*</span></label>
                            <select name="fournisseur_id" class="form-control" required>
                                <option value="">-- Choisir un fournisseur --</option>
                                @foreach($fournisseurs as $f)
                                    <option value="{{ $f->id }}">{{ $f->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Commentaire</label>
                    <textarea name="commentaire" class="form-control" rows="2"></textarea>
                </div>
            </div>
        </div>

        {{-- TABLEAU DES ARTICLES --}}
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">
                    <i class="fas fa-boxes"></i> Articles reçus
                </h3>
                <button type="button" class="btn btn-success btn-sm float-right" id="btn-add">
                    <i class="fas fa-plus"></i> Ajouter un article
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="25%">Article</th>
                                <th width="10%" class="text-center">Cartons</th>
                                <th width="10%" class="text-center">Pièces</th>
                                <th width="12%" class="text-center">Prix unitaire (DZD)</th>
                                <th width="9%"  class="text-center">Remise (%)</th>
                                <th width="9%"  class="text-center">Prix net</th>
                                <th width="10%" class="text-center">Total pièces</th>
                                <th width="10%" class="text-center">Montant</th>
                                <th width="5%"  class="text-center"><i class="fas fa-trash"></i></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-articles">
                            <!-- lignes ajoutées dynamiquement -->
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="7" class="text-right">TOTAL BRUT :</td>
                                <td class="text-right"><span id="total-brut">0.00</span> DZD</td>
                                <td></td>
                            </tr>
                            <tr class="text-danger" id="row-remises-articles" style="display:none;">
                                <td colspan="7" class="text-right">Remises articles :</td>
                                <td class="text-right">- <span id="total-remises-articles">0.00</span> DZD</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="7" class="text-right">Sous-total après remises articles :</td>
                                <td class="text-right"><span id="sous-total">0.00</span> DZD</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- REMISE GLOBALE + TOTAL FINAL --}}
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4 offset-md-4">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">
                                <i class="fas fa-percent text-warning"></i>
                                Remise globale sur le bon (%) <small class="text-muted">— facultatif</small>
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       name="remise_globale"
                                       id="remise-globale"
                                       class="form-control text-center font-weight-bold"
                                       step="0.01" value="0" min="0" max="100"
                                       placeholder="0.00">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white mb-0">
                            <div class="card-body py-2 text-center">
                                <div class="text-sm">TOTAL NET À PAYER</div>
                                <div style="font-size: 1.6em; font-weight: bold;">
                                    <span id="total-net-final">0.00</span> DZD
                                </div>
                                <div id="info-remise-globale" class="text-sm" style="display:none; opacity:0.9;">
                                    Remise globale : - <span id="montant-remise-globale">0.00</span> DZD
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    let index = 0;
    const articles = @json($articles);
    const tbody = document.getElementById('tbody-articles');
    const btnAdd = document.getElementById('btn-add');
    const inputRemiseGlobale = document.getElementById('remise-globale');

    function getOptionsHTML() {
        let html = '<option value="">-- Sélectionner un article --</option>';
        articles.forEach(art => {
            html += `<option value="${art.id}" data-contenance="${art.contenance_carton}">
                ${art.nom} (Carton = ${art.contenance_carton} pcs)
            </option>`;
        });
        return html;
    }

    function ajouterLigne() {
        const tr = document.createElement('tr');
        tr.setAttribute('data-index', index);
        tr.innerHTML = `
            <td>
                <select name="articles[${index}][article_id]" class="form-control form-control-sm select-article" required>
                    ${getOptionsHTML()}
                </select>
            </td>
            <td>
                <input type="number" name="articles[${index}][quantite_cartons]"
                       class="form-control form-control-sm text-center input-cartons"
                       value="0" min="0" required>
            </td>
            <td>
                <input type="number" name="articles[${index}][quantite_pieces]"
                       class="form-control form-control-sm text-center input-pieces"
                       value="0" min="0" required>
            </td>
            <td>
                <input type="number" name="articles[${index}][prix_unitaire]"
                       class="form-control form-control-sm text-right input-prix"
                       step="0.01" value="0" min="0" required>
            </td>
            <td>
                <input type="number" name="articles[${index}][remise]"
                       class="form-control form-control-sm text-center input-remise"
                       step="0.01" value="0" min="0" max="100" placeholder="0">
            </td>
            <td class="text-center">
                <span class="prix-net text-success font-weight-bold">0.00</span>
            </td>
            <td class="text-center">
                <span class="badge badge-info total-pieces">0</span>
            </td>
            <td class="text-right">
                <strong class="montant-ligne">0.00</strong> DZD
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm btn-supprimer">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        const select       = tr.querySelector('.select-article');
        const inputCartons = tr.querySelector('.input-cartons');
        const inputPieces  = tr.querySelector('.input-pieces');
        const inputPrix    = tr.querySelector('.input-prix');
        const inputRemise  = tr.querySelector('.input-remise');
        const spanPrixNet  = tr.querySelector('.prix-net');
        const spanTotal    = tr.querySelector('.total-pieces');
        const spanMontant  = tr.querySelector('.montant-ligne');

        function calculer() {
            const option    = select.selectedOptions[0];
            const contenance = parseInt(option?.dataset.contenance || 0);
            const cartons   = parseInt(inputCartons.value) || 0;
            const pieces    = parseInt(inputPieces.value) || 0;
            const prix      = parseFloat(inputPrix.value) || 0;
            const remise    = parseFloat(inputRemise.value) || 0;

            const totalPieces = (cartons * contenance) + pieces;
            const prixNet     = prix * (1 - remise / 100);
            const montant     = totalPieces * prixNet;

            spanTotal.textContent   = totalPieces;
            spanPrixNet.textContent = prixNet.toFixed(2);
            spanMontant.textContent = montant.toFixed(2);

            calculerTotaux();
        }

        select.addEventListener('change', calculer);
        inputCartons.addEventListener('input', calculer);
        inputPieces.addEventListener('input', calculer);
        inputPrix.addEventListener('input', calculer);
        inputRemise.addEventListener('input', calculer);

        tr.querySelector('.btn-supprimer').addEventListener('click', function() {
            if (confirm('Supprimer cet article ?')) {
                tr.remove();
                calculerTotaux();
                if (tbody.children.length === 0) ajouterLigne();
            }
        });

        index++;
    }

    function calculerTotaux() {
        let totalBrut = 0;
        let sousTotal = 0;

        tbody.querySelectorAll('tr').forEach(tr => {
            const pieces  = parseInt(tr.querySelector('.total-pieces')?.textContent || 0);
            const prix    = parseFloat(tr.querySelector('.input-prix')?.value || 0);
            const montant = parseFloat(tr.querySelector('.montant-ligne')?.textContent || 0);
            totalBrut += pieces * prix;
            sousTotal += montant;
        });

        const remisesArticles = totalBrut - sousTotal;
        const remiseGlobale   = parseFloat(inputRemiseGlobale.value) || 0;
        const montantRemiseGlobale = sousTotal * (remiseGlobale / 100);
        const totalNetFinal   = sousTotal - montantRemiseGlobale;

        // Afficher total brut
        document.getElementById('total-brut').textContent = totalBrut.toFixed(2);

        // Afficher remises articles (si > 0)
        const rowRemisesArticles = document.getElementById('row-remises-articles');
        if (remisesArticles > 0.001) {
            rowRemisesArticles.style.display = '';
            document.getElementById('total-remises-articles').textContent = remisesArticles.toFixed(2);
        } else {
            rowRemisesArticles.style.display = 'none';
        }

        // Sous-total
        document.getElementById('sous-total').textContent = sousTotal.toFixed(2);

        // Total net final
        document.getElementById('total-net-final').textContent = totalNetFinal.toFixed(2);

        // Info remise globale
        const infoRemiseGlobale = document.getElementById('info-remise-globale');
        if (remiseGlobale > 0) {
            infoRemiseGlobale.style.display = '';
            document.getElementById('montant-remise-globale').textContent = montantRemiseGlobale.toFixed(2);
        } else {
            infoRemiseGlobale.style.display = 'none';
        }
    }

    // Recalculer quand remise globale change
    inputRemiseGlobale.addEventListener('input', calculerTotaux);

    // Bouton ajouter
    btnAdd.addEventListener('click', ajouterLigne);

    // Validation
    document.getElementById('entree-form').addEventListener('submit', function(e) {
        if (tbody.children.length === 0) {
            e.preventDefault();
            alert('Vous devez ajouter au moins un article !');
            return false;
        }
        let hasError = false;
        tbody.querySelectorAll('.select-article').forEach(select => {
            if (!select.value) {
                hasError = true;
                select.classList.add('is-invalid');
            }
        });
        if (hasError) {
            e.preventDefault();
            alert('Veuillez sélectionner un article pour chaque ligne !');
            return false;
        }
    });

    // Première ligne au chargement
    ajouterLigne();
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
    #total-net-final {
        font-size: 1.6em;
    }
</style>
@stop