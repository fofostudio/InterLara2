@extends('layouts.app', ['class' => '', 'elementActive' => 'points'])

@section('content')
<div class="content">
    <div class="container">
        <h1 class="mb-4">Puntos</h1>

        <!-- Indicadores estadísticos -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Puntos</h5>
                        <p class="card-text display-4">{{ $points->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Puntos Activos</h5>
                        <p class="card-text display-4">{{ $points->where('status', 'active')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Capacidad Total</h5>
                        <p class="card-text display-4">{{ $points->sum('maxusers') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Lista de Puntos</h3>
                <a href="{{ route('superadmin.points.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Crear Nuevo Punto
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Descripción</th>
                                <th>Dirección</th>
                                <th>Estado</th>
                                <th>Usuario Responsable</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($points as $point)
                            <tr>
                                <td>{{ $point->number }}</td>
                                <td>{{ Str::limit($point->description, 30) }}</td>
                                <td>{{ Str::limit($point->address, 30) }}</td>
                                <td>
                                    <span class="badge {{ $point->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $point->status }}
                                    </span>
                                </td>
                                <td>{{ $point->responsibleUser->name ?? 'No asignado' }}</td>
                                <td>
                                    <div class="btn-group" role="group" aria-label="Acciones">
                                        <a href="{{ route('superadmin.points.show', $point->id) }}" class="btn btn-sm btn-info" title="Ver">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('superadmin.points.edit', $point->id) }}" class="btn btn-sm btn-primary" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('superadmin.points.destroy', $point->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro?')">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
@endpush
