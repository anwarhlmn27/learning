<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CloController;
use App\Http\Controllers\BahanKajianController;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:3,1');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin', 'role.access'])->group(function () {
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
    Route::get('/admin/plo/{prodi}/export-pdf', [\App\Http\Controllers\PloController::class, 'exportMappingPdf'])->name('plo.export_pdf');
    Route::get('/admin/plo/{prodi}/manage', [\App\Http\Controllers\PloController::class, 'manage'])->name('plo.manage');
    Route::post('/admin/plo/{prodi}', [\App\Http\Controllers\PloController::class, 'store'])->name('plo.store');
    Route::put('/admin/plo/{plo}', [\App\Http\Controllers\PloController::class, 'update'])->name('plo.update');
    Route::delete('/admin/plo/{plo}', [\App\Http\Controllers\PloController::class, 'destroy'])->name('plo.destroy');

    // Bahan Kajian (BK) Routes
    Route::get('/admin/bahan-kajian', [BahanKajianController::class, 'index'])->name('bahan_kajian.index');
    Route::get('/admin/bahan-kajian/{prodi}/export-pdf', [BahanKajianController::class, 'exportMappingPdf'])->name('bahan_kajian.export_pdf');
    Route::get('/admin/bahan-kajian/{prodi}/manage', [BahanKajianController::class, 'manage'])->name('bahan_kajian.manage');
    Route::post('/admin/bahan-kajian/{prodi}', [BahanKajianController::class, 'store'])->name('bahan_kajian.store');
    Route::put('/admin/bahan-kajian/{bahanKajian}', [BahanKajianController::class, 'update'])->name('bahan_kajian.update');
    Route::delete('/admin/bahan-kajian/{bahanKajian}', [BahanKajianController::class, 'destroy'])->name('bahan_kajian.destroy');
    
    // Kategori Bahan Kajian Routes
    Route::get('/admin/bahan-kajian/{prodi}/kategori', [BahanKajianController::class, 'manageKategori'])->name('bahan_kajian.kategori.manage');
    Route::post('/admin/bahan-kajian/{prodi}/kategori', [BahanKajianController::class, 'storeKategori'])->name('bahan_kajian.kategori.store');
    Route::put('/admin/bahan-kajian/kategori/{kategori}', [BahanKajianController::class, 'updateKategori'])->name('bahan_kajian.kategori.update');
    Route::delete('/admin/bahan-kajian/kategori/{kategori}', [BahanKajianController::class, 'destroyKategori'])->name('bahan_kajian.kategori.destroy');

    // Subject (Course) Routes
    Route::resource('/admin/subjects', \App\Http\Controllers\SubjectController::class)->names('subjects');
    Route::get('/admin/subjects/prodi/{prodi}', [\App\Http\Controllers\SubjectController::class, 'prodiSubjects'])->name('subjects.prodi');
    Route::get('/admin/subjects/export-bk/{prodi}', [\App\Http\Controllers\SubjectController::class, 'exportMappingBK'])->name('subjects.export-bk');

    // CLO (CPMK) Routes

    // CLO (CPMK) Routes
    Route::get('/admin/clo', [CloController::class, 'index'])->name('clo.index');
    Route::get('/admin/clo/{subject}/manage', [CloController::class, 'manage'])->name('clo.manage');
    Route::post('/admin/clo/{subject}', [CloController::class, 'store'])->name('clo.store');
    Route::put('/admin/clo/{clo}', [CloController::class, 'update'])->name('clo.update');
    Route::delete('/admin/clo/{clo}', [CloController::class, 'destroy'])->name('clo.destroy');

    // Curriculum Routes
    Route::resource('/admin/kurikulum', \App\Http\Controllers\KurikulumController::class)->names('kurikulum');
    Route::get('/admin/kurikulum/{kurikulum}/manage', [\App\Http\Controllers\KurikulumController::class, 'manage'])->name('kurikulum.manage');
    Route::get('/admin/kurikulum/{kurikulum}/export-pdf', [\App\Http\Controllers\KurikulumController::class, 'exportPdf'])->name('kurikulum.export_pdf');
    Route::post('/admin/kurikulum/{kurikulum}/add-subject', [\App\Http\Controllers\KurikulumController::class, 'addSubject'])->name('kurikulum.add-subject');
    Route::delete('/admin/kurikulum/remove-subject/{kurikulumSubject}', [\App\Http\Controllers\KurikulumController::class, 'removeSubject'])->name('kurikulum.remove-subject');

    // Course Mapping (Curriculum Mapping) Routes
    Route::get('/admin/course-mapping', [\App\Http\Controllers\CourseMapingController::class, 'index'])->name('course_mapping.index');
    Route::get('/admin/course-mapping/{prodi}/manage', [\App\Http\Controllers\CourseMapingController::class, 'manage'])->name('course_mapping.manage');
    Route::get('/admin/course-mapping/{prodi}/export-pdf', [\App\Http\Controllers\CourseMapingController::class, 'exportPdf'])->name('course_mapping.export_pdf');
    Route::post('/admin/course-mapping/{prodi}', [\App\Http\Controllers\CourseMapingController::class, 'store'])->name('course_mapping.store');
    Route::put('/admin/course-mapping/{courseMaping}', [\App\Http\Controllers\CourseMapingController::class, 'update'])->name('course_mapping.update');
    Route::delete('/admin/course-mapping/{courseMaping}', [\App\Http\Controllers\CourseMapingController::class, 'destroy'])->name('course_mapping.destroy');

    // RPS Routes
    Route::resource('/admin/rps', \App\Http\Controllers\RpsController::class)->names('admin.rps')->except(['show', 'create']);
    Route::post('/admin/rps/{rp}/new-version', [\App\Http\Controllers\RpsController::class, 'createNewVersion'])->name('admin.rps.new_version');
    Route::post('/admin/rps/{rp}/copy', [\App\Http\Controllers\RpsController::class, 'copyToKurikulum'])->name('admin.rps.copy');
    Route::get('/admin/rps/{rp}/sessions', [\App\Http\Controllers\RpsController::class, 'manageSessions'])->name('admin.rps.sessions');
    Route::put('/admin/rps/sessions/{session}', [\App\Http\Controllers\RpsController::class, 'updateSession'])->name('admin.rps.sessions.update');
    Route::post('/admin/rps/sessions/{session}/activity', [\App\Http\Controllers\RpsController::class, 'storeActivity'])->name('admin.rps.activity.store');
    Route::delete('/admin/rps/activity/{activity}', [\App\Http\Controllers\RpsController::class, 'destroyActivity'])->name('admin.rps.activity.destroy');
    Route::get('/admin/rps/{rp}/export-pdf', [\App\Http\Controllers\RpsController::class, 'exportPdf'])->name('admin.rps.export_pdf');

    // Assessment Types Master Data
    Route::get('/admin/assessment-types', [\App\Http\Controllers\AssessmentTypeController::class, 'index'])->name('assessment_types.index');
    Route::post('/admin/assessment-types', [\App\Http\Controllers\AssessmentTypeController::class, 'store'])->name('assessment_types.store');
    Route::put('/admin/assessment-types/{assessmentType}', [\App\Http\Controllers\AssessmentTypeController::class, 'update'])->name('assessment_types.update');
    Route::delete('/admin/assessment-types/{assessmentType}', [\App\Http\Controllers\AssessmentTypeController::class, 'destroy'])->name('assessment_types.destroy');


    // User Management
    Route::resource('/admin/users', \App\Http\Controllers\UserController::class)->names('users');
    Route::post('/admin/users/import', [\App\Http\Controllers\UserController::class, 'import'])->name('users.import');
    Route::get('/admin/users-template', [\App\Http\Controllers\UserController::class, 'downloadTemplate'])->name('users.template');
    Route::patch('/admin/users/{user}/toggle-status', [\App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggle-status');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/change-password', [\App\Http\Controllers\ChangePasswordController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [\App\Http\Controllers\ChangePasswordController::class, 'updatePassword'])->name('password.update_auth');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/personal', [\App\Http\Controllers\SettingController::class, 'updatePersonal'])->name('settings.personal');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/settings/global', [\App\Http\Controllers\SettingController::class, 'updateGlobal'])
        ->name('settings.global')
        ->middleware('admin.only');
});
