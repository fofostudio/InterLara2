@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'debts',
])

@section('content')
<div class="content">
    <!-- Indicadores estadísticos de deudas -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-body ">
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
                                <i class="nc-icon nc-chart-bar-32 text-success"></i>
                            </div>
                        </div>
                        <div class="col-7 col-md-8">
                            <div class="numbers">
                                <p class="card-category">Total Mes Actual</p>
                                <p class="card-title" id="totalDebtsCurrentMonth">{{ formatCurrency($totalDebtsCurrentMonth) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer ">
                    <hr>
                    <div class="stats">
                        <i class="fa fa-calendar"></i> Este mes
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de ingreso de deudas -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Ingreso de Deudas</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('debts.store') }}" method="POST" id="debtForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="user_id">Operador</label>
                                    <select class="form-control" id="user_id" name="user_id" required>
                                        <option value="">Seleccione un operador</option>
                                        @foreach($operators as $operator)
                                            <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="amount">Monto</label>
                                    <input type="text" class="form-control currency-input"  id="amount" name="amount"  required value="0">

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="description">Descripción</label>
                                    <input type="text" class="form-control" id="description" name="description">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Registrar Deuda</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de últimas deudas registradas -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Últimas Deudas Registradas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-primary">
                                <th>Operador</th>
                                <th>Monto</th>
                                <th>Descripción</th>
                                <th>Fecha de Registro</th>
                            </thead>
                            <tbody>
                                @foreach ($latestDebts as $debt)
                                    <tr>
                                        <td>{{ $debt->user->name }}</td>
                                        <td>{{ formatCurrency($debt->amount) }}</td>
                                        <td>{{ $debt->description }}</td>
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
<script>
function formatCurrency(number) {
    if (number >= 1e9) {
        return '$ ' + (number / 1e9).toFixed(1) + ' B';
    }
    if (number >= 1e6) {
        return '$ ' + (number / 1e6).toFixed(1) + ' M';
    }

    return '$ ' + Math.round(number).toLocaleString('es-CO');
}

$(document).ready(function() {
    $('#debtForm').on('submit', function(e) {
        e.preventDefault();
        submitDebt();
    });

    function submitDebt() {
        $.ajax({
            url: "{{ route('debts.store') }}",
            method: 'POST',
            data: $('#debtForm').serialize(),
            success: function(response) {
                if (response.success) {
                    // Clear the form
                    $('#debtForm')[0].reset();

                    // Add the new debt to the table
                    addDebtToTable(response.debt);

                    // Update statistics
                    updateStats(response.stats);

                    // Show a success message
                    showAlert('Deuda registrada exitosamente', 'success');
                } else {
                    showAlert(response.message, 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error al registrar la deuda';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(errorMessage, 'error');
            }
        });
    }

    function addDebtToTable(debt) {
        let newRow = `
            <tr>
                <td>${debt.user.name}</td>
                <td>${formatCurrency(debt.amount)}</td>
                <td>${debt.description}</td>
                <td>${formatDate(debt.created_at)}</td>
            </tr>
        `;
        $('table tbody').prepend(newRow);
    }

    function updateStats(stats) {
        $('#totalDebts').text(formatCurrency(stats.totalDebts));
        $('#debtsToday').text(formatCurrency(stats.debtsToday));
        $('#averageDailyDebts').text(formatCurrency(stats.averageDailyDebts));
        $('#totalDebtsCurrentMonth').text(formatCurrency(stats.totalDebtsCurrentMonth));
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const currencyInputs = document.querySelectorAll('.currency-input');

    currencyInputs.forEach(input => {
        // Inicializar Cleave.js en cada input
        new Cleave(input, {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            prefix: '$ ',
            rawValueTrimPrefix: true
        });

        // Manejar el valor por defecto y el enfoque
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

    // Modificar el envío del formulario para eliminar el formateo antes de enviar
    document.getElementById('cashClosingForm').addEventListener('submit', function(e) {
        e.preventDefault();

        currencyInputs.forEach(input => {
            input.value = input.value.replace(/[^\d.-]/g, '');
        });

        submitFormWithAjax(this);
    });
});

function submitFormWithAjax(form) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Quieres registrar este cierre de caja?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, registrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando',
                text: 'Por favor, espere...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: form.action,
                method: form.method,
                data: $(form).serialize(),
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    console.error('Error en la solicitud AJAX:', status, error);
                    let errorMessage = 'Error al procesar la solicitud';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}
</script>
@endpush
