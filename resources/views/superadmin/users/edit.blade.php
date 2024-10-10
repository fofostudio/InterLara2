@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'users'
])


@section('content')
<div class="content">
    <div class="container">
    <h1>Editar Usuario (SuperAdmin)</h1>
    
    <form action="{{ route('superadmin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
        </div>
        <div class="form-group">
            <label for="password">Nueva Contraseña (dejar en blanco para mantener la actual)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirmar Nueva Contraseña</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>
        <div class="form-group">
            <label for="role_id">Rol</label>
            <select class="form-control" id="role_id" name="role_id" required>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="point_id">Punto</label>
            <select class="form-control" id="point_id" name="point_id">
                <option value="">Seleccionar Punto</option>
                @foreach($points as $point)
                    <option value="{{ $point->id }}" {{ $user->point_id == $point->id ? 'selected' : '' }}>{{ $point->number }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="address">Dirección</label>
            <input type="text" class="form-control" id="address" name="address" value="{{ $user->address }}">
        </div>
        <div class="form-group">
            <label for="phone">Teléfono</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}">
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('superadmin.users.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</div>

@endsection