@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'users'
])


@section('content')
<div class="content">
    <div class="container">
    <h1>Crear Nuevo Usuario (SuperAdmin)</h1>
    
    <form action="{{ route('superadmin.users.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirmar Contraseña</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>
        <div class="form-group">
            <label for="role_id">Rol</label>
            <select class="form-control" id="role_id" name="role_id" required>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="point_id">Punto</label>
            <select class="form-control" id="point_id" name="point_id">
                <option value="">Seleccionar Punto</option>
                @foreach($points as $point)
                    <option value="{{ $point->id }}">{{ $point->number }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="address">Dirección</label>
            <input type="text" class="form-control" id="address" name="address">
        </div>
        <div class="form-group">
            <label for="phone">Teléfono</label>
            <input type="text" class="form-control" id="phone" name="phone">
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('superadmin.users.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</div>

@endsection