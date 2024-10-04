<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FirstExcelImport;
use App\Imports\SecondExcelImport;
use App\Models\FirstExcelData;
use App\Models\SecondExcelData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExcelProcessController extends Controller
{
    public function index()
    {
        return view('excel.upload-and-results', [
            'class' => '',
            'elementActive' => 'excel_upload'
        ]);
    }

    public function uploadFirst(Request $request)
    {
        Log::info('Iniciando uploadFirst');
        try {
            Log::info('Validando request');
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:xlsx,xls|max:5120', // 5MB max
                'month' => 'required|integer|min:1|max:12',
            ]);

            if ($validator->fails()) {
                Log::error('Validación fallida: ' . json_encode($validator->errors()));
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            Log::info('Validación exitosa, procesando archivo');
            $file = $request->file('file');
            Log::info('Archivo recibido: ' . $file->getClientOriginalName() . ', tamaño: ' . $file->getSize() . ' bytes');

            $month = $request->input('month');
            $year = date('Y');

            Log::info('Iniciando transacción DB');
            DB::beginTransaction();

            Log::info('Verificando datos existentes');
            if (FirstExcelData::whereYear('fecha_venta', $year)->whereMonth('fecha_venta', $month)->exists()) {
                if (!$request->input('overwrite', false)) {
                    Log::info('Datos existentes, solicitando confirmación para sobrescribir');
                    return response()->json(['exists' => true]);
                }
                Log::info('Eliminando datos existentes');
                FirstExcelData::whereYear('fecha_venta', $year)->whereMonth('fecha_venta', $month)->delete();
            }

            Log::info('Creando instancia de FirstExcelImport');
            $import = new FirstExcelImport($month, $year);

            Log::info('Importando datos');
            Excel::import($import, $file);

            Log::info('Obteniendo resumen de importación');
            $summary = $import->getImportSummary();

            Log::info('Commit de la transacción');
            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Archivo procesado exitosamente.',
                'summary' => $summary,
            ];

            if ($summary['error_rows'] > 0) {
                Log::info('Errores encontrados, preparando archivo de errores');
                $errorFiles = Storage::files('error_csvs');
                $latestErrorFile = end($errorFiles);
                $response['error_csv_url'] = Storage::url($latestErrorFile);
            }

            Log::info('Importación completada con éxito');
            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en uploadFirst: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Error al procesar el archivo: ' . $e->getMessage()], 500);
        }
    }

    public function uploadSecond(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $month = $request->input('month');
        $year = date('Y');

        try {
            DB::beginTransaction();

            if (SecondExcelData::whereYear('created_at', $year)->whereMonth('created_at', $month)->exists()) {
                if (!$request->input('overwrite', false)) {
                    return response()->json(['exists' => true]);
                }
                SecondExcelData::whereYear('created_at', $year)->whereMonth('created_at', $month)->delete();
            }

            $import = new SecondExcelImport($month, $year);
            Excel::import($import, $request->file('file'));

            $summary = $import->getImportSummary();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Archivo procesado exitosamente.',
                'summary' => $summary,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar el segundo archivo Excel: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al procesar el archivo: ' . $e->getMessage()], 500);
        }
    }

    public function processData(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
        ]);

        $month = $request->input('month');
        $year = date('Y');

        try {
            $crossedData = $this->crossData($month, $year);

            return response()->json([
                'success' => true,
                'message' => 'Datos procesados exitosamente.',
                'crossedData' => $crossedData
            ]);
        } catch (\Exception $e) {
            Log::error('Error al procesar los datos: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al procesar los datos: ' . $e->getMessage()], 500);
        }
    }

    private function crossData($month, $year)
    {
        $crossedData = FirstExcelData::join('second_excel_data', 'first_excel_data.numero_guia', '=', 'second_excel_data.ADM_NumeroGuia')
            ->whereYear('first_excel_data.fecha_venta', $year)
            ->whereMonth('first_excel_data.fecha_venta', $month)
            ->select(
                'first_excel_data.numero_guia',
                'first_excel_data.ciudad_origen',
                'first_excel_data.ciudad_destino',
                'first_excel_data.fecha_venta',
                'first_excel_data.valor_transporte',
                'second_excel_data.ADM_CreadoPor'
            )
            ->get();

        return $crossedData;
    }

    public function downloadErrorCsv($filename)
    {
        $path = storage_path('app/error_csvs/' . $filename);

        if (!Storage::exists('error_csvs/' . $filename)) {
            abort(404, 'El archivo no existe.');
        }

        return response()->download($path);
    }
}
