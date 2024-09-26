<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Guide;
use App\Models\User;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display all the static pages when authenticated
     *
     * @param string $page
     * @return \Illuminate\View\View
     */
    public function index(string $page)
    {
        if (view()->exists("pages.{$page}")) {
            return view("pages.{$page}");
        }

        return abort(404);
    }
    public function dashboard()
    {
        $totalGuides = Guide::count();
        $totalDebts = Debt::sum('amount');
        $guidesToday = Guide::whereDate('created_at', today())->count();
        $activeOperators = User::whereHas('guides', function ($query) {
            $query->whereMonth('created_at', now()->month);
        })->count();

        // Aquí deberías añadir la lógica para obtener los datos para los gráficos

        return view('pages.dashboard', compact('totalGuides', 'totalDebts', 'guidesToday', 'activeOperators'));
    }
}
