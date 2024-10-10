@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'users'
])

@section('content')
<div class="content">
    <div class="container">
    <h1>Gestión de Usuarios (SuperAdmin)</h1>
    <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary mb-3">Crear Nuevo Usuario</a>
    
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Punto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->getRoleName() }}</td>
                <td>{{ $user->point ? $user->point->number : 'N/A' }}</td>
                <td>
                    <a href="{{ route('superadmin.users.edit', $user->id) }}" class="btn btn-sm btn-info">Editar</a>
                    <form action="{{ route('superadmin.users.destroy', $user->id) }}" method="POST" style="display: inline-block;">
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