<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DebtController extends Controller
{
    public function index()
    {
        $totalDebts = Debt::pending()->sum('amount');
        $debtsToday = Debt::pending()->whereDate('created_at', today())->sum('amount');
        $averageDailyDebts = round(Debt::pending()->whereMonth('created_at', now()->month)->avg('amount'), 2);
        $totalDebtsCurrentMonth = Debt::pending()->whereMonth('created_at', now()->month)->sum('amount');
        $operators = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['operator', 'misc']);
        })->get();


        $latestDebts = Debt::pending()->with('user')->latest()->take(10)->get();

        $debtsPivotTable = Debt::pending()->whereMonth('created_at', now()->month)
            ->select('user_id', DB::raw('DAY(created_at) as day'), DB::raw('SUM(amount) as total'))
            ->groupBy('user_id', 'day')
            ->get()
            ->groupBy('user_id')
            ->map(function ($items) {
                return $items->pluck('total', 'day');
            });

        $operatorNames = $operators->pluck('name', 'id');

        return view('debts.index', compact(
            'totalDebts',
            'debtsToday',
            'averageDailyDebts',
            'totalDebtsCurrentMonth',
            'operators',
            'latestDebts',
            'debtsPivotTable',
            'operatorNames'
        ));
    }

    public function create()
    {
        $operators = User::whereHas('role', function ($query) {
            $query->where('name', 'operator');
        })->get();

        return view('debts.create', compact('operators'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required',
            'description' => 'nullable|string|max:255',
        ]);

        $debt = new Debt();
        $debt->user_id = $request->user_id;
        $amount = preg_replace('/[^0-9.]/', '', $request->input('amount'));  // Eliminar cualquier cosa que no sea número o punto decimal
        $debt->amount = is_numeric($amount) ? (float) $amount : 0;  // Asegurar que sea un número
                $debt->observation = $request->description;
        $debt->cashier_id = Auth::id();
        $debt->status = Debt::STATUS_PENDING;
        $debt->save();

        $stats = $this->getUpdatedStats();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Deuda registrada exitosamente.',
                'debt' => $debt->load('user'),
                'stats' => $stats,
            ]);
        }

        return redirect()->route('debts.index')->with('success', 'Deuda registrada exitosamente.');
    }
    public function markAsPaid(Debt $debt)
    {
        $debt->markAsPaid();

        return redirect()->route('debts.index')->with('success', 'Deuda marcada como pagada exitosamente.');
    }

    public function show(Debt $debt)
    {
        return view('debts.show', compact('debt'));
    }

    public function edit(Debt $debt)
    {
        $operators = User::whereHas('role', function ($query) {
            $query->where('name', 'operator');
        })->get();

        return view('debts.edit', compact('debt', 'operators'));
    }

    public function update(Request $request, Debt $debt)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $debt->user_id = $request->user_id;
        $debt->amount = $request->amount;
        $debt->description = $request->description;
        $debt->save();

        return redirect()->route('debts.index')->with('success', 'Deuda actualizada exitosamente.');
    }

    public function destroy(Debt $debt)
    {
        $debt->delete();

        return redirect()->route('debts.index')->with('success', 'Deuda eliminada exitosamente.');
    }

    private function getUpdatedStats()
    {
        return [
            'totalDebts' => Debt::pending()->sum('amount'),
            'debtsToday' => Debt::pending()->whereDate('created_at', today())->sum('amount'),
            'averageDailyDebts' => round(Debt::pending()->whereMonth('created_at', now()->month)->avg('amount'), 2),
            'totalDebtsCurrentMonth' => Debt::pending()->whereMonth('created_at', now()->month)->sum('amount'),
        ];
    }
}
