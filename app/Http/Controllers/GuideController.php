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
        $pointId = Auth::user()->point_id;

        $operators = User::where('point_id', $pointId)
            ->whereHas('role', function ($query) {
                $query->where('name', 'operator');
            })->get();

        $totalGuides = Guide::where('point_id', $pointId)
            ->where('status', 'pending')
            ->count();

        $guidesToday = Guide::where('point_id', $pointId)
            ->where('status', 'pending')
            ->whereDate('created_at', today())
            ->count();

        $activeOperators = User::where('point_id', $pointId)
            ->whereHas('guides', function ($query) {
                $query->where('status', 'pending')
                    ->whereMonth('created_at', now()->month);
            })->count();

        $averageDailyGuides = round(
            Guide::where('point_id', $pointId)
                ->where('status', 'pending')
                ->whereMonth('created_at', now()->month)
                ->count() / now()->daysInMonth
        );

        $operatorStats = User::where('point_id', $pointId)
            ->whereHas('role', function ($query) {
                $query->where('name', 'operator');
            })->withCount([
                'guides as total_guides' => function ($query) {
                    $query->where('status', 'pending');
                },
                'guides as guides_today' => function ($query) {
                    $query->where('status', 'pending')
                        ->whereDate('created_at', today());
                }
            ])->get()->map(function ($operator) {
                $operator->average_daily = round(
                    $operator->guides()
                        ->where('status', 'pending')
                        ->whereMonth('created_at', now()->month)
                        ->count() / now()->daysInMonth
                );
                return $operator;
            });

        $guides = Guide::with(['user', 'cashier'])
            ->where('point_id', $pointId)
            ->where('status', 'pending')
            ->latest()
            ->paginate(40);

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

            $pointId = Auth::user()->point_id;

            $guide = new Guide();
            $guide->guide_number = $request->guide_number;
            $guide->user_id = $request->user_id;
            $guide->cashier_id = Auth::id();
            $guide->point_id = $pointId;
            $guide->registration_date = now();
            $guide->value = 0;
            $guide->client = 'No';
            $guide->save();

            $operator = User::find($request->user_id);

            $stats = [
                'totalGuides' => Guide::where('point_id', $pointId)->count(),
                'guidesToday' => Guide::where('point_id', $pointId)->whereDate('created_at', today())->count(),
                'averageDailyGuides' => round(Guide::where('point_id', $pointId)->whereMonth('created_at', now()->month)->count() / now()->daysInMonth),
                'operatorTotalGuides' => $operator->guides()->where('point_id', $pointId)->count(),
                'operatorGuidesToday' => $operator->guides()->where('point_id', $pointId)->whereDate('created_at', today())->count(),
                'operatorAverageDaily' => round($operator->guides()->where('point_id', $pointId)->whereMonth('created_at', now()->month)->count() / now()->daysInMonth),
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
        $pointId = Auth::user()->point_id;

        $operators = User::where('point_id', $pointId)
            ->whereHas('role', function ($query) {
                $query->where('name', 'operator');
            })->get();

        $latestGuides = Guide::where('point_id', $pointId)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        $todayGuidesCount = Guide::where('point_id', $pointId)
            ->whereDate('created_at', today())
            ->count();

        $todayGuidesValue = Guide::where('point_id', $pointId)
            ->whereDate('created_at', today())
            ->sum('value');

        $topOperator = User::where('point_id', $pointId)
            ->withCount(['guides' => function ($query) {
                $query->whereDate('created_at', today());
            }])
            ->orderByDesc('guides_count')
            ->first();

        return view('guides.create', compact('operators', 'latestGuides', 'todayGuidesCount', 'todayGuidesValue', 'topOperator'));
    }

    public function statistics()
    {
        $pointId = Auth::user()->point_id;

        $statistics = User::where('point_id', $pointId)
            ->whereHas('role', function ($query) {
                $query->where('name', 'operator');
            })
            ->withCount('guides')
            ->withSum('guides', 'value')
            ->get();

        return response()->json($statistics);
    }
}