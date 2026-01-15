@extends('adminlte::page')

@section('title', $supplier->nom)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Fiche fournisseur</h1>
        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Retour
        </a>
    </div>
@stop

@section('content')

{{-- Informations du fournisseur --}}
<div class="card">
    <div class="card-body">
        <div class="row">
            {{-- Photo --}}
            <div class="col-md-3 text-center">
                <img src="{{ $supplier->photo ? asset('storage/'.$supplier->photo) : asset('images/default-supplier.png') }}"
                     class="img-fluid rounded shadow"
                     style="max-height: 200px; object-fit: cover;">
            </div>

            {{-- Informations --}}
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3 pb-2 border-bottom">
                            <div class="text-muted small">
                                <i class="fas fa-building mr-2 text-info"></i>Nom
                            </div>
                            <div class="h4 mb-0 mt-1">{{ $supplier->nom }}</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3 pb-2 border-bottom">
                            <div class="text-muted small">
                                <i class="fas fa-tag mr-2 text-info"></i>Marque
                            </div>
                            <div class="h5 mb-0 mt-1">
                                <span class="badge badge-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                    {{ $supplier->marque ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3 pb-2 border-bottom">
                            <div class="text-muted small">
                                <i class="fas fa-phone mr-2 text-info"></i>Téléphone
                            </div>
                            <div class="h5 mb-0 mt-1">
                                {{ $supplier->telephone ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3 pb-2 border-bottom">
                            <div class="text-muted small">
                                <i class="fas fa-receipt mr-2 text-info"></i>Bons d'achat
                            </div>
                            <div class="h5 mb-0 mt-1">
                                <span class="badge badge-success" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                    {{ $nombreBons }} bon(s)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total des achats --}}
                <div class="mb-3">
                    <div class="text-muted small">
                        <i class="fas fa-dollar-sign mr-2 text-info"></i>Total des achats
                        <button class="btn btn-sm btn-outline-secondary ml-2" onclick="toggleTotal()" type="button">
                            <i class="fas fa-eye" id="toggle-total-icon"></i>
                        </button>
                    </div>
                    <div class="mt-1">
                        <span id="total-value" class="total-hidden h3 mb-0 text-success">
                            {{ number_format($totalAchats, 2) }} DA
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Historique des achats --}}
<div class="card mt-4">
    <div class="card-header bg-info">
        <h3 class="card-title text-white">
            <i class="fas fa-history mr-2"></i>
            Historique d'achat
        </h3>
    </div>
    <div class="card-body p-0">
        @if($entrees->isEmpty())
            <div class="p-4 text-center text-muted">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p>Aucun bon d'achat enregistré pour ce fournisseur.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th><i class="fas fa-hashtag mr-1"></i> N° Bon</th>
                            <th><i class="fas fa-calendar mr-1"></i> Date</th>
                            <th><i class="fas fa-boxes mr-1"></i> Articles</th>
                            <th><i class="fas fa-dollar-sign mr-1"></i> Montant</th>
                            <th><i class="fas fa-cog mr-1"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entrees as $entree)
                            <tr>
                                <td><code>#{{ $entree->id }}</code></td>
                                <td>{{ \Carbon\Carbon::parse($entree->date_reception)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $entree->articles->count() }} article(s)
                                    </span>
                                </td>
                                <td>
                                    <span class="montant-hidden">
                                        <strong>{{ number_format($entree->prix_total ?? 0, 2) }} DA</strong>
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('entrees.show', $entree->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-3">
                {{ $entrees->links() }}
            </div>
        @endif
    </div>
</div>

@stop

@section('css')
<style>
.total-hidden, .montant-hidden {
    filter: blur(5px);
    user-select: none;
}
</style>
@stop

@section('js')
<script>
// Toggle affichage du total
function toggleTotal() {
    var totalElement = document.getElementById('total-value');
    var icon = document.getElementById('toggle-total-icon');
    var montants = document.querySelectorAll('.montant-hidden');
    
    if(totalElement.classList.contains('total-hidden')) {
        totalElement.classList.remove('total-hidden');
        montants.forEach(m => m.classList.remove('montant-hidden'));
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        totalElement.classList.add('total-hidden');
        montants.forEach(m => m.classList.add('montant-hidden'));
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@stop