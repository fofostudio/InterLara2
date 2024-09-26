@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'cash_closing'
])

@section('content')
<div class="content">
    <!-- Formulario de ingreso de Cierre de Caja -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Registro de Cierre de Caja</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cash_closings.store') }}" method="POST" id="cashClosingForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date">Fecha</label>
                                    <input readonly type="date" class="form-control" id="date" name="date" required value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="total_sales">Venta Total</label>
                                    <input type="text" class="form-control currency-input" id="total_sales" name="total_sales" required value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="expenses">Gastos</label>
                                    <input type="text" class="form-control currency-input" id="expenses" name="expenses" required value="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cash">Efectivo</label>
                                    <input type="text" class="form-control currency-input" id="cash" name="cash" required value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cancelled_guides">Guía Anular</label>
                                    <input type="text" class="form-control currency-input" id="cancelled_guides" name="cancelled_guides" required value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="debt">Deuda</label>
                                    <input type="text" class="form-control currency-input" id="debt" name="debt" required value="0">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Registrar Cierre de Caja</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Cierre de Caja -->
    <div class="row">
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
                                <p class="card-category">Total Ventas (Mes)</p>
                                <p class="card-title">{{ formatCurrency($totalSalesMonth) }}</p>
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
                                <p class="card-category">Total Gastos (Mes)</p>
                                <p class="card-title">{{ formatCurrency($totalExpensesMonth) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer ">
                    <hr>
                    <div class="stats">
                        <i class="fa fa-clock-o"></i> Último mes
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
                                <p class="card-category">Total Efectivo (Mes)</p>
                                <p class="card-title">{{ formatCurrency($totalCashMonth) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer ">
                    <hr>
                    <div class="stats">
                        <i class="fa fa-refresh"></i> Último mes
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
                                <i class="nc-icon nc-chart-bar-32 text-warning"></i>
                            </div>
                        </div>
                        <div class="col-7 col-md-8">
                            <div class="numbers">
                                <p class="card-category">Total Deudas (Mes)</p>
                                <p class="card-title">{{ formatCurrency($totalDebtMonth) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer ">
                    <hr>
                    <div class="stats">
                        <i class="fa fa-refresh"></i> Último mes
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de Cierres de Caja -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Historial de Cierres de Caja</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="text-primary">
                                <th>Fecha</th>
                                <th>Venta Total</th>
                                <th>Gastos</th>
                                <th>Efectivo</th>
                                <th>Guía Anular</th>
                                <th>Deuda</th>
                                <th>Balance</th>
                            </thead>
                            <tbody>
                                @foreach ($cashClosings as $closing)
                                    <tr>
                                        <td>{{ $closing->date->format('d/m/Y') }}</td>
                                        <td>{{ formatCurrency($closing->total_sales) }}</td>
                                        <td>{{ formatCurrency($closing->expenses) }}</td>
                                        <td>{{ formatCurrency($closing->cash) }}</td>
                                        <td>{{ formatCurrency($closing->cancelled_guides) }}</td>
                                        <td>{{ formatCurrency($closing->debt) }}</td>
                                        <td>{{ formatCurrency($closing->total_sales - $closing->expenses) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $cashClosings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.currency-input {
    text-align: right;
}
</style>
@endsection

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
