@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'excel_upload'
])

@section('content')
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card" id="upload-card">
                <div class="card-header">
                    <h5 class="card-title">Subir Archivos Excel</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="month_select">Seleccionar Mes</label>
                        <select class="form-control" id="month_select" name="month_select" required>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_excel">Primer archivo Excel (Detallado)</label>
                                <input type="file" class="form-control-file" id="first_excel" name="first_excel" required>
                                <small class="form-text text-muted">
                                    Este archivo debe contener la información detallada de las guías.
                                </small>
                                <div id="first_excel_progress_container" class="mt-2" style="display: none;">
                                    <div class="progress">
                                        <div id="first_excel_progress" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small id="first_excel_status" class="form-text text-muted mt-1">Preparando carga...</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="second_excel">Segundo archivo Excel (ADM)</label>
                                <input type="file" class="form-control-file" id="second_excel" name="second_excel" required>
                                <small class="form-text text-muted">
                                    Este archivo debe contener la información de ADM_NumeroGuia y ADM_CreadoPor.
                                </small>
                                <div id="second_excel_progress_container" class="mt-2" style="display: none;">
                                    <div class="progress">
                                        <div id="second_excel_progress" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small id="second_excel_status" class="form-text text-muted mt-1">Preparando carga...</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-success btn-lg" id="process_data" disabled>Procesar Datos</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="results-card" class="card mt-4" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title">Resultados del Cruce de Datos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="results-table">
                            <thead class="text-primary">
                                <tr>
                                    <th>Número de Guía</th>
                                    <th>Ciudad Origen</th>
                                    <th>Ciudad Destino</th>
                                    <th>Fecha de Venta</th>
                                    <th>Valor Total</th>
                                    <th>Creado Por</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los resultados se cargarán aquí dinámicamente -->
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
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">

<script>
$(document).ready(function() {
    var firstFileUploaded = false;
    var secondFileUploaded = false;
    var resultsTable;

    function checkEnableProcessButton() {
        $('#process_data').prop('disabled', !(firstFileUploaded && secondFileUploaded));
    }

    function uploadFile(fileInput, progressBar, statusElement, uploadUrl) {
        var formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('month', $('#month_select').val());
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: uploadUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        progressBar.width(percentComplete * 100 + '%');
                        statusElement.text(`Cargando... ${Math.round(percentComplete * 100)}%`);
                    }
                }, false);
                return xhr;
            },
            beforeSend: function() {
                progressBar.width('0%');
                statusElement.text('Preparando carga...');
                progressBar.parent().parent().show();
            },
            success: function(response) {
                if (response.exists) {
                    Swal.fire({
                        title: 'Datos existentes',
                        text: 'Ya existen datos para este mes. ¿Desea sobrescribirlos?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, sobrescribir',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            formData.append('overwrite', true);
                            uploadFile(fileInput, progressBar, statusElement, uploadUrl);
                        } else {
                            progressBar.parent().parent().hide();
                            fileInput.value = ''; // Limpiar el input de archivo
                        }
                    });
                } else if (response.success) {
                    let summaryMessage = `
                        Archivo procesado:<br>
                        Total de filas: ${response.summary.total_rows}<br>
                        Filas importadas: ${response.summary.imported_rows}<br>
                        Filas con errores: ${response.summary.error_rows}
                    `;

                    if (response.error_csv_url) {
                        summaryMessage += `<br><a href="${response.error_csv_url}" target="_blank">Descargar CSV de errores</a>`;
                    }

                    Swal.fire({
                        title: 'Importación Completada',
                        html: summaryMessage,
                        icon: 'info'
                    });

                    statusElement.text('Carga completada');
                    if (fileInput.id === 'first_excel') {
                        firstFileUploaded = true;
                    } else {
                        secondFileUploaded = true;
                    }
                    checkEnableProcessButton();
                } else {
                    Swal.fire('Error', response.message, 'error');
                    statusElement.text('Error en la carga');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Hubo un problema al cargar el archivo: ' + xhr.responseText, 'error');
                statusElement.text('Error en la carga');
                progressBar.parent().parent().hide();
                fileInput.value = ''; // Limpiar el input de archivo
            }
        });
    }

    $('#first_excel').change(function() {
        if (this.files.length > 0) {
            uploadFile(this, $('#first_excel_progress'), $('#first_excel_status'), '{{ route("excel.upload.first") }}');
        }
    });

    $('#second_excel').change(function() {
        if (this.files.length > 0) {
            uploadFile(this, $('#second_excel_progress'), $('#second_excel_status'), '{{ route("excel.upload.second") }}');
        }
    });

    $('#process_data').click(function() {
        Swal.fire({
            title: 'Procesando datos',
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
            url: '{{ route("excel.process") }}',
            type: 'POST',
            data: {
                month: $('#month_select').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.close();
                $('#results-card').show();
                if (resultsTable) {
                    resultsTable.destroy();
                }
                var tableBody = $('#results-table tbody');
                tableBody.empty();
                response.crossedData.forEach(function(data) {
                    tableBody.append(`
                        <tr>
                            <td>${data.numero_guia}</td>
                            <td>${data.ciudad_origen}</td>
                            <td>${data.ciudad_destino}</td>
                            <td>${data.fecha_venta}</td>
                            <td>${parseFloat(data.valor_transporte).toLocaleString('es-CO', {style: 'currency', currency: 'COP'})}</td>
                            <td>${data.ADM_CreadoPor}</td>
                        </tr>
                    `);
                });
                resultsTable = $('#results-table').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                    }
                });
                Swal.fire('Éxito', 'Datos procesados correctamente', 'success');
            },
            error: function(xhr) {
                Swal.fire('Error', 'Hubo un problema al procesar los datos: ' + xhr.responseText, 'error');
            }
        });
    });
});
</script>
@endpush
