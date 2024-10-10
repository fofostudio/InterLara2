<?php

namespace App\Http\Controllers;

use App\Models\CashClosing;
use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashClosingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $pointId = $user->point_id;
    
        $today = now()->toDateString();
    
        $cashClosings = CashClosing::where('point_id', $pointId)
            ->latest()
            ->paginate(10);
    
        $totalSalesMonth = CashClosing::where('point_id', $pointId)
            ->whereMonth('date', now()->month)
            ->sum('total_sales');
    
        $totalExpensesMonth = CashClosing::where('point_id', $pointId)
            ->whereMonth('date', now()->month)
            ->sum('expenses');
    
        $totalCashMonth = CashClosing::where('point_id', $pointId)
            ->whereMonth('date', now()->month)
            ->sum('cash');
    
        $totalDebtMonth = CashClosing::where('point_id', $pointId)
            ->whereMonth('date', now()->month)
            ->sum('debt');
    
        // Obtener gastos del día actual
        $expensesToday = Debt::where('point_id', $pointId)
        ->where('status',  'pending')
            ->where('is_expense', true)
            ->whereDate('created_at', $today)
            ->sum('amount');
    
        // Obtener deudas del día actual
        $debtsToday = Debt::where('point_id', $pointId)
        ->where('status',  'pending')

            ->where('is_expense', false)
            ->whereDate('created_at', $today)
            ->sum('amount');
    
        return view('cash_closings.index', compact(
            'cashClosings',
            'totalSalesMonth',
            'totalExpensesMonth',
            'totalCashMonth',
            'totalDebtMonth',
            'expensesToday',
            'debtsToday'
        ));
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $pointId = $user->point_id;

            $validated = $request->validate([
                'date' => [
                    'required',
                    'date',
                    function ($attribute, $value, $fail) use ($pointId) {
                        $exists = CashClosing::where('date', $value)
                            ->where('point_id', $pointId)
                            ->exists();
                        if ($exists) {
                            $fail('Ya existe un cierre de caja para la fecha seleccionada en este punto.');
                        }
                    },
                ],
                'total_sales' => 'required|numeric|min:0',
                'expenses' => 'required|numeric|min:0',
                'cash' => 'required|numeric|min:0',
                'cancelled_guides' => 'required|numeric|min:0',
                'debt' => 'required|numeric|min:0',
                'digital_wallets' => 'required|numeric|min:0',

            ]);

            $validated['user_id'] = $user->id;
            $validated['point_id'] = auth()->user()->point_id; // Añadimos el point_id del usuario autenticado

            $cashClosing = CashClosing::create($validated);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Cierre de caja registrado exitosamente.']);
            }

            return redirect()->route('cash_closings.index')->with('success', 'Cierre de caja registrado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al procesar cierre de caja: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al procesar la solicitud: ' . $e->getMessage()], 500);
            }

            return back()->withErrors(['error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
        }
    }
}