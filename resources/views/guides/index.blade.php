@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'guides',
])

@section('content')
    <div class="content">
        <!-- Indicadores estadísticos -->
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
                                    <p class="card-category">Total Guías</p>
                                    <p class="card-title" id="totalGuides">{{ $totalGuides }}</p>
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
                                    <p class="card-category">Guías Hoy</p>
                                    <p class="card-title" id="guidesToday">{{ $guidesToday }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer ">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-calendar-o"></i> Últimas 24 horas
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
                                    <p class="card-category">Operadores Activos</p>
                                    <p class="card-title" id="activeOperators">{{ $activeOperators }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer ">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-clock-o"></i> En el último mes
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
                                    <p class="card-category">Promedio Diario</p>
                                    <p class="card-title" id="averageDailyGuides">{{ $averageDailyGuides }}</p>
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

        <!-- Tarjetas de operadores seleccionables -->
        <div class="row mb-4">
            @foreach ($operatorStats as $stat)
                <div class="col-md-2 col-sm-6 mb-3">
                    <div class="card operator-card h-100" data-operator-id="{{ $stat->id }}">
                        <div class="card-body p-3">
                            <h5 class="card-title mb-2">{{ $stat->name }}</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-center">
                                    <h4 class="mb-0 total-guides">{{ $stat->total_guides }}</h4>
                                    <small class="text-muted">Total</small>
                                </div>
                                <div class="text-center">
                                    <h4 class="mb-0 guides-today">{{ $stat->guides_today }}</h4>
                                    <small class="text-muted">Hoy</small>
                                </div>
                                <div class="text-center">
                                    <h4 class="mb-0 average-daily">{{ $stat->average_daily }}</h4>
                                    <small class="text-muted">Prom.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Formulario de ingreso de guías -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Ingreso de Guías</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('guides.store') }}" method="POST" id="guideForm">
                            @csrf
                            <input type="hidden" id="user_id" name="user_id">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="guide_number">Número de Guía</label>
                                        <input type="text" class="form-control" id="guide_number" name="guide_number"
                                            required
                                            placeholder="Seleccione un operador y luego escanee o ingrese el número de guía"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block" disabled>Registrar
                                            Guía</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado de guías -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Listado de Guías</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="text-primary">
                                    <th>Número de Guía</th>
                                    <th>Operador</th>
                                    <th>Fecha de Registro</th>
                                    <th>Estado</th>
                                </thead>
                                <tbody>
                                    @foreach ($guides as $guide)
                                        <tr>
                                            <td>{{ $guide->guide_number }}</td>
                                            <td>{{ $guide->user->name }}</td>
                                            <td>{{ $guide->created_at->format('d/m/Y h:i A') }}</td>
                                            <td>{{ $guide->status }}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $guides->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .operator-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .operator-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .operator-card.selected {
            border-color: #007bff;
            background-color: #e8f0fe;
        }

        .operator-card .card-body {
            padding: 0.75rem;
        }

        .operator-card .card-title {
            font-size: 1rem;
            font-weight: bold;
        }

        .operator-card h4 {
            font-size: 1.2rem;
        }

        .operator-card small {
            font-size: 0.7rem;
        }
    </style>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let selectedOperatorId = null;

            $('.operator-card').on('click', function() {
                $('.operator-card').removeClass('selected');
                $(this).addClass('selected');
                selectedOperatorId = $(this).data('operator-id');
                $('#user_id').val(selectedOperatorId);
                $('#guide_number').prop('disabled', false).focus();
                $('button[type="submit"]').prop('disabled', false);
            });

            $('#guide_number').on('keypress', function(e) {
                if (e.which == 13 || e.which == 42) { // 13 is enter, 42 is *
                    e.preventDefault();
                    if (selectedOperatorId) {
                        submitGuide($(this).val());
                    } else {
                        alert('Por favor, seleccione un operador antes de ingresar la guía.');
                    }
                }
            });

            function submitGuide(guideNumber) {
                $.ajax({
                    url: "{{ route('guides.store') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: selectedOperatorId,
                        guide_number: guideNumber
                    },
                    success: function(response) {
                        if (response.success) {
                            // Clear the input and keep focus
                            $('#guide_number').val('').focus();

                            // Add the new guide to the table
                            addGuideToTable(response.guide);

                            // Update operator statistics
                            updateOperatorStats(response.stats);

                            // Show a success message
                            showAlert('Guía registrada exitosamente', 'success');
                        } else {
                            showAlert(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        showAlert('Error al registrar la guía: ' + xhr.responseJSON.message, 'error');
                    }
                });
            }

            function addGuideToTable(guide) {
                let date = new Date(guide.created_at);
                let formattedDate = date.toLocaleString('en-US', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                }).replace(',', '');

                let newRow = `
        <tr>
            <td>${guide.guide_number}</td>
            <td>${guide.user.name}</td>
            <td>${formattedDate}</td>
            <td>${guide.status}</td>
        </tr>
    `;
                $('table tbody').prepend(newRow);
            }

            function updateOperatorStats(stats) {
                let operatorCard = $(`.operator-card[data-operator-id="${selectedOperatorId}"]`);
                operatorCard.find('.total-guides').text(stats.operatorTotalGuides);
                operatorCard.find('.guides-today').text(stats.operatorGuidesToday);
                operatorCard.find('.average-daily').text(stats.operatorAverageDaily);

                // Update global statistics
                $('#totalGuides').text(stats.totalGuides);
                $('#guidesToday').text(stats.guidesToday);
                $('#averageDailyGuides').text(stats.averageDailyGuides);
            }

            function showAlert(message, type) {
                let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                let alert = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
                $('.content').prepend(alert);
                setTimeout(() => {
                    $('.alert').alert('close');
                }, 5000);
            }
        });
    </script>
@endpush
