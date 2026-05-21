@extends('adminlte::page')

@section('title','Sorties')

@section('content_header')
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

<div class="inv-wrap">

    {{-- TOPBAR --}}
    <div class="inv-topbar">
        <div>
            <h4 class="inv-title">Liste des sorties</h4>
            <small class="text-muted">Gestion des bons de sortie</small>
        </div>
        <a href="{{ route('sorties.create') }}" class="btn-inv-primary">
            <i class="fas fa-plus"></i> Nouvelle sortie
        </a>
    </div>

    {{-- FILTRES --}}
    <div class="inv-card mb-3">
        <form method="GET" action="{{ route('sorties.index') }}" class="filters-row">

            <div class="field">
                <label><i class="fas fa-user fa-xs"></i> Gestionnaire</label>
                <input type="text" name="gestionnaire"
                       placeholder="Rechercher..."
                       value="{{ request('gestionnaire') }}">
            </div>

            <div class="field">
                <label><i class="fas fa-calendar-day fa-xs"></i> Mois</label>
                <select name="mois">
                    <option value="">— Tous —</option>
                    @foreach([1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',
                              7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre']
                             as $num => $nom)
                        <option value="{{ $num }}" {{ request('mois') == $num ? 'selected' : '' }}>{{ $nom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label><i class="fas fa-calendar-alt fa-xs"></i> Année</label>
                <select name="annee">
                    <option value="">— Toutes —</option>
                    @foreach($annees as $annee)
                        <option value="{{ $annee }}" {{ request('annee') == $annee ? 'selected' : '' }}>{{ $annee }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label><i class="fas fa-sort fa-xs"></i> Trier par</label>
                <select name="sort">
                    <option value="date_sortie"   {{ request('sort','date_sortie')==='date_sortie' ? 'selected':'' }}>Date</option>
                    <option value="id"             {{ request('sort')==='id'           ? 'selected':'' }}>N° Bon</option>
                    <option value="gestionnaire"   {{ request('sort')==='gestionnaire' ? 'selected':'' }}>Gestionnaire</option>
                </select>
            </div>

            <div class="field" style="max-width:90px">
                <label>&nbsp;</label>
                <select name="direction">
                    <option value="desc" {{ request('direction','desc')==='desc' ? 'selected':'' }}>↓ Desc</option>
                    <option value="asc"  {{ request('direction')==='asc'         ? 'selected':'' }}>↑ Asc</option>
                </select>
            </div>

            <div class="field" style="max-width:120px">
                <label>&nbsp;</label>
                <div style="display:flex;gap:6px">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <a href="{{ route('sorties.index') }}" class="btn-reset">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>

        </form>
    </div>

    {{-- COMPTEUR --}}
    <div class="mb-2">
        <small class="text-muted">
            <strong class="text-dark">{{ $sorties->total() }}</strong> sortie(s) trouvée(s)
            @if(request()->hasAny(['gestionnaire','mois','annee']))
                &mdash; <a href="{{ route('sorties.index') }}" class="text-danger" style="font-size:11px;">
                    <i class="fas fa-times-circle"></i> Effacer les filtres
                </a>
            @endif
        </small>
    </div>

    {{-- TABLEAU --}}
    <div class="inv-card">
        <div class="table-responsive">
            <table class="inv-table">
                <thead>
                    <tr>
                        <th style="width:90px">N° Bon</th>
                        <th style="width:130px">Date de sortie</th>
                        <th>Gestionnaire</th>
                        <th style="width:110px;text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($sorties as $sortie)
                    <tr>
                        <td>
                            <span class="bon-num">#{{ $sortie->numero_bon ?? $sortie->id }}</span>
                        </td>

                        <td class="text-muted-sm">
                            {{ \Carbon\Carbon::parse($sortie->date_sortie ?? $sortie->created_at)->format('d/m/Y') }}
                        </td>

                        <td>
    @if($sortie->gestionnaire)
        <div style="display:flex;align-items:center;gap:8px">
            <div class="avatar-circle avatar-sortie">
                {{ strtoupper(substr($sortie->gestionnaire->name, 0, 2)) }}
            </div>
            <span style="font-weight:500;font-size:13px">
                {{ $sortie->gestionnaire->name }}
            </span>
        </div>
    @else
        <span class="text-muted">—</span>
    @endif
</td>

                        <td style="text-align:center">
                            <div style="display:flex;justify-content:center;gap:5px">
                                <a href="{{ route('sorties.show', $sortie) }}" class="action-btn action-view" title="Voir">
                                    <i class="fas fa-eye" style="font-size:11px"></i>
                                </a>
                                <a href="{{ route('sorties.edit', $sortie) }}" class="action-btn action-edit" title="Modifier">
                                    <i class="fas fa-edit" style="font-size:11px"></i>
                                </a>
                                <form action="{{ route('sorties.destroy', $sortie) }}" method="POST" style="display:inline"
                                      onsubmit="return confirm('Supprimer cette sortie ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn action-delete" title="Supprimer">
                                        <i class="fas fa-trash" style="font-size:11px"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:3rem;color:#aaa">
                            <i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.4"></i>
                            Aucune sortie enregistrée
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($sorties->hasPages())
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.75rem 1.25rem;border-top:0.5px solid #f0f0f0">
            <small class="text-muted">Page {{ $sorties->currentPage() }} sur {{ $sorties->lastPage() }} — {{ $sorties->total() }} résultats</small>
            {{ $sorties->links() }}
        </div>
        @endif
    </div>

</div>
@stop

@section('css')
<style>
.inv-wrap { padding: .5rem 0; }

.inv-topbar {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 1.25rem;
}
.inv-title { font-size: 18px; font-weight: 500; margin: 0; color: #1a1a1a; }

.btn-inv-primary {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 16px; border-radius: 8px;
    background: #185FA5; color: #e6f1fb;
    font-size: 13px; text-decoration: none; border: none;
    transition: background .15s;
}
.btn-inv-primary:hover { background: #0C447C; color: #fff; text-decoration: none; }

.inv-card {
    background: #fff; border: 0.5px solid #e0e0e0;
    border-radius: 12px; overflow: hidden;
}

.filters-row {
    display: flex; flex-wrap: wrap; gap: 10px;
    align-items: flex-end; padding: 1rem 1.25rem;
}
.field { display: flex; flex-direction: column; flex: 1; min-width: 130px; }
.field label { font-size: 11px; color: #888; margin-bottom: 4px; }
.field input, .field select {
    height: 34px; border: 0.5px solid #d0d0d0; border-radius: 8px;
    padding: 0 10px; font-size: 13px; background: #fff; color: #333;
    width: 100%;
}
.field input:focus, .field select:focus {
    outline: none; border-color: #378ADD;
    box-shadow: 0 0 0 2px rgba(55,138,221,.15);
}
.btn-filter {
    height: 34px; padding: 0 14px; background: #185FA5; color: #e6f1fb;
    border: none; border-radius: 8px; font-size: 13px; cursor: pointer;
    display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;
}
.btn-filter:hover { background: #0C447C; }
.btn-reset {
    height: 34px; width: 34px; display: inline-flex; align-items: center;
    justify-content: center; border: 0.5px solid #d0d0d0; border-radius: 8px;
    background: #fff; color: #666; text-decoration: none;
}
.btn-reset:hover { background: #f5f5f5; color: #333; text-decoration: none; }

.inv-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.inv-table thead tr { background: #fafafa; }
.inv-table thead th {
    padding: 9px 14px; text-align: left; font-weight: 500;
    font-size: 11px; color: #888; border-bottom: 0.5px solid #e8e8e8;
    text-transform: uppercase; letter-spacing: .4px; white-space: nowrap;
}
.inv-table tbody tr { border-bottom: 0.5px solid #f0f0f0; transition: background .1s; }
.inv-table tbody tr:hover { background: #fafafa; }
.inv-table tbody tr:last-child { border-bottom: none; }
.inv-table tbody td { padding: 10px 14px; vertical-align: middle; color: #1a1a1a; }

.bon-num { font-weight: 600; color: #185FA5; font-size: 13px; }
.text-muted-sm { font-size: 13px; color: #666; }

.avatar-circle {
    width: 28px; height: 28px; border-radius: 50%;
    background: #e6f1fb; color: #185FA5;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 600; flex-shrink: 0;
}
/* couleur différente pour les sorties */
.avatar-sortie {
    background: #faeeda; color: #854F0B;
}

.action-btn {
    width: 30px; height: 30px; padding: 0;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 6px; cursor: pointer; text-decoration: none;
    border: none; transition: opacity .15s;
}
.action-btn:hover { opacity: .8; }
.action-view   { background: #e6f1fb; color: #185FA5; }
.action-edit   { background: #faeeda; color: #854F0B; }
.action-delete { background: #fcebeb; color: #A32D2D; }
</style>
@stop