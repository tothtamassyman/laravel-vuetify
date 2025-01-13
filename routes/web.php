<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

Route::middleware(['web'])->group(function () {
    Route::get('/reset-password/{token}', function ($token) {
        return view('auth.reset-password', ['token' => $token]);
    })->middleware('guest')->name('password.reset');
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
