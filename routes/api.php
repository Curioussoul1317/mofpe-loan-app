<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\LoanController;
use Illuminate\Support\Facades\Route;

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
});