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
                                <th width="30%">Article</th>
                                <th width="12%" class="text-center">Cartons</th>
                                <th width="12%" class="text-center">Pièces</th>
                                <th width="15%" class="text-center">Prix unitaire (DZD)</th>
                                <th width="12%" class="text-center">Total pièces</th>
                                <th width="14%" class="text-center">Montant</th>
                                <th width="5%" class="text-center">
                                    <i class="fas fa-trash"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tbody-articles">
                            <!-- Les lignes seront ajoutées ici -->
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <td colspan="4" class="text-right">TOTAL GÉNÉRAL :</td>
                                <td class="text-center">
                                    <span id="grand-total-pieces" class="badge badge-primary">0</span>
                                </td>
                                <td class="text-center">
                                    <span id="grand-total-montant">0.00</span> DZD
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
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

    // Créer les options pour le select
    function getOptionsHTML() {
        let html = '<option value="">-- Sélectionner un article --</option>';
        articles.forEach(art => {
            html += `<option value="${art.id}" data-contenance="${art.contenance_carton}">
                ${art.nom} (Carton = ${art.contenance_carton} pcs)
            </option>`;
        });
        return html;
    }

    // Ajouter une ligne
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
                <input type="number" 
                       name="articles[${index}][quantite_cartons]" 
                       class="form-control form-control-sm text-center input-cartons" 
                       value="0" min="0" required>
            </td>
            <td>
                <input type="number" 
                       name="articles[${index}][quantite_pieces]" 
                       class="form-control form-control-sm text-center input-pieces" 
                       value="0" min="0" required>
            </td>
            <td>
                <input type="number" 
                       name="articles[${index}][prix_unitaire]" 
                       class="form-control form-control-sm text-right input-prix" 
                       step="0.01" value="0" min="0" required>
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

        // Attacher les événements
        const select = tr.querySelector('.select-article');
        const inputCartons = tr.querySelector('.input-cartons');
        const inputPieces = tr.querySelector('.input-pieces');
        const inputPrix = tr.querySelector('.input-prix');
        const spanTotal = tr.querySelector('.total-pieces');
        const spanMontant = tr.querySelector('.montant-ligne');
        const btnSupprimer = tr.querySelector('.btn-supprimer');

        function calculer() {
            const option = select.selectedOptions[0];
            const contenance = parseInt(option?.dataset.contenance || 0);
            const cartons = parseInt(inputCartons.value) || 0;
            const pieces = parseInt(inputPieces.value) || 0;
            const prix = parseFloat(inputPrix.value) || 0;

            const totalPieces = (cartons * contenance) + pieces;
            const montant = totalPieces * prix;

            spanTotal.textContent = totalPieces;
            spanMontant.textContent = montant.toFixed(2);

            calculerGrandTotal();
        }

        select.addEventListener('change', calculer);
        inputCartons.addEventListener('input', calculer);
        inputPieces.addEventListener('input', calculer);
        inputPrix.addEventListener('input', calculer);

        btnSupprimer.addEventListener('click', function() {
            if (confirm('Supprimer cet article ?')) {
                tr.remove();
                calculerGrandTotal();
                
                // Si plus de lignes, en ajouter une
                if (tbody.children.length === 0) {
                    ajouterLigne();
                }
            }
        });

        index++;
    }

    // Calculer les totaux généraux
    function calculerGrandTotal() {
        let totalPieces = 0;
        let totalMontant = 0;

        tbody.querySelectorAll('tr').forEach(tr => {
            const pieces = parseInt(tr.querySelector('.total-pieces')?.textContent || 0);
            const montant = parseFloat(tr.querySelector('.montant-ligne')?.textContent || 0);
            totalPieces += pieces;
            totalMontant += montant;
        });

        document.getElementById('grand-total-pieces').textContent = totalPieces;
        document.getElementById('grand-total-montant').textContent = totalMontant.toFixed(2);
    }

    // Bouton ajouter
    btnAdd.addEventListener('click', ajouterLigne);

    // Validation du formulaire
    document.getElementById('entree-form').addEventListener('submit', function(e) {
        console.log('Formulaire soumis');
        console.log('Nombre de lignes:', tbody.children.length);
        
        if (tbody.children.length === 0) {
            e.preventDefault();
            alert('Vous devez ajouter au moins un article !');
            return false;
        }

        // Vérifier que tous les selects ont une valeur
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
        
        // Afficher ce qui sera envoyé
        const formData = new FormData(this);
        console.log('Données du formulaire:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
    });

    // Ajouter la première ligne au chargement
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
    #grand-total-pieces {
        font-size: 1.1em;
        padding: 6px 12px;
    }
    #grand-total-montant {
        font-size: 1.2em;
        color: #28a745;
    }
</style>
@stop