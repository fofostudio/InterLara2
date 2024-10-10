@extends('layouts.app', ['class' => '', 'elementActive' => 'points'])

@section('content')
<div class="content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Detalles del Punto</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">Información del Punto</div>
                                    <div class="card-body">
                                        <p><strong>Número:</strong> #{{ $point->number }}</p>
                                        <p><strong>Descripción:</strong> {{ $point->description }}</p>
                                        <p><strong>Dirección:</strong> {{ $point->address }}</p>
                                        <p><strong>Teléfono:</strong> {{ $point->phone }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">Configuración del Punto</div>
                                    <div class="card-body">
                                        <p><strong>Máximo de usuarios:</strong> {{ $point->maxusers }}</p>
                                        <p><strong>Fecha de inicio:</strong> {{ $point->dateStart->format('d/m/Y') }}</p>
                                        <p><strong>Fecha límite:</strong> {{ $point->dateLimit->format('d/m/Y') }}</p>
                                        <p>
                                            <strong>Estado:</strong>
                                            <span class="badge {{ $point->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $point->status }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header">Usuario Responsable</div>
                            <div class="card-body">
                                <p><strong>Nombre:</strong> {{ $point->responsibleUser->name ?? 'No asignado' }}</p>
                                <p><strong>Email:</strong> {{ $point->responsibleUser->email ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">Usuarios Asignados</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Email</th>
                                                <th>Rol</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($point->users as $user)
                                                <tr>
                                                    <td>{{ $user->id }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->role->name }}</td>
                                                    <td>
                                                        <form action="{{ route('superadmin.points.removeUser', [$point->id, $user->id]) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="bi bi-person-x-fill"></i> Remover
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">Asignar Usuario</div>
                            <div class="card-body">
                                <form action="{{ route('superadmin.points.assignUser', $point->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <select name="user_id" class="form-select">
                                            <option selected disabled>Seleccionar usuario</option>
                                            @foreach ($availableUsers as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-person-plus-fill"></i> Asignar Usuario
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('superadmin.points.edit', $point->id) }}" class="btn btn-primary">
                                <i class="bi bi-pencil-fill"></i> Editar
                            </a>
                            <a href="{{ route('superadmin.points.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
@endpush