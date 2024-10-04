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
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw new \Exception("El archivo Excel está vacío");
        }

        DB::beginTransaction();
        try {
            $dataToInsert = [];
            foreach ($rows as $index => $row)
            {
                $this->rowCount++;
                try {
                    $processedRow = $this->processRow($row, $index);
                    if ($processedRow) {
                        $dataToInsert[] = $processedRow;
                    }
                } catch (\Exception $e) {
                    $this->addErrorRow($row, $index, $e->getMessage());
                    continue;
                }
            }

            if (!empty($dataToInsert)) {
                FirstExcelData::insert($dataToInsert);
            }

            $this->generateErrorCSV();
            $this->logImportSummary();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error durante la importación: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function processRow($row, $index)
    {
        $validatedData = $this->validateRow($row);
        if ($validatedData->fails()) {
            throw new \Exception(implode(', ', $validatedData->errors()->all()));
        }

        $numeroGuia = $this->getValueFromRow($row, ['numero_guia', 'numero_de_guia', 'numeroguia']);
        $isDuplicate = DB::table('temp_guias')->where('numero_guia', $numeroGuia)->exists();
        if ($isDuplicate) {
            throw new \Exception("Número de guía duplicado");
        }
        DB::table('temp_guias')->insert(['numero_guia' => $numeroGuia]);

        $fechaVenta = $this->parseFecha($this->getValueFromRow($row, ['fecha_venta', 'fecha_de_venta', 'fechaventa']));
        if ($fechaVenta->month != $this->month || $fechaVenta->year != $this->year) {
            throw new \Exception("La fecha de venta no corresponde al mes y año seleccionados");
        }

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
            Log::warning("No se pudo parsear la fecha: $valor");
            return null;
        }
    }

    protected function addErrorRow($row, $index, $reason)
    {
        $errorRow = $row->toArray();
        $errorRow['error_reason'] = $reason;
        $this->errorRows[$index] = $errorRow;
    }

    protected function generateErrorCSV()
    {
        if (empty($this->errorRows)) {
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
        return 1000; // Ajusta este número según sea necesario
    }

    public function batchSize(): int
    {
        return 1000; //
    }
}
