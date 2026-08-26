<?php

use App\Http\Controllers\Auth\WebAuthController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LoanController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [
        WebAuthController::class,
        'create',
    ])->name('login');

    Route::post('/login', [
        WebAuthController::class,
        'store',
    ])->name('login.store');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ])->name('dashboard');

    Route::get('/customers', [
        CustomerController::class,
        'index',
    ])->name('customers.index');

    Route::get('/customers/{customer}', [
        CustomerController::class,
        'show',
    ])->name('customers.show');

    Route::get('/loans', [
        LoanController::class,
        'index',
    ])->name('loans.index');

    Route::get('/loans/create', [
        LoanController::class,
        'create',
    ])->name('loans.create');

    Route::post('/loans', [
        LoanController::class,
        'store',
    ])->name('loans.store');

    Route::get('/loans/{loan}', [
        LoanController::class,
        'show',
    ])->name('loans.show');

    Route::post('/loans/{loan}/repayments', [
        LoanController::class,
        'storeRepayment',
    ])->name('loans.repayments.store');

    Route::post('/logout', [
        WebAuthController::class,
        'destroy',
    ])->name('logout');
});

Route::get('/api-docs', function () {
    return view('api-docs');
});

Route::get('/openapi.yaml', function () {
    return response()->file(
        base_path('docs/openapi.yaml')
    );
});

Route::redirect('/', '/dashboard');