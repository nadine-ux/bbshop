@extends('adminlte::page')

@section('title', 'Nouvelle demande')

@section('content_header')
    <h1>Nouvelle demande de réapprovisionnement</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if($articles->isEmpty())
    <div class="alert alert-info">Aucun article en base.</div>
@else
<div class="card">
    <div class="card-body">

        {{-- ═════════ SCAN ═════════ --}}
        <div class="mb-3">
            <label class="font-weight-bold"><i class="fas fa-barcode text-success"></i> Scanner un article en rupture</label>
            <div class="input-group">
                <input type="text" id="barcodeInput" class="form-control"
                       placeholder="Scanner (douchette) ou saisir un code-barres puis Entrée..."
                       autocomplete="off">
                <div class="input-group-append">
                    <button type="button" id="btnCamera" class="btn btn-success">
                        <i class="fas fa-camera"></i> Caméra
                    </button>
                </div>
            </div>
            <div id="scanMsg" class="mt-2 small"></div>

            @include('partials.barcode-scan')
        </div>

        <hr>

        {{-- ═════════ FILTRES ═════════ --}}
        <div class="form-row mb-3">
            <div class="col-md-5 mb-2">
                <input type="text" id="filtreNom" class="form-control" placeholder="🔍 Filtrer par nom d'article...">
            </div>
            <div class="col-md-3 mb-2">
                <select id="filtreEtat" class="form-control">
                    <option value="">Tous les états</option>
                    <option value="rupture">🔴 Rupture</option>
                    <option value="faible">⚠️ Faible</option>
                    <option value="normal">✅ Normal</option>
                </select>
            </div>
            <div class="col-md-4 mb-2 d-flex align-items-center">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="filtreCoches">
                    <label class="custom-control-label" for="filtreCoches">Articles cochés seulement</label>
                </div>
                <span class="ml-auto badge badge-primary" style="font-size:.9rem">
                    <span id="nbCoches">0</span> sélectionné(s)
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('demandes.store') }}">
            @csrf

            <div class="form-group col-md-4 px-0">
                <label>Gestionnaire destinataire</label>
                <select name="gestionnaire_id" class="form-control" required>
                    <option value="">-- Choisir --</option>
                    @foreach($gestionnaires as $g)
                        <option value="{{ $g->id }}" {{ old('gestionnaire_id') == $g->id ? 'selected' : '' }}>
                            {{ $g->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tableArticles">
                    <thead class="thead-dark">
                        <tr>
                            <th><input type="checkbox" id="check-all"></th>
                            <th>Article</th>
                            <th>Stock</th>
                            <th>État</th>
                            <th>Cartons</th>
                            <th>Pièces</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articles as $a)
                            <tr class="ligne-article"
                                data-nom="{{ \Illuminate\Support\Str::lower($a->nom) }}"
                                data-etat="{{ $a->etat }}"
                                data-codes="{{ $a->codes->implode('|') }}">
                                <td>
                                    <input type="checkbox" class="check-ligne"
                                           name="lignes[{{ $a->id }}][selected]" value="1"
                                           {{ old('lignes.'.$a->id.'.selected') ? 'checked' : '' }}>
                                </td>
                                <td>
                                    {{ $a->nom }}
                                    <small class="text-muted">({{ $a->contenance_carton ?: 1 }} pcs/carton)</small>
                                </td>
                                <td>{{ $a->stock }}</td>
                                <td>
                                    @if($a->etat === 'rupture')
                                        <span class="badge badge-danger">Rupture</span>
                                    @elseif($a->etat === 'faible')
                                        <span class="badge badge-warning">Faible</span>
                                    @else
                                        <span class="badge badge-success">Normal</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="number" min="0" class="form-control input-cartons" style="width:100px"
                                           name="lignes[{{ $a->id }}][cartons]"
                                           value="{{ old('lignes.'.$a->id.'.cartons', $a->cartons_suggeres) }}">
                                </td>
                                <td>
                                    <input type="number" min="0" class="form-control input-pieces" style="width:100px"
                                           name="lignes[{{ $a->id }}][pieces]"
                                           value="{{ old('lignes.'.$a->id.'.pieces', $a->pieces_suggerees) }}">
                                </td>
                            </tr>
                        @endforeach
                        <tr id="aucunResultat" style="display:none">
                            <td colspan="6" class="text-center text-muted">Aucun article ne correspond.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-group mt-3">
                <label>Remarque (facultatif)</label>
                <textarea name="remarque" rows="2" class="form-control">{{ old('remarque') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Envoyer la demande
            </button>
            <a href="{{ route('demandes.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>
@endif
@stop

@section('css')
<style>
    .ligne-article.scan-flash { background: #d4edda !important; transition: background .3s; }
    .btn-photo-scan { margin-top: .6rem; }
</style>
@stop

@section('js')
@include('partials.barcode-scan-js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    var rows      = Array.from(document.querySelectorAll('.ligne-article'));
    var inputCode = document.getElementById('barcodeInput');
    var msg       = document.getElementById('scanMsg');
    var fNom      = document.getElementById('filtreNom');
    var fEtat     = document.getElementById('filtreEtat');
    var fCoches   = document.getElementById('filtreCoches');
    var checkAll  = document.getElementById('check-all');

    // ───────── Filtres ─────────
    function appliquerFiltres() {
        var nom = fNom.value.trim().toLowerCase();
        var etat = fEtat.value;
        var seulementCoches = fCoches.checked;
        var visibles = 0;

        rows.forEach(function (tr) {
            var ok = (!nom || tr.dataset.nom.indexOf(nom) !== -1)
                  && (!etat || tr.dataset.etat === etat)
                  && (!seulementCoches || tr.querySelector('.check-ligne').checked);
            tr.style.display = ok ? '' : 'none';
            if (ok) visibles++;
        });
        document.getElementById('aucunResultat').style.display = visibles ? 'none' : '';
    }

    function majCompteur() {
        document.getElementById('nbCoches').textContent =
            rows.filter(function (tr) { return tr.querySelector('.check-ligne').checked; }).length;
    }

    [fNom, fEtat, fCoches].forEach(function (el) {
        el.addEventListener('input', appliquerFiltres);
        el.addEventListener('change', appliquerFiltres);
    });

    rows.forEach(function (tr) {
        tr.querySelector('.check-ligne').addEventListener('change', function () {
            majCompteur();
            if (fCoches.checked) appliquerFiltres();
        });
    });

    // Tout cocher : seulement les lignes visibles
    checkAll.addEventListener('change', function () {
        rows.forEach(function (tr) {
            if (tr.style.display !== 'none') tr.querySelector('.check-ligne').checked = checkAll.checked;
        });
        majCompteur();
    });

    // ───────── Scan ─────────
    function setMsg(texte, ok) {
        msg.innerHTML = texte;
        msg.className = 'mt-2 small ' + (ok ? 'text-success' : 'text-danger');
    }

    function traiterCode(code) {
        code = (code || '').trim();
        if (!code) return;

        var tr = rows.find(function (r) {
            return r.dataset.codes.split('|').indexOf(code) !== -1;
        });

        if (!tr) {
            setMsg('❌ Aucun article trouvé pour le code « ' + code + ' ».', false);
            return;
        }

        // Réinitialiser les filtres pour que la ligne soit visible
        fNom.value = ''; fEtat.value = ''; fCoches.checked = false;
        appliquerFiltres();

        var cb = tr.querySelector('.check-ligne');
        cb.checked = true;
        majCompteur();

        var nom = tr.children[1].childNodes[0].textContent.trim();
        var etat = tr.dataset.etat;
        setMsg('✅ ' + nom + ' ajouté à la demande' + (etat !== 'rupture' ? ' (attention : état ' + etat + ')' : '') + '.', true);

        tr.scrollIntoView({ behavior: 'smooth', block: 'center' });
        tr.classList.add('scan-flash');
        setTimeout(function () { tr.classList.remove('scan-flash'); }, 1800);

        // Si aucune quantité n'est prévue, on met le curseur sur "Pièces"
        var pieces = tr.querySelector('.input-pieces');
        var cartons = tr.querySelector('.input-cartons');
        if ((parseInt(pieces.value) || 0) === 0 && (parseInt(cartons.value) || 0) === 0) {
            pieces.focus(); pieces.select();
        }
    }

    // Caméra / photo : le partial appelle ce callback
    BarcodeScan.onDetected = function (code) {
        inputCode.value = code;
        traiterCode(code);
    };

    // Douchette USB ou saisie manuelle (la douchette envoie Entrée)
    inputCode.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            traiterCode(inputCode.value);
            inputCode.value = '';
            inputCode.focus();
        }
    });

    majCompteur();
});
</script>
@stop