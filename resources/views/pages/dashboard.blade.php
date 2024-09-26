@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'dashboard',
])

@section('content')
    <div class="content">
        <!-- Datos Globales -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5 col-md-4">
                                <div class="icon-big text-center icon-warning">
                                    <i class="nc-icon nc-globe text-warning"></i>
                                </div>
                            </div>
                            <div class="col-7 col-md-8">
                                <div class="numbers">
                                    <p class="card-category">Guías Registradas</p>
                                    <p class="card-title" id="totalGuides">{{ number_format($initialData['totalGuides']) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-refresh"></i> Actualizado ahora
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5 col-md-4">
                                <div class="icon-big text-center icon-warning">
                                    <i class="nc-icon nc-money-coins text-success"></i>
                                </div>
                            </div>
                            <div class="col-7 col-md-8">
                                <div class="numbers">
                                    <p class="card-category">Total Deudas</p>
                                    <p class="card-title" id="totalDebts">
                                       $ {{ number_format($initialData['totalDebts'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-calendar-o"></i> Último registro
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5 col-md-4">
                                <div class="icon-big text-center icon-warning">
                                    <i class="nc-icon nc-vector text-danger"></i>
                                </div>
                            </div>
                            <div class="col-7 col-md-8">
                                <div class="numbers">
                                    <p class="card-category">Guías Hoy</p>
                                    <p class="card-title" id="guidesToday">{{ $initialData['guidesToday'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-clock-o"></i> En las últimas 24 horas
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5 col-md-4">
                                <div class="icon-big text-center icon-warning">
                                    <i class="nc-icon nc-favourite-28 text-primary"></i>
                                </div>
                            </div>
                            <div class="col-7 col-md-8">
                                <div class="numbers">
                                    <p class="card-category">Operadores Activos</p>
                                    <p class="card-title" id="activeOperators">{{ $initialData['activeOperators'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-refresh"></i> Actualizado ahora
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 5 Deudas y Guías -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Top 5 Deudas por Usuario (Mes Actual)</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled team-members" id="topDebts">
                            @foreach ($initialData['topDebts'] as $debt)
                                <li>
                                    <div class="row">
                                        <div class="col-md-7 col-7">{{ $debt['name'] }}</div>
                                        <div class="col-md-5 col-5 text-right">
                                            $ {{ number_format($debt['amount'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Top 5 Guías Registradas por Usuario (Mes Actual)</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled team-members" id="topGuides">
                            @foreach ($initialData['topGuides'] as $guide)
                                <li>
                                    <div class="row">
                                        <div class="col-md-7 col-7">
                                            {{ $guide['name'] }}
                                            <br />
                                            <span class="text-muted"><small>{{ $guide['guideCount'] }} guías</small></span>
                                        </div>
                                        <div class="col-md-5 col-5 text-right">
                                            {{ number_format($guide['guideCount'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos con Selector de Mes -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Guías Registradas por Día</h5>
                        <select class="form-control month-selector" data-target="chartGuides">
                            <!-- Opciones de mes se llenarán con JavaScript -->
                        </select>
                    </div>
                    <div class="card-body">
                        <canvas id="chartGuides"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Deudas Registradas por Día</h5>
                        <select class="form-control month-selector" data-target="chartDebts">
                            <!-- Opciones de mes se llenarán con JavaScript -->
                        </select>
                    </div>
                    <div class="card-body">
                        <canvas id="chartDebts"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Valor Transporte por Usuario (Excel)</h5>
                        <select class="form-control month-selector" data-target="excelTransportTable">
                            <!-- Opciones de mes se llenarán con JavaScript -->
                        </select>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="excelTransportTable">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Valor Transporte</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($initialData['excelTransportValueByUser'] as $user => $value)
                                        <tr>
                                            <td>{{ $user }}</td>
                                            <td>{{ number_format($value, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Valor Transporte por Usuario (Guías Registradas)</h5>
                        <select class="form-control month-selector" data-target="guideTransportTable">
                            <!-- Opciones de mes se llenarán con JavaScript -->
                        </select>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="guideTransportTable">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Valor Transporte</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Se llenará dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            var charts = {};

            function initializeMonthSelectors() {
                $('.month-selector').each(function() {
                    for (let i = 11; i >= 0; i--) {
                        let date = new Date();
                        date.setMonth(date.getMonth() - i);
                        let monthYear = date.toLocaleString('default', {
                            month: 'long',
                            year: 'numeric'
                        });
                        let value = date.toISOString().slice(0, 7);
                        $(this).append($('<option>', {
                            value: value,
                            text: monthYear
                        }));
                    }
                    $(this).val(new Date().toISOString().slice(0, 7));
                });
            }

            function updateChart(chartId, data) {
                if (charts[chartId]) {
                    charts[chartId].destroy();
                }

                var ctx = document.getElementById(chartId).getContext('2d');
                charts[chartId] = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: chartId === 'chartGuides' ? 'Guías' : 'Deudas',
                            data: data.values,
                            borderColor: chartId === 'chartGuides' ? 'rgb(75, 192, 192)' :
                                'rgb(255, 99, 132)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            function updateTable(tableId, data) {
                var table = $('#' + tableId);
                table.find('tbody').empty();
                $.each(data, function(user, value) {
                    table.find('tbody').append(
                        '<tr><td>' + user + '</td><td>' + formatCurrency(value) + '</td></tr>'
                    );
                });
            }

            function formatCurrency(amount) {
                return new Intl.NumberFormat('es-CO', {
                    style: 'currency',
                    currency: 'COP',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(amount);
            }

            $('.month-selector').change(function() {
                var target = $(this).data('target');
                var month = $(this).val();

                $.ajax({
                    url: '{{ route('dashboard.data') }}',
                    method: 'GET',
                    data: {
                        month: month,
                        target: target
                    },
                    success: function(response) {
                        if (target.startsWith('chart')) {
                            updateChart(target, response.data);
                        } else if (target.endsWith('Table')) {
                            updateTable(target, response.data);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al actualizar los datos:", error);
                    }
                });
            });

            initializeMonthSelectors();

            // Inicializar todos los gráficos y tablas con los datos del mes actual
            $('.month-selector').trigger('change');
        });
    </script>
@endpush
