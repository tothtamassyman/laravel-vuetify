<?php

use App\Http\Controllers\Auth\AbilitiesController;
use App\Http\Controllers\Auth\GroupController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PolicyController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupStatsController;
use App\Http\Controllers\LanguageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['sanctum.stateful', 'auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/groups/{id}/add-user', [GroupController::class, 'addUser']);
    Route::post('/groups/{id}/remove-user', [GroupController::class, 'removeUser']);
    Route::post('/groups/switch', [GroupController::class, 'switchGroup']);

    Route::get('/group-stats', [GroupStatsController::class, 'index']);

    Route::apiResource('/groups', GroupController::class);
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/roles', RoleController::class);
    Route::apiResource('/permissions', PermissionController::class);
});

Route::get('/abilities', [AbilitiesController::class, 'index']);

Route::post('/login', [AuthController::class, 'login']);

Route::get('/locales', [LanguageController::class, 'getLocales'])->name('locales');
Route::get('/current-locale', [LanguageController::class, 'getCurrentLocale']);
Route::get('/set-locale/{locale}', [LanguageController::class, 'setLocale']);

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');

Route::get('/policies', [PolicyController::class, 'index']);
Route::middleware('auth')->put('/password', [PasswordController::class, 'update'])->name('password.change');
