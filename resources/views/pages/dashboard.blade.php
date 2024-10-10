@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'dashboard',
])

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-5 col-md-4">
                                <div class="icon-big text-center icon-warning">
                                    <i class="nc-icon nc-globe text-warning"></i>
                                </div>
                            </div>
                            <div class="col-7 col-md-8">
                                <div class="numbers">
                                    <p class="card-category">Guías Registradas</p>
                                    <p class="card-title">{{ number_format($initialData['totalGuides']) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer ">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-refresh"></i> Actualizado ahora
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-5 col-md-4">
                                <div class="icon-big text-center icon-warning">
                                    <i class="nc-icon nc-money-coins text-success"></i>
                                </div>
                            </div>
                            <div class="col-7 col-md-8">
                                <div class="numbers">
                                    <p class="card-category">Total Movimientos</p>
                                    <p class="card-title">$ {{ number_format($initialData['totalDebts'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer ">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-calendar-o"></i> Último mes
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-5 col-md-4">
                                <div class="icon-big text-center icon-warning">
                                    <i class="nc-icon nc-vector text-danger"></i>
                                </div>
                            </div>
                            <div class="col-7 col-md-8">
                                <div class="numbers">
                                    <p class="card-category">Guías Hoy</p>
                                    <p class="card-title">{{ $initialData['guidesToday'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer ">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-clock-o"></i> En las últimas 24 horas
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-5 col-md-4">
                                <div class="icon-big text-center icon-warning">
                                    <i class="nc-icon nc-favourite-28 text-primary"></i>
                                </div>
                            </div>
                            <div class="col-7 col-md-8">
                                <div class="numbers">
                                    <p class="card-category">Operadores Activos</p>
                                    <p class="card-title">{{ $initialData['activeOperators'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer ">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-refresh"></i> Actualizado ahora
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card ">
                    <div class="card-header ">
                        <h5 class="card-title">Ventas Diarias</h5>
                        <p class="card-category">Desempeño de los últimos 30 días</p>
                    </div>
                    <div class="card-body ">
                        <canvas id="chartDailySales" width="400" height="200"></canvas>
                    </div>
                    <div class="card-footer ">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-history"></i> Actualizado hace 3 minutos
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card ">
                    <div class="card-header ">
                        <h5 class="card-title">Ventas vs Gastos</h5>
                        <p class="card-category">Proporción del mes actual</p>
                    </div>
                    <div class="card-body ">
                        <canvas id="chartSalesVsExpenses"></canvas>
                    </div>
                    <div class="card-footer ">
                        <div class="legend">
                            <i class="fa fa-circle text-primary"></i> Ventas
                            <i class="fa fa-circle text-danger"></i> Gastos
                        </div>
                        <hr>
                        <div class="stats">
                            <i class="fa fa-check"></i> Datos verificados
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card ">
                    <div class="card-header ">
                        <h5 class="card-title">Top 5 Movimientos por Usuario</h5>
                        <p class="card-category">Mes Actual</p>
                    </div>
                    <div class="card-body ">
                        <ul class="list-group" id="topDebts">
                            @foreach ($initialData['topDebts'] as $debt)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $debt['name'] }}
                                    <span class="badge bg-primary rounded-pill">$
                                        {{ number_format($debt['amount'], 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card ">
                    <div class="card-header ">
                        <h5 class="card-title">Top 5 Guías Registradas por Usuario</h5>
                        <p class="card-category">Mes Actual</p>
                    </div>
                    <div class="card-body ">
                        <ul class="list-group" id="topGuides">
                            @foreach ($initialData['topGuides'] as $guide)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $guide['name'] }}
                                    <span
                                        class="badge bg-success rounded-pill">{{ number_format($guide['guideCount'], 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
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
                        <h5 class="card-title">Movimientos Registrados por Día</h5>
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
                            <table class="table table-striped" id="excelTransportTable">
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
                            <table class="table table-striped" id="guideTransportTable">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Valor Transporte</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($initialData['guideTransportValueByUser'] as $user => $value)
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
                var config = {
                    type: chartId === 'chartSalesVsExpenses' ? 'pie' : 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: getChartLabel(chartId),
                            data: data.values,
                            backgroundColor: getChartColor(chartId),
                            borderColor: getChartColor(chartId),
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Fecha'
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: getYAxisLabel(chartId)
                                },
                                ticks: {
                                    callback: function(value, index, values) {
                                        return chartId.includes('Sales') ? '$ ' + value.toLocaleString(
                                            'es-CO') : value;
                                    }
                                }
                            }
                        }
                    }
                };

                if (chartId === 'chartSalesVsExpenses') {
                    config.options.scales = {}; // Remove scales for pie chart
                }

                charts[chartId] = new Chart(ctx, config);
            }

            function getChartLabel(chartId) {
                switch (chartId) {
                    case 'chartDailySales':
                        return 'Ventas Diarias';
                    case 'chartGuides':
                        return 'Guías Registradas';
                    case 'chartDebts':
                        return 'Movimientos';
                    default:
                        return '';
                }
            }

            function getChartColor(chartId) {
                switch (chartId) {
                    case 'chartDailySales':
                        return 'rgb(75, 192, 192)';
                    case 'chartGuides':
                        return 'rgb(255, 159, 64)';
                    case 'chartDebts':
                        return 'rgb(255, 99, 132)';
                    case 'chartSalesVsExpenses':
                        return ['rgb(75, 192, 192)', 'rgb(255, 99, 132)'];
                    default:
                        return 'rgb(75, 192, 192)';
                }
            }

            function getYAxisLabel(chartId) {
                switch (chartId) {
                    case 'chartDailySales':
                        return 'Ventas ($)';
                    case 'chartGuides':
                        return 'Número de Guías';
                    case 'chartDebts':
                        return 'Monto ($)';
                    default:
                        return '';
                }
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
                return '$ ' + amount.toLocaleString('es-CO', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
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

            // Inicializar todas las gráficas con los datos iniciales
            updateChart('chartDailySales', {
                labels: {!! json_encode($initialData['monthlyGuides']['labels']) !!},
                values: {!! json_encode($initialData['monthlyGuides']['values']) !!}
            });
            updateChart('chartGuides', {
                labels: {!! json_encode($initialData['monthlyGuides']['labels']) !!},
                values: {!! json_encode($initialData['monthlyGuides']['values']) !!}
            });
            updateChart('chartDebts', {
                labels: {!! json_encode($initialData['monthlyDebts']['labels']) !!},
                values: {!! json_encode($initialData['monthlyDebts']['values']) !!}
            });
            updateChart('chartSalesVsExpenses', {
                labels: ['Ventas', 'Gastos'],
                values: [
                    {!! $initialData['totalSalesMonth'] ?? 0 !!},
                    {!! $initialData['totalExpensesMonth'] ?? 0 !!}
                ]
            });

            // Inicializar tablas con los datos iniciales
            updateTable('excelTransportTable', {!! json_encode($initialData['excelTransportValueByUser']) !!});
            updateTable('guideTransportTable', {!! json_encode($initialData['guideTransportValueByUser']) !!});

            // Ajustar el tamaño de los gráficos cuando se redimensiona la ventana
            $(window).resize(function() {
                for (let chartId in charts) {
                    charts[chartId].resize();
                }
            });
        });
    </script>
@endpush
