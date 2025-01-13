<?php

use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LanguageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['sanctum.stateful', 'auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('groups/{id}/add-user', [GroupController::class, 'addUser']);
    Route::post('groups/{id}/remove-user', [GroupController::class, 'removeUser']);
    Route::post('groups/switch', [GroupController::class, 'switchGroup']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::get('/locales', [LanguageController::class, 'getLocales'])->name('locales');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
Route::middleware('auth')->put('/password', [PasswordController::class, 'update'])->name('password.change');
