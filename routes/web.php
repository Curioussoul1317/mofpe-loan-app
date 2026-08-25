<?php

use App\Http\Controllers\Auth\WebAuthController;
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

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [
        WebAuthController::class,
        'destroy',
    ])->name('logout');

});

Route::redirect('/', '/dashboard');