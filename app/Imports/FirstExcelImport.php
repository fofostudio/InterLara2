<?php

namespace App\Imports;

use App\Models\FirstExcelData;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FirstExcelImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    protected $month;
    protected $year;
    protected $rowCount = 0;
    protected $importedCount = 0;
    protected $errorRows = [];

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
        DB::statement('CREATE TEMPORARY TABLE IF NOT EXISTS temp_guias (numero_guia VARCHAR(255) PRIMARY KEY)');
        Log::info("FirstExcelImport iniciado para mes: $month, año: $year");
    }

    public function collection(Collection $rows)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        Log::info("Iniciando importación con " . $rows->count() . " filas");
        if ($rows->isEmpty()) {
            Log::warning("El archivo Excel está vacío");
            throw new \Exception("El archivo Excel está vacío");
        }
    
        $maxAttempts = 3;
        $attempt = 0;
    
        while ($attempt < $maxAttempts) {
            try {
                DB::beginTransaction();
                
                $chunkSize = 100;
                $chunks = $rows->chunk($chunkSize);
                $totalChunks = $chunks->count();
                
                Log::info("Procesando $totalChunks chunks de $chunkSize filas cada uno");
    
                foreach ($chunks as $chunkIndex => $chunk) {
                    Log::info("Procesando chunk " . ($chunkIndex + 1) . " de $totalChunks");
                    $dataToInsert = [];
                    foreach ($chunk as $index => $row) {
                        $this->rowCount++;
                        try {
                            Log::debug("Procesando fila $this->rowCount");
                            $processedRow = $this->processRow($row, $this->rowCount);
                            if ($processedRow) {
                                $dataToInsert[] = $processedRow;
                                $this->importedCount++;
                            }
                        } catch (\Exception $e) {
                            Log::error("Error en fila $this->rowCount: " . $e->getMessage());
                            $this->addErrorRow($row, $this->rowCount, $e->getMessage());
                            continue;
                        }
                    }
    
                    if (!empty($dataToInsert)) {
                        Log::info("Insertando lote de " . count($dataToInsert) . " filas");
                        FirstExcelData::insert($dataToInsert);
                    }
                }
    
                $this->generateErrorCSV();
                $this->logImportSummary();
    
                DB::commit();
                Log::info("Importación completada exitosamente");
                break; // Salir del bucle si la importación fue exitosa
            } catch (\Illuminate\Database\QueryException $e) {
                $attempt++;
                Log::error("Intento $attempt de $maxAttempts falló. Error de base de datos: " . $e->getMessage());
                
                if ($attempt >= $maxAttempts) {
                    Log::error("Se alcanzó el número máximo de intentos. Abortando importación.");
                    throw $e;
                }
                
                Log::info("Esperando antes de reintentar...");
                sleep(5); // Espera 5 segundos antes de reintentar
                
                // Intentar reconectar a la base de datos
                DB::reconnect();
                Log::info("Reconexión a la base de datos realizada. Reintentando importación.");
            } catch (\Exception $e) {
                Log::error('Error inesperado en la importación: ' . $e->getMessage());
                Log::error('Trace: ' . $e->getTraceAsString());
                throw $e; // Lanzar la excepción para errores no relacionados con la base de datos
            }
        }
    }

    protected function processRow($row, $rowNumber)
    {
        Log::debug("Procesando fila $rowNumber");
        $validatedData = $this->validateRow($row);
        if ($validatedData->fails()) {
            $errors = implode(', ', $validatedData->errors()->all());
            Log::warning("Validación fallida en fila $rowNumber: $errors");
            throw new \Exception($errors);
        }

        $numeroGuia = $this->getValueFromRow($row, ['numero_guia', 'numero_de_guia', 'numeroguia']);
        $isDuplicate = DB::table('temp_guias')->where('numero_guia', $numeroGuia)->exists();
        if ($isDuplicate) {
            Log::warning("Número de guía duplicado en fila $rowNumber: $numeroGuia");
            throw new \Exception("Número de guía duplicado: $numeroGuia");
        }
        DB::table('temp_guias')->insert(['numero_guia' => $numeroGuia]);

        $fechaVenta = $this->parseFecha($this->getValueFromRow($row, ['fecha_venta', 'fecha_de_venta', 'fechaventa']));
        if ($fechaVenta && ($fechaVenta->month != $this->month || $fechaVenta->year != $this->year)) {
            Log::warning("Fecha de venta incorrecta en fila $rowNumber: " . $fechaVenta->toDateString());
            throw new \Exception("La fecha de venta no corresponde al mes y año seleccionados");
        }

        Log::debug("Fila $rowNumber procesada exitosamente");
        return [
            'numero_guia' => $numeroGuia,
            'regional_origen' => $this->getValueFromRow($row, ['regional_origen', 'regionalorigen']),
            'codigo_ciudad_origen' => $this->getValueFromRow($row, ['codigo_ciudad_origen', 'codigociudadorigen']),
            'ciudad_origen' => $this->getValueFromRow($row, ['ciudad_origen', 'ciudadorigen']),
            'regional_destino' => $this->getValueFromRow($row, ['regional_destino', 'regionaldestino']),
            'codigo_ciudad_destino' => $this->getValueFromRow($row, ['codigo_ciudad_destino', 'codigociudaddestino']),
            'ciudad_destino' => $this->getValueFromRow($row, ['ciudad_destino', 'ciudaddestino']),
            'fecha_venta' => $fechaVenta,
            'fecha_edicion' => $this->parseFecha($this->getValueFromRow($row, ['fecha_edicion', 'fechaedicion'])),
            'tipo_cliente' => $this->getValueFromRow($row, ['tipo_cliente', 'tipocliente']),
            'desc_tipo_entrega' => $this->getValueFromRow($row, ['desc_tipo_entrega', 'desctipoentrega']),
            'unidad_negocio' => $this->getValueFromRow($row, ['unidad_negocio', 'unidadnegocio']),
            'id_centro_costos_origen' => $this->getValueFromRow($row, ['id_centro_costos_origen', 'idcentrocostosorigen']),
            'id_centro_servicios_origen' => $this->getValueFromRow($row, ['id_centro_servicios_origen', 'idcentroserviciosorigen']),
            'nombre_centro_servicio_origen' => $this->getValueFromRow($row, ['nombre_centro_servicio_origen', 'nombrecentroservicioorigen']),
            'id_centro_costos_destino' => $this->getValueFromRow($row, ['id_centro_costos_destino', 'idcentrocostosdestino']),
            'id_centro_servicios_destino' => $this->getValueFromRow($row, ['id_centro_servicios_destino', 'idcentroserviciosdestino']),
            'nombre_centro_servicio_destino' => $this->getValueFromRow($row, ['nombre_centro_servicio_destino', 'nombrecentroserviciodestino']),
            'numero_identificacion_origen' => $this->getValueFromRow($row, ['numero_identificacion_origen', 'numeroidentificacionorigen']),
            'id_convenio' => $this->getValueFromRow($row, ['id_convenio', 'idconvenio']),
            'nit_convenio' => $this->getValueFromRow($row, ['nit_convenio', 'nitconvenio']),
            'id_sucursal' => $this->getValueFromRow($row, ['id_sucursal', 'idsucursal']),
            'id_contrato' => $this->getValueFromRow($row, ['id_contrato', 'idcontrato']),
            'razon_social' => $this->getValueFromRow($row, ['razon_social', 'razonsocial']),
            'nombre_sucursal' => $this->getValueFromRow($row, ['nombre_sucursal', 'nombresucursal']),
            'nombre_contrato' => $this->getValueFromRow($row, ['nombre_contrato', 'nombrecontrato']),
            'piezas' => $this->getValueFromRow($row, ['piezas']),
            'peso' => $this->getValueFromRow($row, ['peso']),
            'valor_comercial' => $this->getValueFromRow($row, ['valor_comercial', 'valorcomercial']),
            'valor_transporte' => $this->getValueFromRow($row, ['valor_transporte', 'valortransporte']),
            'valor_seguro' => $this->getValueFromRow($row, ['valor_seguro', 'valorseguro']),
            'valor_adicionales' => $this->getValueFromRow($row, ['valor_adicionales', 'valoradicionales']),
            'valor_descuento' => $this->getValueFromRow($row, ['valor_descuento', 'valordescuento']),
            'valor_contrapago' => $this->getValueFromRow($row, ['valor_contrapago', 'valorcontrapago']),
            'valor_total' => $this->getValueFromRow($row, ['valor_total', 'valortotal']),
            'tipo_servicio' => $this->getValueFromRow($row, ['tipo_servicio', 'tiposervicio']),
            'tipo_envio' => $this->getValueFromRow($row, ['tipo_envio', 'tipoenvio']),
            'tipo_identificacion_remitente' => $this->getValueFromRow($row, ['tipo_identificacion_remitente', 'tipoidentificacionremitente']),
            'identificacion_remitente' => $this->getValueFromRow($row, ['identificacion_remitente', 'identificacionremitente']),
            'nombre_remitente' => $this->getValueFromRow($row, ['nombre_remitente', 'nombreremitente']),
            'telefono_remitente_origen' => $this->getValueFromRow($row, ['telefono_remitente_origen', 'telefonoremitenteorigen']),
            'direccion_remitente' => $this->getValueFromRow($row, ['direccion_remitente', 'direccionremitente']),
            'tipo_identificacion_destinatario' => $this->getValueFromRow($row, ['tipo_identificacion_destinatario', 'tipoidentificaciondestinatario']),
            'identificacion_destinatario' => $this->getValueFromRow($row, ['identificacion_destinatario', 'identificaciondestinatario']),
            'nombre_destinatario' => $this->getValueFromRow($row, ['nombre_destinatario', 'nombredestinatario']),
            'telefono_destinatario' => $this->getValueFromRow($row, ['telefono_destinatario', 'telefonodestinatario']),
            'direccion_destinatario' => $this->getValueFromRow($row, ['direccion_destinatario', 'direcciondestinatario']),
            'contenido' => $this->getValueFromRow($row, ['contenido']),
            'forma_pago' => $this->getValueFromRow($row, ['forma_pago', 'formapago']),
            'es_alcobro' => $this->getValueFromRow($row, ['es_alcobro', 'esalcobro']),
            'estado_envio' => $this->getValueFromRow($row, ['estado_envio', 'estadoenvio']),
            'tiempo_gestion' => $this->getValueFromRow($row, ['tiempo_gestion', 'tiempogestion']),
            'fecha_estimada_entrega' => $this->parseFecha($this->getValueFromRow($row, ['fecha_estimada_entrega', 'fechaestimadaentrega'])),
            'ultimo_estado' => $this->getValueFromRow($row, ['ultimo_estado', 'ultimoestado']),
            'fecha_ultimo_estado' => $this->parseFecha($this->getValueFromRow($row, ['fecha_ultimo_estado', 'fechaultimoestado'])),
            'nom_ciudad_ultimo_estado' => $this->getValueFromRow($row, ['nom_ciudad_ultimo_estado', 'nomciudadultimoestado']),
            'nom_racol_ultimo_estado' => $this->getValueFromRow($row, ['nom_racol_ultimo_estado', 'nomracolultimoestado']),
            'largo' => $this->getValueFromRow($row, ['largo']),
            'ancho' => $this->getValueFromRow($row, ['ancho']),
            'alto' => $this->getValueFromRow($row, ['alto']),
            'contrapago' => $this->getValueFromRow($row, ['contrapago']),
            'planilla_asignacion_envio' => $this->getValueFromRow($row, ['planilla_asignacion_envio', 'planillaasignacionenvio']),
            'fecha_asignacion' => $this->parseFecha($this->getValueFromRow($row, ['fecha_asignacion', 'fechaasignacion'])),
            'cedula_mensajero' => $this->getValueFromRow($row, ['cedula_mensajero', 'cedulamensajero']),
            'nombre_mensajero' => $this->getValueFromRow($row, ['nombre_mensajero', 'nombremensajero']),
            'fecha_descargue' => $this->parseFecha($this->getValueFromRow($row, ['fecha_descargue', 'fechadescargue'])),
            'fecha_digitalizacion' => $this->parseFecha($this->getValueFromRow($row, ['fecha_digitalizacion', 'fechadigitalizacion'])),
            'digitalizacion_prueba' => $this->getValueFromRow($row, ['digitalizacion_prueba', 'digitalizacionprueba']),
            'fecha_recibido' => $this->parseFecha($this->getValueFromRow($row, ['fecha_recibido', 'fecharecibido'])),
            'fecha_archivo' => $this->parseFecha($this->getValueFromRow($row, ['fecha_archivo', 'fechaarchivo'])),
            'archivo_prueba' => $this->getValueFromRow($row, ['archivo_prueba', 'archivoprueba']),
        ];
    }

    protected function validateRow($row)
    {
        return Validator::make($row->toArray(), [
            // Añade aquí más reglas de validación según tus necesidades
        ]);
    }

    protected function getValueFromRow($row, $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            if (isset($row[$key]) && $row[$key] !== '') {
                return $row[$key];
            }
        }
        return null;
    }
    protected function parseFecha($valor)
    {
        if (!$valor) return null;

        try {
            return Carbon::parse($valor);
        } catch (\Exception $e) {
            Log::warning("No se pudo parsear la fecha: $valor", ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function addErrorRow($row, $rowNumber, $reason)
    {
        $errorRow = $row->toArray();
        $errorRow['row_number'] = $rowNumber;
        $errorRow['error_reason'] = $reason;
        $this->errorRows[] = $errorRow;
        Log::warning("Fila $rowNumber añadida a errores: $reason");
    }

    protected function generateErrorCSV()
    {
        if (empty($this->errorRows)) {
            Log::info("No se encontraron errores durante la importación");
            return;
        }

        $csvFileName = 'error_rows_' . now()->format('Y-m-d_His') . '.csv';
        $csvContent = implode(',', array_keys(reset($this->errorRows))) . "\n";

        foreach ($this->errorRows as $row) {
            $csvContent .= implode(',', array_map(function($value) {
                return '"' . str_replace('"', '""', $value) . '"';
            }, $row)) . "\n";
        }

        Storage::put('error_csvs/' . $csvFileName, $csvContent);
        Log::info("Archivo de errores generado: $csvFileName");
    }

    protected function logImportSummary()
    {
        $summary = [
            'Total de filas en el archivo' => $this->rowCount,
            'Filas importadas exitosamente' => $this->importedCount,
            'Filas con errores' => count($this->errorRows),
        ];

        Log::info('Resumen de importación:', $summary);
    }

    public function getImportSummary()
    {
        return [
            'total_rows' => $this->rowCount,
            'imported_rows' => $this->importedCount,
            'error_rows' => count($this->errorRows),
        ];
    }

    public function chunkSize(): int
    {
        return 500; // Ajusta según sea necesario
    }
    
    public function batchSize(): int
    {
        return 100; // Ajusta según sea necesario
    }
}