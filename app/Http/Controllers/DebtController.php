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
        $pointId = Auth::user()->point_id;

        $totalDebts = Debt::pending()->where('point_id', $pointId)->where('is_expense', false)->where('status',  'pending')->sum('amount');
        $debtsToday = Debt::pending()->where('point_id', $pointId)->where('is_expense', false)->where('status',  'pending')->whereDate('created_at', today())->sum('amount');
        $averageDailyDebts = round(Debt::pending()->where('point_id', $pointId)->where('is_expense', false)->where('status',  'pending')->whereMonth('created_at', now()->month)->avg('amount'), 2);
        $totalDebtsCurrentMonth = Debt::pending()->where('point_id', $pointId)->where('status',  'pending')->where('is_expense', false)->whereMonth('created_at', now()->month)->sum('amount');
        $expensesToday = Debt::where('point_id', $pointId)->where('status',  'pending')->where('is_expense', true)->whereDate('created_at', today())->sum('amount');

        $operators = User::where('point_id', $pointId)->whereHas('role', function ($query) {
            $query->whereIn('name', ['operator', 'misc']);
        })->get();

        $latestDebts = Debt::where('point_id', $pointId)->with('user')->where('status',  'pending')->latest()->take(10)->get();

        $debtsPivotTable = Debt::where('point_id', $pointId)
            ->where('is_expense', false)
            ->where('status',  'pending')
            ->whereMonth('created_at', now()->month)
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
            'expensesToday',
            'operators',
            'latestDebts',
            'debtsPivotTable',
            'operatorNames'
        ));
    }
    public function create()
    {
        $pointId = Auth::user()->point_id;
        $operators = User::where('point_id', $pointId)->whereHas('role', function ($query) {
            $query->where('name', 'operator');
        })->get();

        return view('debts.create', compact('operators'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required',
            'description' => 'nullable|string|max:255',
            'is_expense' => 'required|boolean',
        ]);

        if (!$request->is_expense) {
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);
        }

        $debt = new Debt();
        $debt->user_id = $request->is_expense ? 19 : $request->user_id;
        $amount = preg_replace('/[^0-9.]/', '', $request->input('amount'));
        $debt->amount = is_numeric($amount) ? (float) $amount : 0;
        $debt->observation = $request->description;
        $debt->cashier_id = Auth::id();
        $debt->point_id = Auth::user()->point_id;
        $debt->status = Debt::STATUS_PENDING;
        $debt->is_expense = $request->is_expense;
        $debt->save();

        $stats = $this->getUpdatedStats();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $request->is_expense ? 'Gasto registrado exitosamente.' : 'Deuda registrada exitosamente.',
                'debt' => $debt->load('user'),
                'stats' => $stats,
            ]);
        }

        return redirect()->route('debts.index')->with('success', $request->is_expense ? 'Gasto registrado exitosamente.' : 'Deuda registrada exitosamente.');
    }


    public function pendingDebts()
    {
        $pointId = Auth::user()->point_id;

        $pendingDebts = Debt::where('point_id', $pointId)
            ->where('status', Debt::STATUS_PENDING)
            ->with('user')
            ->latest()
            ->paginate(15);

        $paymentsToday = Debt::where('point_id', $pointId)
            ->whereDate('paid_at', today())
            ->count();

        $totalPaymentsToday = Debt::where('point_id', $pointId)
            ->whereDate('paid_at', today())
            ->sum('amount');

        return view('debts.pending', compact('pendingDebts', 'paymentsToday', 'totalPaymentsToday'));
    }

    public function markAsPaid(Debt $debt)
    {
        if ($debt->point_id !== Auth::user()->point_id) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para modificar esta deuda.'], 403);
        }

        $debt->status = Debt::STATUS_PAID;
        $debt->paid_at = now();
        $debt->save();

        return response()->json(['success' => true, 'message' => 'Deuda marcada como pagada exitosamente.']);
    }

    public function show(Debt $debt)
    {
        if ($debt->point_id !== Auth::user()->point_id) {
            return redirect()->route('debts.index')->with('error', 'No tienes permiso para ver esta deuda.');
        }

        return view('debts.show', compact('debt'));
    }

    public function edit(Debt $debt)
    {
        if ($debt->point_id !== Auth::user()->point_id) {
            return redirect()->route('debts.index')->with('error', 'No tienes permiso para editar esta deuda.');
        }

        $pointId = Auth::user()->point_id;
        $operators = User::where('point_id', $pointId)->whereHas('role', function ($query) {
            $query->where('name', 'operator');
        })->get();

        return view('debts.edit', compact('debt', 'operators'));
    }

    public function update(Request $request, Debt $debt)
    {
        if ($debt->point_id !== Auth::user()->point_id) {
            return redirect()->route('debts.index')->with('error', 'No tienes permiso para actualizar esta deuda.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $debt->user_id = $request->user_id;
        $debt->amount = $request->amount;
        $debt->observation = $request->description;
        $debt->save();

        return redirect()->route('debts.index')->with('success', 'Deuda actualizada exitosamente.');
    }

    public function destroy(Debt $debt)
    {
        if ($debt->point_id !== Auth::user()->point_id) {
            return redirect()->route('debts.index')->with('error', 'No tienes permiso para eliminar esta deuda.');
        }

        $debt->delete();

        return redirect()->route('debts.index')->with('success', 'Deuda eliminada exitosamente.');
    }

    private function getUpdatedStats()
    {
        $pointId = Auth::user()->point_id;

        return [
            'totalDebts' => Debt::pending()->where('point_id', $pointId)->where('is_expense', false)->sum('amount'),
            'debtsToday' => Debt::pending()->where('point_id', $pointId)->where('is_expense', false)->whereDate('created_at', today())->sum('amount'),
            'averageDailyDebts' => round(Debt::pending()->where('point_id', $pointId)->where('is_expense', false)->whereMonth('created_at', now()->month)->avg('amount'), 2),
            'totalDebtsCurrentMonth' => Debt::pending()->where('point_id', $pointId)->where('is_expense', false)->whereMonth('created_at', now()->month)->sum('amount'),
            'totalExpenses' => Debt::where('point_id', $pointId)->where('is_expense', true)->sum('amount'),
            'expensesToday' => Debt::where('point_id', $pointId)->where('is_expense', true)->whereDate('created_at', today())->sum('amount'),
        ];
    }
}
