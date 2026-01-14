@extends('adminlte::page')

@section('title', 'Tableau de bord')

@section('content_header')
    <h1>Tableau de bord</h1>
@stop

@section('content')
<div class="row">

    {{-- Catégories --}}
    <div class="col-6 col-md-4 mb-4">
        <a href="{{ url('categories') }}" class="text-decoration-none">
            <div class="card text-white bg-warning shadow-lg  rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-tags fa-2x me-3"></i>
                    <div>
                        <h5 class="card-title">Stock</h5>
                        <p class="card-text">Gérer les catégories</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Articles --}}
    <div class="col-6 col-md-4 mb-4">
        <a href="{{ url('articles') }}" class="text-decoration-none">
            <div class="card text-white bg-info shadow-lg  rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-box fa-2x me-3"></i>
                    <div>
                        <h5 class="card-title">Articles</h5>
                        <p class="card-text">Gérer les articles</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Fournisseurs --}}
    <div class="col-6 col-md-4 mb-4">
        <a href="{{ url('suppliers') }}" class="text-decoration-none">
            <div class="card text-white bg-success shadow-lg  rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-truck fa-2x me-3"></i>
                    <div>
                        <h5 class="card-title">Fournisseurs</h5>
                        <p class="card-text">Gérer les fournisseurs</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Entrées --}}
    <div class="col-6 col-md-4 mb-4">
        <a href="{{ url('entrees') }}" class="text-decoration-none">
            <div class="card text-white bg-primary shadow-lg  rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-arrow-down fa-2x me-3"></i>
                    <div>
                        <h5 class="card-title">Entrées</h5>
                        <p class="card-text">Suivi des entrées</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Sorties --}}
    <div class="col-6 col-md-4 mb-4">
        <a href="{{ url('sorties') }}" class="text-decoration-none">
            <div class="card text-white bg-danger shadow-lg  rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-arrow-up fa-2x me-3"></i>
                    <div>
                        <h5 class="card-title">Sorties</h5>
                        <p class="card-text">Suivi des sorties</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Demandes --}}
    <div class="col-6 col-md-4 mb-4">
        <a href="{{ url('requests') }}" class="text-decoration-none">
            <div class="card text-white bg-secondary shadow-lg rounded-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-tasks fa-2x me-3"></i>
                    <div>
                        <h5 class="card-title">Demandes</h5>
                        <p class="card-text">Demandes internes</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

</div>
@stop
<style>
    .card {
    border-radius: 1.5rem !important; /* coins très arrondis */
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
</style>