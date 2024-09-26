<?php

namespace App\Imports;

use App\Models\SecondExcelData;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SecondExcelImport implements ToCollection, WithHeadingRow
{
    protected $month;
    protected $year;
    protected $rowCount = 0;
    protected $importedCount = 0;
    protected $errorRows = [];
    protected $uniqueGuides = [];

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw new \Exception("El archivo Excel está vacío");
        }

        foreach ($rows as $index => $row)
        {
            $this->rowCount++;
            try {
                $this->processRow($row, $index);
            } catch (\Exception $e) {
                $this->addErrorRow($row, $index, $e->getMessage());
                continue;
            }
        }

        $this->generateErrorCSV();
        $this->logImportSummary();
    }

    protected function processRow($row, $index)
    {
        $validatedData = $this->validateRow($row);
        if ($validatedData->fails()) {
            throw new \Exception(implode(', ', $validatedData->errors()->all()));
        }

        $numeroGuia = $this->getValueFromRow($row, ['adm_numeroguia', 'ADM_NumeroGuia', 'numero_guia']);
        if (in_array($numeroGuia, $this->uniqueGuides)) {
            throw new \Exception("Número de guía duplicado");
        }

        SecondExcelData::create([
            'ADM_NumeroGuia' => $numeroGuia,
            'ADM_CreadoPor' => $this->getValueFromRow($row, ['adm_creadopor', 'ADM_CreadoPor', 'creado_por']),
            'created_at' => Carbon::createFromDate($this->year, $this->month, 1),
        ]);

        $this->uniqueGuides[] = $numeroGuia;
        $this->importedCount++;
    }

    protected function validateRow($row)
    {
        return Validator::make($row->toArray(), [
            'adm_numeroguia' => 'required',
            'adm_creadopor' => 'required',
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

        $csvFileName = 'error_rows_second_excel_' . now()->format('Y-m-d_His') . '.csv';
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

        Log::info('Resumen de importación del segundo archivo:', $summary);
    }

    public function getImportSummary()
    {
        return [
            'total_rows' => $this->rowCount,
            'imported_rows' => $this->importedCount,
            'error_rows' => count($this->errorRows),
        ];
    }
}
