<?php

namespace App\Http\Controllers;

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
        $currentMonth = now()->format('Y-m');
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        $initialData = [
            'totalGuides' => $this->getTotalGuides(),
            'totalDebts' => $this->getTotalDebts(),
            'guidesToday' => $this->getGuidesToday(),
            'activeOperators' => $this->getActiveOperators(),
            'topDebts' => $this->getTopDebts($startDate, $endDate),
            'topGuides' => $this->getTopGuides($startDate, $endDate),
            'monthlyGuides' => $this->getMonthlyGuides($startDate, $endDate),
            'monthlyDebts' => $this->getMonthlyDebts($startDate, $endDate),
            'excelTransportValueByUser' => $this->getExcelTransportValueByUser($startDate, $endDate),
            'guideTransportValueByUser' => $this->getGuideTransportValueByUser($startDate, $endDate),
        ];

        return view('pages.dashboard', compact('initialData', 'currentMonth'));
    }

    public function getMonthlyData(Request $request)
    {
        $month = $request->input('month');
        $target = $request->input('target');
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $data = [];

        switch ($target) {
            case 'chartGuides':
                $data = $this->getMonthlyGuides($startDate, $endDate);
                break;
            case 'chartDebts':
                $data = $this->getMonthlyDebts($startDate, $endDate);
                break;
            case 'excelTransportTable':
                $data = $this->getExcelTransportValueByUser($startDate, $endDate);
                break;
            case 'guideTransportTable':
                $data = $this->getGuideTransportValueByUser($startDate, $endDate);
                break;
        }

        return response()->json(['data' => $data]);
    }

    private function getTotalGuides()
    {
        return Guide::count();
    }

    private function getTotalDebts()
    {
        return Debt::sum('amount');
    }

    private function getGuidesToday()
    {
        return Guide::whereDate('created_at', today())->count();
    }

    private function getActiveOperators()
    {
        return User::whereHas('guides', function ($query) {
            $query->whereMonth('created_at', now()->month);
        })->count();
    }

    private function getTopDebts($startDate, $endDate)
    {
        return Debt::with('user')
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

    private function getTopGuides($startDate, $endDate)
    {
        return Guide::with('user')
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

    private function getMonthlyGuides($startDate, $endDate)
    {
        $guides = Guide::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $guides->pluck('date'),
            'values' => $guides->pluck('count'),
        ];
    }

    private function getMonthlyDebts($startDate, $endDate)
    {
        $debts = Debt::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $debts->pluck('date'),
            'values' => $debts->pluck('total'),
        ];
    }

    private function getExcelTransportValueByUser($startDate, $endDate)
    {
        return FirstExcelData::join('second_excel_data', 'first_excel_data.numero_guia', '=', 'second_excel_data.ADM_NumeroGuia')
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
    private function getGuideTransportValueByUser($startDate, $endDate)
    {
        return Guide::join('first_excel_data', 'guides.guide_number', '=', 'first_excel_data.numero_guia')
            ->join('users', 'guides.user_id', '=', 'users.id')
            ->whereBetween('guides.created_at', [$startDate, $endDate])
            ->select('users.name', DB::raw('SUM(first_excel_data.valor_transporte) as total_value'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_value')
            ->get()
            ->pluck('total_value', 'name')
            ->toArray();
    }
}
