@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'points'
])

@section('content')
<div class="content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Editar Punto</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.points.update', $point->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header">Información del Punto</div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="number">Número</label>
                                                <input type="text" class="form-control" id="number" name="number" value="{{ $point->number }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="description">Descripción</label>
                                                <textarea class="form-control" id="description" name="description" rows="3">{{ $point->description }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="address">Dirección</label>
                                                <input type="text" class="form-control" id="address" name="address" value="{{ $point->address }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone">Teléfono</label>
                                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $point->phone }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header">Configuración del Punto</div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="maxusers">Máximo de usuarios</label>
                                                <input type="number" class="form-control" id="maxusers" name="maxusers" value="{{ $point->maxusers }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="dateStart">Fecha de inicio</label>
                                                <input type="date" class="form-control" id="dateStart" name="dateStart" value="{{ $point->dateStart->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="dateLimit">Fecha límite</label>
                                                <input type="date" class="form-control" id="dateLimit" name="dateLimit" value="{{ $point->dateLimit->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="status">Estado</label>
                                                <select class="form-control" id="status" name="status" required>
                                                    <option value="active" {{ $point->status == 'active' ? 'selected' : '' }}>Activo</option>
                                                    <option value="inactive" {{ $point->status == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-4">
                                <div class="card-header">Usuario Responsable</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="responsible_user">Usuario Responsable Actual</label>
                                        <input type="text" class="form-control" id="responsible_user" value="{{ $point->responsibleUser->name ?? 'No asignado' }}" readonly>
                                    </div>
                                    <p class="mt-2 text-muted">Para cambiar el usuario responsable, por favor use la función correspondiente en la vista de detalles del punto.</p>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                                <a href="{{ route('superadmin.points.index') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection