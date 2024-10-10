@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'paymentsMang',
])

@section('content')
<div class="content">
    <div class="container">
        <h2 class="mb-4">Gestión de Pagos Pendientes</h2>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pagos Hoy</h5>
                        <p class="card-text display-4">{{ number_format($paymentsToday, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total en Pagos Hoy</h5>
                        <p class="card-text display-4">$ {{ number_format($totalPaymentsToday, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Deudas Pendientes
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingDebts as $debt)
                                <tr>
                                    <td>{{ $debt->user->name }}</td>
                                    <td>$ {{ number_format($debt->amount, 0, ',', '.') }}</td>
                                    <td>{{ $debt->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $debt->observation }}</td>
                                    <td>
                                        <button class="btn btn-success btn-sm mark-as-paid" data-debt-id="{{ $debt->id }}">
                                            Marcar como Pagado
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No hay deudas pendientes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if ($pendingDebts->hasPages())
            <div class="mt-4">
                {{ $pendingDebts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Mostrar alertas de sesión con SweetAlert
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: "{{ session('success') }}",
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') }}",
        });
    @endif

    // Manejar el clic en el botón "Marcar como Pagado"
    document.querySelectorAll('.mark-as-paid').forEach(button => {
        button.addEventListener('click', function() {
            const debtId = this.getAttribute('data-debt-id');
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Quieres marcar esta deuda como pagada?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, marcar como pagado',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar solicitud AJAX para marcar como pagado
                    fetch(`{{ url('debts') }}/${debtId}/mark-as-paid`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                '¡Pagado!',
                                'La deuda ha sido marcada como pagada.',
                                'success'
                            ).then(() => {
                                // Recargar la página o actualizar la tabla
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error',
                                'Hubo un problema al marcar la deuda como pagada.',
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire(
                            'Error',
                            'Hubo un problema al procesar la solicitud.',
                            'error'
                        );
                    });
                }
            });
        });
    });
});
</script>
@endpush