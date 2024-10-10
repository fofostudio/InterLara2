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
                        <h3 class="mb-0">Crear Nuevo Punto</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.points.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header">Información del Punto</div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="number">Número</label>
                                                <input type="text" class="form-control" id="number" name="number" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="description">Descripción</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="address">Dirección</label>
                                                <input type="text" class="form-control" id="address" name="address" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone">Teléfono</label>
                                                <input type="text" class="form-control" id="phone" name="phone">
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
                                                <input type="number" class="form-control" id="maxusers" name="maxusers">
                                            </div>
                                            <div class="form-group">
                                                <label for="dateStart">Fecha de inicio</label>
                                                <input type="date" class="form-control" id="dateStart" name="dateStart" value="{{ date('Y-m-d') }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="dateLimit">Fecha límite</label>
                                                <input type="date" class="form-control" id="dateLimit" name="dateLimit" value="{{ date('Y-m-d', strtotime('+1 month')) }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="status">Estado</label>
                                                <select class="form-control" id="status" name="status" required>
                                                    <option value="active" selected>Activo</option>
                                                    <option value="inactive">Inactivo</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-4">
                                <div class="card-header">Usuario Responsable</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="resp_user_name">Nombre</label>
                                                <input type="text" class="form-control" id="resp_user_name" name="resp_user_name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="resp_user_email">Email</label>
                                                <input type="email" class="form-control" id="resp_user_email" name="resp_user_email" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="resp_user_password">Contraseña</label>
                                                <input type="password" class="form-control" id="resp_user_password" name="resp_user_password" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Guardar</button>
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