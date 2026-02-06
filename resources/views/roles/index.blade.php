@extends('adminlte::page')

@section('title', 'Gestion des Rôles')

@section('content_header')
    <h1>Gestion des Rôles et Permissions</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Liste des rôles</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="row">
                @foreach($roles as $role)
                    <div class="col-md-4">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-user-shield"></i> {{ $role->name }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Permissions ({{ $role->permissions->count() }}):</strong></p>
                                <div class="mb-3" style="max-height: 200px; overflow-y: auto;">
                                    @forelse($role->permissions as $permission)
                                        <span class="badge badge-info">{{ $permission->name }}</span>
                                    @empty
                                        <em class="text-muted">Aucune permission</em>
                                    @endforelse
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Modifier les permissions
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@stop