@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'roles'
])

@section('content')
<div class="content">
    <div class="container">
        <h1>Editar Rol</h1>
        
        <form action="{{ route('superadmin.roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $role->name }}" required>
            </div>
            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ $role->description }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="{{ route('superadmin.roles.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

@endsection