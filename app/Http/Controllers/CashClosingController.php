<?php

namespace App\Http\Controllers;

use App\Models\CashClosing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashClosingController extends Controller
{
    public function index()
    {
        $cashClosings = CashClosing::latest()->paginate(10);

        $totalSalesMonth = CashClosing::whereMonth('date', now()->month)->sum('total_sales');
        $totalExpensesMonth = CashClosing::whereMonth('date', now()->month)->sum('expenses');
        $totalCashMonth = CashClosing::whereMonth('date', now()->month)->sum('cash');
        $totalDebtMonth = CashClosing::whereMonth('date', now()->month)->sum('debt');

        return view('cash_closings.index', compact(
            'cashClosings',
            'totalSalesMonth',
            'totalExpensesMonth',
            'totalCashMonth',
            'totalDebtMonth'
        ));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date|unique:cash_closings,date',
                'total_sales' => 'required|numeric|min:0',
                'expenses' => 'required|numeric|min:0',
                'cash' => 'required|numeric|min:0',
                'cancelled_guides' => 'required|numeric|min:0',
                'debt' => 'required|numeric|min:0',
            ], [
                'date.unique' => 'Ya existe un cierre de caja para la fecha seleccionada.'
            ]);
            $validated['user_id'] = auth()->id();

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
