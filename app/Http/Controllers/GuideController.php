<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class GuideController extends Controller
{
    public function index()
    {
        // Obtener solo operadores
        $operators = User::whereHas('role', function ($query) {
            $query->where('name', 'operator');
        })->get();

        // Contar solo las guías con estado 'pending'
        $totalGuides = Guide::where('status', 'pending')->count();

        // Contar las guías de hoy con estado 'pending'
        $guidesToday = Guide::where('status', 'pending')
            ->whereDate('created_at', today())
            ->count();

        // Contar los operadores activos basados en las guías 'pending' del mes actual
        $activeOperators = User::whereHas('guides', function ($query) {
            $query->where('status', 'pending')->whereMonth('created_at', now()->month);
        })->count();

        // Calcular el promedio diario de guías con estado 'pending' en el mes actual
        $averageDailyGuides = round(
            Guide::where('status', 'pending')
                ->whereMonth('created_at', now()->month)
                ->count() / now()->daysInMonth
        );

        // Obtener estadísticas de los operadores, filtrando por guías 'pending'
        $operatorStats = User::whereHas('role', function ($query) {
            $query->where('name', 'operator');
        })->withCount([
            // Contar todas las guías 'pending'
            'guides as total_guides' => function ($query) {
                $query->where('status', 'pending');
            },
            // Contar las guías de hoy 'pending'
            'guides as guides_today' => function ($query) {
                $query->where('status', 'pending')
                    ->whereDate('created_at', today());
            }
        ])->get()->map(function ($operator) {
            // Calcular el promedio diario de guías 'pending' del mes actual para cada operador
            $operator->average_daily = round(
                $operator->guides()
                    ->where('status', 'pending')
                    ->whereMonth('created_at', now()->month)
                    ->count() / now()->daysInMonth
            );
            return $operator;
        });

        // Obtener las guías con estado 'pending'
        $guides = Guide::with(['user', 'cashier'])
            ->where('status', 'pending')  // Filtra por el estado 'pending'
            ->latest()
            ->paginate(40);

        // Retornar la vista con las variables modificadas
        return view('guides.index', compact(
            'operators',
            'totalGuides',
            'guidesToday',
            'activeOperators',
            'averageDailyGuides',
            'operatorStats',
            'guides'
        ));
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'guide_number' => 'required|unique:guides',
                'user_id' => 'required|exists:users,id',
            ], [
                'guide_number.unique' => 'El número de guía ya ha sido registrado anteriormente.',
            ]);

            $guide = new Guide();
            $guide->guide_number = $request->guide_number;
            $guide->user_id = $request->user_id;
            $guide->cashier_id = Auth::id();
            $guide->registration_date = now();
            $guide->value = 0;
            $guide->client = 'No';
            $guide->save();

            $operator = User::find($request->user_id);

            $stats = [
                'totalGuides' => Guide::count(),
                'guidesToday' => Guide::whereDate('created_at', today())->count(),
                'averageDailyGuides' => round(Guide::whereMonth('created_at', now()->month)->count() / now()->daysInMonth),
                'operatorTotalGuides' => $operator->guides()->count(),
                'operatorGuidesToday' => $operator->guides()->whereDate('created_at', today())->count(),
                'operatorAverageDaily' => round($operator->guides()->whereMonth('created_at', now()->month)->count() / now()->daysInMonth),
            ];

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Guía registrada exitosamente.',
                    'guide' => $guide->load('user')->toArray(),
                    'stats' => $stats,
                ]);
            }

            return redirect()->route('guides.index')->with('success', 'Guía registrada exitosamente.');
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->errors()['guide_number'][0] ?? 'Error al registrar la guía.',
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function create()
    {
        $operators = User::where('role', 'operator')->get();
        $latestGuides = Guide::with('user')->latest()->take(5)->get();
        $todayGuidesCount = Guide::whereDate('created_at', today())->count();
        $todayGuidesValue = Guide::whereDate('created_at', today())->sum('value');
        $topOperator = User::withCount(['guides' => function ($query) {
            $query->whereDate('created_at', today());
        }])->orderByDesc('guides_count')->first();

        return view('guides.create', compact('operators', 'latestGuides', 'todayGuidesCount', 'todayGuidesValue', 'topOperator'));
    }

    public function statistics()
    {
        $statistics = User::where('role', 'operator')
            ->withCount('guides')
            ->withSum('guides', 'value')
            ->get();

        return response()->json($statistics);
    }
}
