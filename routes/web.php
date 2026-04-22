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

    // Graduate Profile (GP) Routes
    Route::get('/admin/gp', [\App\Http\Controllers\GpController::class, 'index'])->name('gp.index');
    Route::get('/admin/gp/{prodi}/manage', [\App\Http\Controllers\GpController::class, 'manage'])->name('gp.manage');
    Route::post('/admin/gp/{prodi}/profile', [\App\Http\Controllers\GpController::class, 'storeProfile'])->name('gp.profile.store');
    Route::put('/admin/gp/profile/{gp}', [\App\Http\Controllers\GpController::class, 'updateProfile'])->name('gp.profile.update');
    Route::delete('/admin/gp/profile/{gp}', [\App\Http\Controllers\GpController::class, 'destroyProfile'])->name('gp.profile.destroy');
    Route::post('/admin/gp/{prodi}/attachment', [\App\Http\Controllers\GpController::class, 'storeAttachment'])->name('gp.attachment.store');
    Route::delete('/admin/gp/attachment/{attachment}', [\App\Http\Controllers\GpController::class, 'destroyAttachment'])->name('gp.attachment.destroy');

    // Program Learning Outcomes (PLO/CPL) Routes
    Route::get('/admin/plo', [\App\Http\Controllers\PloController::class, 'index'])->name('plo.index');
    Route::get('/admin/plo/{prodi}/manage', [\App\Http\Controllers\PloController::class, 'manage'])->name('plo.manage');
    Route::post('/admin/plo/{prodi}', [\App\Http\Controllers\PloController::class, 'store'])->name('plo.store');
    Route::put('/admin/plo/{plo}', [\App\Http\Controllers\PloController::class, 'update'])->name('plo.update');
    Route::delete('/admin/plo/{plo}', [\App\Http\Controllers\PloController::class, 'destroy'])->name('plo.destroy');

    // Subject (Course) Routes
    Route::resource('/admin/subjects', \App\Http\Controllers\SubjectController::class)->names('subjects');

    // Curriculum Routes
    Route::resource('/admin/kurikulum', \App\Http\Controllers\KurikulumController::class)->names('kurikulum');
    Route::get('/admin/kurikulum/{kurikulum}/manage', [\App\Http\Controllers\KurikulumController::class, 'manage'])->name('kurikulum.manage');
    Route::post('/admin/kurikulum/{kurikulum}/add-subject', [\App\Http\Controllers\KurikulumController::class, 'addSubject'])->name('kurikulum.add-subject');
    Route::delete('/admin/kurikulum/remove-subject/{kurikulumSubject}', [\App\Http\Controllers\KurikulumController::class, 'removeSubject'])->name('kurikulum.remove-subject');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
