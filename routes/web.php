<?php

use App\Http\Controllers\CashClosingController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\ExcelProcessController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard'); // Redirige al dashboard si está autenticado
    } else {
        return redirect('/login'); // Redirige al login si no está autenticado
    }
});


require __DIR__ . '/auth.php';

Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/cash-closings', [CashClosingController::class, 'index'])->name('cash_closings.index');
    Route::post('/cash-closings', [CashClosingController::class, 'store'])->name('cash_closings.store');
    Route::resource('user', 'App\Http\Controllers\UserController', ['except' => ['show']]);
    Route::get('profile', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\ProfileController@edit']);
    Route::put('profile', ['as' => 'profile.update', 'uses' => 'App\Http\Controllers\ProfileController@update']);
    Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);
    Route::get('/excel-upload', [ExcelProcessController::class, 'index'])->name('excel.upload.form');
    Route::post('/excel-upload/first', [ExcelProcessController::class, 'uploadFirst'])->name('excel.upload.first');
    Route::post('/excel-upload/second', [ExcelProcessController::class, 'uploadSecond'])->name('excel.upload.second');
    Route::post('/excel-process', [ExcelProcessController::class, 'processData'])->name('excel.process');
});

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    // Rutas de deudas
    Route::get('/debts', [DebtController::class, 'index'])->name('debts.index');
    Route::get('/debts/create', [DebtController::class, 'create'])->name('debts.create');
    Route::post('/debts', [DebtController::class, 'store'])->name('debts.store');
    Route::get('/debts/{debt}', [DebtController::class, 'show'])->name('debts.show');
    Route::get('/debts/{debt}/edit', [DebtController::class, 'edit'])->name('debts.edit');
    Route::put('/debts/{debt}', [DebtController::class, 'update'])->name('debts.update');
    Route::delete('/debts/{debt}', [DebtController::class, 'destroy'])->name('debts.destroy');

    // Ruta para marcar una deuda como pagada
    Route::patch('/debts/{debt}/mark-as-paid', [DebtController::class, 'markAsPaid'])->name('debts.markAsPaid');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');

    Route::get('/guides', [GuideController::class, 'index'])->name('guides.index');
    Route::get('/guides/create', [GuideController::class, 'create'])->name('guides.create');
    Route::post('/guides', [GuideController::class, 'store'])->name('guides.store');
    Route::get('/guides/{guide}', [GuideController::class, 'show'])->name('guides.show');
    Route::get('/guides/{guide}/edit', [GuideController::class, 'edit'])->name('guides.edit');
    Route::put('/guides/{guide}', [GuideController::class, 'update'])->name('guides.update');
    Route::delete('/guides/{guide}', [GuideController::class, 'destroy'])->name('guides.destroy');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('{page}', ['as' => 'page.index', 'uses' => 'App\Http\Controllers\PageController@index']);
});
