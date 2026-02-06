@extends('adminlte::page')

@section('title', 'Modifier les Permissions')

@section('content_header')
    <h1>Modifier les Permissions - {{ $role->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Permissions pour le rôle: <strong>{{ $role->name }}</strong></h3>
        </div>
        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                @foreach($permissions as $category => $perms)
                    <div class="card card-outline card-secondary mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $category }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($perms as $permission)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   class="form-check-input" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->name }}"
                                                   id="perm_{{ $permission->id }}"
                                                   {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach

                @error('permissions')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer les permissions
                </button>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card-outline');
        cards.forEach(card => {
            const header = card.querySelector('.card-header');
            const checkboxes = card.querySelectorAll('input[type="checkbox"]');
            
            const btnGroup = document.createElement('div');
            btnGroup.className = 'float-right';
            btnGroup.innerHTML = `
                <button type="button" class="btn btn-xs btn-success select-all">Tout</button>
                <button type="button" class="btn btn-xs btn-warning deselect-all">Aucun</button>
            `;
            header.appendChild(btnGroup);
            
            btnGroup.querySelector('.select-all').addEventListener('click', () => {
                checkboxes.forEach(cb => cb.checked = true);
            });
            
            btnGroup.querySelector('.deselect-all').addEventListener('click', () => {
                checkboxes.forEach(cb => cb.checked = false);
            });
        });
    });
</script>
@stop