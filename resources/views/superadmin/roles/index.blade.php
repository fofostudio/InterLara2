@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'roles'
])

@section('content')
<div class="content">
    <div class="container">
    <h1>Roles</h1>
    <a href="{{ route('superadmin.roles.create') }}" class="btn btn-primary mb-3">Crear Nuevo Rol</a>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ $role->name }}</td>
                <td>{{ $role->description }}</td>
                <td>
                    <a href="{{ route('superadmin.roles.edit', $role->id) }}" class="btn btn-sm btn-info">Editar</a>
                    <form action="{{ route('superadmin.roles.destroy', $role->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>

@endsection