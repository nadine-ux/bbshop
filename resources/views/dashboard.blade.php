@extends('adminlte::page')

@section('title', 'Tableau de bord')

@section('content_header')
    <h1>Tableau de bord</h1>
@stop

@section('content')
<div class="row">
    {{-- Articles en stock --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>150</h3>
                <p>Articles en stock</p>
            </div>
            <div class="icon"><i class="fas fa-boxes"></i></div>
            <a href="{{ route('stock.index') }}" class="small-box-footer">
                Plus d’infos <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    {{-- Demandes en attente --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>53</h3>
                <p>Demandes en attente</p>
            </div>
            <div class="icon"><i class="fas fa-file-alt"></i></div>
            <a href="" class="small-box-footer">
                Voir <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>
@stop
