@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'dashboard'
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
                                    <i class="nc-icon nc-delivery-fast text-warning"></i>
                                </div>
                            </div>
                            <div class="col-7 col-md-8">
                                <div class="numbers">
                                    <p class="card-category">Total Guías</p>
                                    <p class="card-title">{{ $totalGuides }}
                                    <p>
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
                                    <p class="card-category">Total Deudas</p>
                                    <p class="card-title">{{ formatCurrency($totalDebts) }}
                                    <p>
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
                                    <p class="card-title">{{ $guidesToday }}
                                    <p>
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
                                    <i class="nc-icon nc-single-02 text-primary"></i>
                                </div>
                            </div>
                            <div class="col-7 col-md-8">
                                <div class="numbers">
                                    <p class="card-category">Operadores Activos</p>
                                    <p class="card-title">{{ $activeOperators }}
                                    <p>
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
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header ">
                        <h5 class="card-title">Rendimiento de Guías</h5>
                        <p class="card-category">Últimos 7 días</p>
                    </div>
                    <div class="card-body ">
                        <canvas id="guidePerformanceChart" width="400" height="100"></canvas>
                    </div>
                    <div class="card-footer ">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-history"></i> Actualizado hace 3 minutos
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card ">
                    <div class="card-header ">
                        <h5 class="card-title">Distribución de Deudas</h5>
                        <p class="card-category">Por Operador</p>
                    </div>
                    <div class="card-body ">
                        <canvas id="debtDistributionChart"></canvas>
                    </div>
                    <div class="card-footer ">
                        <div class="legend">
                            <i class="fa fa-circle text-primary"></i> Operador 1
                            <i class="fa fa-circle text-warning"></i> Operador 2
                            <i class="fa fa-circle text-danger"></i> Operador 3
                            <i class="fa fa-circle text-gray"></i> Otros
                        </div>
                        <hr>
                        <div class="stats">
                            <i class="fa fa-calendar"></i> Últimos 30 días
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card card-chart">
                    <div class="card-header">
                        <h5 class="card-title">Tendencia de Guías y Deudas</h5>
                        <p class="card-category">Últimos 6 meses</p>
                    </div>
                    <div class="card-body">
                        <canvas id="guidesDebtsTrendChart" width="400" height="100"></canvas>
                    </div>
                    <div class="card-footer">
                        <div class="chart-legend">
                            <i class="fa fa-circle text-info"></i> Guías
                            <i class="fa fa-circle text-warning"></i> Deudas
                        </div>
                        <hr />
                        <div class="card-stats">
                            <i class="fa fa-check"></i> Datos actualizados diariamente
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
            // Datos de ejemplo - reemplaza esto con datos reales de tu backend
            const guidePerformanceData = {
                labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
                datasets: [{
                    label: 'Guías Registradas',
                    data: [12, 19, 3, 5, 2, 3, 10],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            };

            const debtDistributionData = {
                labels: ['Operador 1', 'Operador 2', 'Operador 3', 'Otros'],
                datasets: [{
                    data: [300, 50, 100, 75],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                }]
            };

            const guidesDebtsTrendData = {
                labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
                datasets: [{
                    label: 'Guías',
                    data: [65, 59, 80, 81, 56, 55],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }, {
                    label: 'Deudas',
                    data: [28, 48, 40, 19, 86, 27],
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.1
                }]
            };

            new Chart(document.getElementById('guidePerformanceChart'), {
                type: 'line',
                data: guidePerformanceData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            new Chart(document.getElementById('debtDistributionChart'), {
                type: 'pie',
                data: debtDistributionData,
                options: {
                    responsive: true
                }
            });

            new Chart(document.getElementById('guidesDebtsTrendChart'), {
                type: 'line',
                data: guidesDebtsTrendData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endpush
