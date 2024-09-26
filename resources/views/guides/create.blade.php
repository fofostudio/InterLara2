@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'guides'
])

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Ingreso de Guías</h5>
                        <p class="card-category">Registre las nuevas guías aquí</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('guides.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="guide_number">Número de Guía</label>
                                        <input type="text" class="form-control" id="guide_number" name="guide_number" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="user_id">Operador</label>
                                        <select class="form-control" id="user_id" name="user_id" required>
                                            @foreach($operators as $operator)
                                                <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="value">Valor</label>
                                        <input type="number" class="form-control" id="value" name="value" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client">Cliente</label>
                                        <input type="text" class="form-control" id="client" name="client">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="observation">Observación</label>
                                        <textarea class="form-control" id="observation" name="observation" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Registrar Guía</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Últimas Guías Ingresadas</h5>
                        <p class="card-category">Resumen de las últimas 5 guías registradas</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="text-primary">
                                    <tr>
                                        <th>Número de Guía</th>
                                        <th>Operador</th>
                                        <th>Valor</th>
                                        <th>Cliente</th>
                                        <th>Fecha de Registro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($latestGuides as $guide)
                                    <tr>
                                        <td>{{ $guide->guide_number }}</td>
                                        <td>{{ $guide->user->name }}</td>
                                        <td>$ {{ number_format($guide->value, 2) }}</td>
                                        <td>{{ $guide->client }}</td>
                                        <td>{{ $guide->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center icon-warning">
                                    <i class="nc-icon nc-delivery-fast text-warning"></i>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="numbers">
                                    <p class="card-category">Total Guías Hoy</p>
                                    <p class="card-title">{{ $todayGuidesCount }}
                                    <p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-calendar-o"></i> Últimas 24 horas
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center icon-warning">
                                    <i class="nc-icon nc-money-coins text-success"></i>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="numbers">
                                    <p class="card-category">Valor Total Hoy</p>
                                    <p class="card-title">$ {{ number_format($todayGuidesValue, 2) }}
                                    <p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-calendar-o"></i> Últimas 24 horas
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center icon-warning">
                                    <i class="nc-icon nc-single-02 text-primary"></i>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="numbers">
                                    <p class="card-category">Operador Líder</p>
                                    <p class="card-title">{{ $topOperator->name ?? 'N/A' }}
                                    <p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-trophy"></i> Mayor número de guías hoy
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Aquí puedes agregar cualquier JavaScript específico para esta vista
        });
    </script>
@endpush
