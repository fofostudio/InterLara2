@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'debts',
])

@section('content')
<div class="content">
    <!-- Indicadores estadísticos -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5 col-md-4">
                            <div class="icon-big text-center icon-warning">
                                <i class="nc-icon nc-money-coins text-warning"></i>
                            </div>
                        </div>
                        <div class="col-7 col-md-8">
                            <div class="numbers">
                                <p class="card-category">Total Deudas</p>
                                <p class="card-title" id="totalDebts">{{ formatCurrency($totalDebts) }}</p>
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
                                <i class="nc-icon nc-vector text-danger"></i>
                            </div>
                        </div>
                        <div class="col-7 col-md-8">
                            <div class="numbers">
                                <p class="card-category">Deudas Hoy</p>
                                <p class="card-title" id="debtsToday">{{ formatCurrency($debtsToday) }}</p>
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
                                <p class="card-category">Promedio Diario</p>
                                <p class="card-title" id="averageDailyDebts">{{ formatCurrency($averageDailyDebts) }}</p>
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
                                <i class="nc-icon nc-chart-bar-32 text-success"></i>
                            </div>
                        </div>
                        <div class="col-7 col-md-8">
                            <div class="numbers">
                                <p class="card-category">Gastos Hoy</p>
                                <p class="card-title" id="expensesToday">{{ formatCurrency($expensesToday) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <hr>
                    <div class="stats">
                        <i class="fa fa-calendar"></i> Hoy
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de ingreso de movimientos -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="nc-icon nc-simple-add"></i> Ingreso de Movimientos</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('debts.store') }}" method="POST" id="debtForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="user_id">Operador</label>
                                    <div class="input-group">
                                        <select class="form-control" style="margin: 10px"  id="user_id" name="user_id" >
                                            <option value="">Seleccione un operador</option>
                                            @foreach($operators as $operator)
                                                <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append" style="margin: 10px" >
                                            <button class="btn btn-outline-secondary" type="button" id="gastoBtn">Gasto</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="amount">Monto</label>
                                    <input type="text" class="form-control currency-input" id="amount" name="amount" required value="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="description">Descripción</label>
                                    <input type="text" class="form-control" id="description" name="description">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Registrar</button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="is_expense" id="is_expense" value="0">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de movimientos registrados -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="nc-icon nc-bullet-list-67"></i> Movimientos Registrados</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="movementsTable" class="table">
                            <thead class="text-primary">
                                <tr>
                                    <th>Operador/Gasto</th>
                                    <th>Monto</th>
                                    <th>Descripción</th>
                                    <th>Fecha de Registro</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestDebts as $debt)
                                <tr>
                                    <td>{{ $debt->is_expense ? 'Gasto' : $debt->user->name }}</td>
                                    <td>{{ formatCurrency($debt->amount) }}</td>
                                    <td>{{ $debt->observation }}</td>
                                    <td>{{ $debt->created_at->format('d/m/Y h:i A') }}</td>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
<script>
$(document).ready(function() {
    let isExpense = false;
    let submitInProgress = false;

    function formatCurrency(number) {
        if (number >= 1e9) {
            return '$ ' + (number / 1e9).toFixed(1) + ' B';
        }
        if (number >= 1e6) {
            return '$ ' + (number / 1e6).toFixed(1) + ' M';
        }
        return '$ ' + Math.round(number).toLocaleString('es-CO');
    }

    $('#gastoBtn').on('click', function() {
        isExpense = !isExpense;
        updateFormState();
    });

    function updateFormState() {
        if (isExpense) {
            $('#user_id').prop('disabled', true).val('');
            $('#gastoBtn').addClass('btn-warning').removeClass('btn-outline-secondary').text('Deuda');
            $('#is_expense').val(1);
        } else {
            $('#user_id').prop('disabled', false);
            $('#gastoBtn').removeClass('btn-warning').addClass('btn-outline-secondary').text('Gasto');
            $('#is_expense').val(0);
        }
    }

    $('#debtForm').on('submit', function(e) {
        e.preventDefault();
        if (submitInProgress) return;
        submitInProgress = true;
        
        if (isExpense) {
            $('#user_id').prop('disabled', false);
        }
        submitDebt();
    });

    function submitDebt() {
        $.ajax({
            url: "{{ route('debts.store') }}",
            method: 'POST',
            data: $('#debtForm').serialize(),
            success: function(response) {
                if (response.success) {
                    $('#debtForm')[0].reset();
                    isExpense = false;
                    updateFormState();
                    updateStats(response.stats);
                    addDebtToTable(response.debt);
                    showAlert(response.message, 'success');
                } else {
                    showAlert(response.message, 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error al registrar la deuda/gasto';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(errorMessage, 'error');
            },
            complete: function() {
                submitInProgress = false;
            }
        });
    }

    function updateStats(stats) {
        $('#totalDebts').text(formatCurrency(stats.totalDebts));
        $('#debtsToday').text(formatCurrency(stats.debtsToday));
        $('#averageDailyDebts').text(formatCurrency(stats.averageDailyDebts));
        $('#expensesToday').text(formatCurrency(stats.expensesToday));
    }

    function addDebtToTable(debt) {
        let newRow = `
            <tr>
                <td>${debt.is_expense ? 'Gasto' : debt.user.name}</td>
                <td>${formatCurrency(debt.amount)}</td>
                <td>${debt.observation}</td>
                <td>${formatDate(debt.created_at)}</td>
            </tr>
        `;
        $('#movementsTable tbody').prepend(newRow);
    }

    function formatDate(dateString) {
        let date = new Date(dateString);
        return date.toLocaleString('es-CO', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        }).replace(',', '');
    }

    function showAlert(message, type) {
        Swal.fire({
            title: type === 'success' ? '¡Éxito!' : 'Error',
            text: message,
            icon: type,
            confirmButtonText: 'OK'
        });
    }

    const currencyInputs = document.querySelectorAll('.currency-input');

    currencyInputs.forEach(input => {
        new Cleave(input, {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            prefix: '$ ',
            rawValueTrimPrefix: true
        });

        input.addEventListener('focus', function() {
            if (this.value === '$ 0') {
                this.value = '';
            }
        });

        input.addEventListener('blur', function() {
            if (this.value === '' || this.value === '$ ') {
                this.value = '$ 0';
            }
        });
    });

    // Inicializar DataTable
    $('#movementsTable').DataTable({
        "pageLength": 30,
        "lengthMenu": [[30, 50, 100, -1], [30, 50, 100, "Todos"]],
        "order": [[3, "desc"]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        }
    });
});
</script>
@endpush