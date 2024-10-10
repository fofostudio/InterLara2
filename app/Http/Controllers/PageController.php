<?php

namespace App\Http\Controllers;

use App\Models\CashClosing;
use App\Models\Debt;
use App\Models\FirstExcelData;
use App\Models\Guide;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(string $page)
    {
        if (view()->exists("pages.{$page}")) {
            return view("pages.{$page}");
        }
        return abort(404);
    }

    public function dashboard()
    {
        $user = auth()->user();
        $pointId = $user->point_id;

        if ($user->role_id != 1 && !$pointId) {
            return abort(403, 'No tienes un punto asignado.');
        }

        $currentMonth = now()->format('Y-m');
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        $initialData = [
            'totalGuides' => $this->getTotalGuides($pointId),
            'totalDebts' => $this->getTotalDebts($pointId),
            'guidesToday' => $this->getGuidesToday($pointId),
            'activeOperators' => $this->getActiveOperators($pointId),
            'topDebts' => $this->getTopDebts($startDate, $endDate, $pointId),
            'topGuides' => $this->getTopGuides($startDate, $endDate, $pointId),
            'monthlyGuides' => $this->getMonthlyGuides($startDate, $endDate, $pointId),
            'monthlyDebts' => $this->getMonthlyDebts($startDate, $endDate, $pointId),
            'excelTransportValueByUser' => $this->getExcelTransportValueByUser($startDate, $endDate, $pointId),
            'guideTransportValueByUser' => $this->getGuideTransportValueByUser($startDate, $endDate, $pointId),
            'dailySales' => $this->getDailySales($startDate, $endDate, $pointId),
            'bestSalesDay' => $this->getBestSalesDay($startDate, $endDate, $pointId),
            'bestGuidesDay' => $this->getBestGuidesDay($startDate, $endDate, $pointId),
            'totalSalesMonth' => $this->getTotalSalesMonth($startDate, $endDate, $pointId),
            'totalExpensesMonth' => $this->getTotalExpensesMonth($startDate, $endDate, $pointId),
        ];

        return view('pages.dashboard', compact('initialData', 'currentMonth'));
    }
    private function getTotalSalesMonth($startDate, $endDate, $pointId)
    {
        return CashClosing::where('point_id', $pointId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('total_sales');
    }
    private function getTotalExpensesMonth($startDate, $endDate, $pointId)
    {
        return CashClosing::where('point_id', $pointId)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('expenses');
    }


    private function getDailySales($startDate, $endDate, $pointId)
    {
        $sales = CashClosing::where('point_id', $pointId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get(['date', 'total_sales']);

        return [
            'labels' => $sales->pluck('date')->map(function ($date) {
                return Carbon::parse($date)->format('d/m');
            }),
            'values' => $sales->pluck('total_sales'),
        ];
    }
    private function getBestSalesDay($startDate, $endDate, $pointId)
    {
        $bestDay = CashClosing::where('point_id', $pointId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderByDesc('total_sales')
            ->first();

        return $bestDay ? [
            'date' => Carbon::parse($bestDay->date)->format('d/m/Y'),
            'amount' => $bestDay->total_sales
        ] : null;
    }


    private function getBestGuidesDay($startDate, $endDate, $pointId)
    {
        $bestDay = Guide::where('point_id', $pointId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->orderByDesc('count')
            ->first();

        return $bestDay ? [
            'date' => Carbon::parse($bestDay->date)->format('d/m/Y'),
            'count' => $bestDay->count
        ] : null;
    }

    public function getMonthlyData(Request $request)
    {
        $user = auth()->user();
        $pointId = $user->point_id;
        if ($user->role_id != 1 && !$pointId) {
            return response()->json(['error' => 'No tienes un punto asignado.'], 403);
        }

        $month = $request->input('month');
        $target = $request->input('target');
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $data = [];

        switch ($target) {
            case 'chartDailySales':
                $data = $this->getDailySales($startDate, $endDate, $pointId);
                break;
            case 'chartGuides':
                $data = $this->getMonthlyGuides($startDate, $endDate, $pointId);
                break;
            case 'chartDebts':
                $data = $this->getMonthlyDebts($startDate, $endDate, $pointId);
                break;
            case 'excelTransportTable':
                $data = $this->getExcelTransportValueByUser($startDate, $endDate, $pointId);
                break;
            case 'guideTransportTable':
                $data = $this->getGuideTransportValueByUser($startDate, $endDate, $pointId);
                break;
        }

        return response()->json(['data' => $data]);
    }

    private function getTotalGuides($pointId)
    {
        return Guide::where('point_id', $pointId)->count();
    }

    private function getTotalDebts($pointId)
    {
        return Debt::where('point_id', $pointId)
            ->where('status', Debt::STATUS_PENDING)
            ->sum('amount');
    }

    private function getGuidesToday($pointId)
    {
        return Guide::where('point_id', $pointId)->whereDate('created_at', today())->count();
    }

    private function getActiveOperators($pointId)
    {
        return User::where('point_id', $pointId)
            ->whereHas('guides', function ($query) {
                $query->whereMonth('created_at', now()->month);
            })->count();
    }

    private function getTopDebts($startDate, $endDate, $pointId)
    {
        return Debt::with('user')
            ->where('point_id', $pointId)
            ->where('status', 'pending')  // Asumiendo que 'pending' es el valor para deudas pendientes

            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('user_id', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('user_id')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get()
            ->map(function ($debt) {
                return [
                    'name' => $debt->user->name,
                    'amount' => $debt->total_amount,
                ];
            });
    }

    private function getTopGuides($startDate, $endDate, $pointId)
    {

        return Guide::with('user')
            ->where('point_id', $pointId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('user_id', DB::raw('COUNT(*) as guide_count'), DB::raw('SUM(value) as total_value'))
            ->groupBy('user_id')
            ->orderByDesc('guide_count')
            ->limit(5)
            ->get()
            ->map(function ($guide) {
                return [
                    'name' => $guide->user->name,
                    'guideCount' => $guide->guide_count,
                    'totalValue' => $guide->total_value,
                ];
            });
    }

    private function getMonthlyGuides($startDate, $endDate, $pointId)
    {
        $guides = Guide::where('point_id', $pointId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $guides->pluck('date'),
            'values' => $guides->pluck('count'),
        ];
    }

    private function getMonthlyDebts($startDate, $endDate, $pointId)
    {
        $debts = Debt::where('point_id', $pointId)
            ->where('status', 'pending')  // Asumiendo que 'pending' es el valor para deudas pendientes

            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $debts->pluck('date'),
            'values' => $debts->pluck('total'),
        ];
    }

    private function getExcelTransportValueByUser($startDate, $endDate, $pointId)
    {
        return FirstExcelData::join('second_excel_data', 'first_excel_data.numero_guia', '=', 'second_excel_data.ADM_NumeroGuia')
            // Filtrar por point_id en ambas tablas
            ->where('first_excel_data.point_id', $pointId)
            ->where('second_excel_data.point_id', $pointId)
            ->whereBetween('first_excel_data.fecha_venta', [$startDate, $endDate])
            ->select('second_excel_data.ADM_CreadoPor', DB::raw('SUM(first_excel_data.valor_transporte) as total_value'))
            ->groupBy('second_excel_data.ADM_CreadoPor')
            ->orderByDesc('total_value')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->ADM_CreadoPor => (float) $item->total_value];
            })
            ->toArray();
    }


    private function getGuideTransportValueByUser($startDate, $endDate, $pointId)
    {
        return Guide::join('first_excel_data', 'guides.guide_number', '=', 'first_excel_data.numero_guia')
            // Filtrar por point_id en la tabla first_excel_data
            ->where('first_excel_data.point_id', $pointId)
            ->whereBetween('guides.created_at', [$startDate, $endDate])
            ->select('users.name', DB::raw('SUM(first_excel_data.valor_transporte) as total_value'))
            ->join('users', 'guides.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_value')
            ->get()
            ->pluck('total_value', 'name')
            ->toArray();
    }
}
