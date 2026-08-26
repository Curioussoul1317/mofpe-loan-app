<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\LoanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RepaymentController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;

Route::post('/login', [
    AuthController::class,
    'login',
]);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [
        AuthController::class,
        'logout',
    ]);

    Route::get('/customers', [
        CustomerController::class,
        'index',
    ]);

    Route::get('/customers/{customer}', [
        CustomerController::class,
        'show',
    ]);

    Route::post('/customers', [
        CustomerController::class,
        'store',
    ])->middleware('role:administrator');

    Route::get('/loans', [
        LoanController::class,
        'index',
    ]);

    Route::post('/loans', [
        LoanController::class,
        'store',
    ])->middleware(
        'role:administrator,loan_officer'
    );

    Route::get('/loans/{loan}', [
        LoanController::class,
        'show',
    ]);

    Route::get('/loans/{loan}/repayments', [
    RepaymentController::class,
    'index',
    ]);

    Route::post('/loans/{loan}/repayments', [
        RepaymentController::class,
        'store',
    ])->middleware(
        'role:administrator,loan_officer'
    );

    Route::get('/dashboard', [
    DashboardController::class,
    'index',
    ]);

    Route::get('/currencies', [
        CurrencyController::class,
        'index',
    ]);

    Route::post('/currencies', [
        CurrencyController::class,
        'store',
    ])->middleware('role:administrator');

    Route::patch('/loans/{loan}', [
        LoanController::class,
        'update',
    ])->middleware('role:administrator');

    Route::get('/loans/{loan}/audit', [
        LoanController::class,
        'audit',
    ]);


    Route::middleware('role:administrator')->group(function () {

        Route::get('/users', [
            UserController::class,
            'index',
        ]);

        Route::post('/users', [
            UserController::class,
            'store',
        ]);

        Route::patch('/users/{user}', [
            UserController::class,
            'update',
        ]);

    });

});