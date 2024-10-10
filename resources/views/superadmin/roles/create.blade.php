@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'roles'
])

@section('content')
<div class="content">
    <div class="container">
    <h1>Crear Nuevo Rol</h1>
    
    <form action="{{ route('superadmin.roles.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Descripción</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('superadmin.roles.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</div>

@endsection