<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::resource('/admin/univ', \App\Http\Controllers\UnivController::class)->names('univ');
    Route::resource('/admin/fakultas', \App\Http\Controllers\FakultasController::class)->names('fakultas');
    Route::resource('/admin/prodi', \App\Http\Controllers\ProdiController::class)->names('prodi');
    Route::resource('/admin/visi', \App\Http\Controllers\VisiController::class)->names('visi');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
