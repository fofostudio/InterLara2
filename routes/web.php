<?php

use App\Http\Controllers\CashClosingController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\ExcelProcessController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

require __DIR__ . '/auth.php';

Route::get('/debts/pend', [DebtController::class, 'pendingDebts'])->name('debts.pending');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/data', [PageController::class, 'getMonthlyData'])->name('dashboard.data');

    // Profile
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'password'])->name('profile.password');

    // Cash Closings
    Route::get('/cash-closings', [CashClosingController::class, 'index'])->name('cash_closings.index');
    Route::post('/cash-closings', [CashClosingController::class, 'store'])->name('cash_closings.store');

    // Excel Process
    Route::prefix('excel')->name('excel.')->group(function () {
        Route::get('/upload', [ExcelProcessController::class, 'index'])->name('upload.form');
        Route::post('/upload/first', [ExcelProcessController::class, 'uploadFirst'])->name('upload.first');
        Route::post('/upload/second', [ExcelProcessController::class, 'uploadSecond'])->name('upload.second');
        Route::post('/process', [ExcelProcessController::class, 'processData'])->name('process');
    });

    // Debts
    Route::resource('debts', DebtController::class);
    Route::patch('/debts/{debt}/mark-as-paid', [DebtController::class, 'markAsPaid'])->name('debts.markAsPaid');

    // Guides
    Route::resource('guides', GuideController::class);

    // Users
    Route::resource('user', UserController::class)->except(['show']);

    // Catch-all route for pages
    Route::get('{page}', [PageController::class, 'index'])->name('page.index');
});

// SuperAdmin routes
Route::group(['middleware' => ['auth', 'role:superadmin'], 'prefix' => 'superadmin', 'as' => 'superadmin.'], function () {
    Route::resource('points', 'App\Http\Controllers\PointController');
    Route::resource('users', 'App\Http\Controllers\SuperAdminUserController');
    Route::resource('roles', 'App\Http\Controllers\RoleController')->except(['show']);
    Route::post('points/{point}/assign-user', 'App\Http\Controllers\PointController@assignUser')->name('points.assignUser');
    Route::delete('points/{point}/remove-user/{user}', 'App\Http\Controllers\PointController@removeUser')->name('points.removeUser');
});