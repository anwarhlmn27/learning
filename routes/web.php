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
    Route::get('/obe/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');

    // RBAC Settings Matrix
    Route::get('/obe/rbac', [\App\Http\Controllers\RbacController::class, 'index'])->name('rbac.index');
    Route::post('/obe/rbac/toggle', [\App\Http\Controllers\RbacController::class, 'togglePermission'])->name('rbac.toggle');

    Route::resource('/obe/univ', \App\Http\Controllers\UnivController::class)->names('univ');
    Route::resource('/obe/fakultas', \App\Http\Controllers\FakultasController::class)->names('fakultas');
    Route::resource('/obe/prodi', \App\Http\Controllers\ProdiController::class)->names('prodi');
    Route::resource('/obe/visi', \App\Http\Controllers\VisiController::class)->names('visi');

    // Graduate Profile (GP) Routes
    Route::get('/obe/gp', [\App\Http\Controllers\GpController::class, 'index'])->name('gp.index');
    Route::get('/obe/gp/{prodi}/manage', [\App\Http\Controllers\GpController::class, 'manage'])->name('gp.manage');
    Route::post('/obe/gp/{prodi}/profile', [\App\Http\Controllers\GpController::class, 'storeProfile'])->name('gp.profile.store');
    Route::put('/obe/gp/profile/{gp}', [\App\Http\Controllers\GpController::class, 'updateProfile'])->name('gp.profile.update');
    Route::delete('/obe/gp/profile/{gp}', [\App\Http\Controllers\GpController::class, 'destroyProfile'])->name('gp.profile.destroy');
    Route::post('/obe/gp/{prodi}/attachment', [\App\Http\Controllers\GpController::class, 'storeAttachment'])->name('gp.attachment.store');
    Route::delete('/obe/gp/attachment/{attachment}', [\App\Http\Controllers\GpController::class, 'destroyAttachment'])->name('gp.attachment.destroy');

    // Program Learning Outcomes (PLO/CPL) Routes
    Route::get('/obe/plo', [\App\Http\Controllers\PloController::class, 'index'])->name('plo.index');
    Route::get('/obe/plo/{prodi}/export-pdf', [\App\Http\Controllers\PloController::class, 'exportMappingPdf'])->name('plo.export_pdf');
    Route::get('/obe/plo/{prodi}/manage', [\App\Http\Controllers\PloController::class, 'manage'])->name('plo.manage');
    Route::post('/obe/plo/{prodi}', [\App\Http\Controllers\PloController::class, 'store'])->name('plo.store');
    Route::put('/obe/plo/{plo}', [\App\Http\Controllers\PloController::class, 'update'])->name('plo.update');
    Route::delete('/obe/plo/{plo}', [\App\Http\Controllers\PloController::class, 'destroy'])->name('plo.destroy');
    
    Route::get('/obe/plo/detail/{plo}', [\App\Http\Controllers\PloController::class, 'show'])->name('plo.show');
    Route::post('/obe/plo/{plo}/terms', [\App\Http\Controllers\PloController::class, 'storeTerm'])->name('plo.terms.store');
    Route::put('/obe/plo/terms/{term}', [\App\Http\Controllers\PloController::class, 'updateTerm'])->name('plo.terms.update');
    Route::delete('/obe/plo/terms/{term}', [\App\Http\Controllers\PloController::class, 'destroyTerm'])->name('plo.terms.destroy');
    Route::post('/obe/plo/{plo}/indicators', [\App\Http\Controllers\PloController::class, 'storeIndicator'])->name('plo.indicators.store');
    Route::put('/obe/plo/indicators/{indicator}', [\App\Http\Controllers\PloController::class, 'updateIndicator'])->name('plo.indicators.update');
    Route::delete('/obe/plo/indicators/{indicator}', [\App\Http\Controllers\PloController::class, 'destroyIndicator'])->name('plo.indicators.destroy');

    // Bahan Kajian (BK) Routes
    Route::get('/obe/bahan-kajian', [BahanKajianController::class, 'index'])->name('bahan_kajian.index');
    Route::get('/obe/bahan-kajian/{prodi}/export-pdf', [BahanKajianController::class, 'exportMappingPdf'])->name('bahan_kajian.export_pdf');
    Route::get('/obe/bahan-kajian/{prodi}/manage', [BahanKajianController::class, 'manage'])->name('bahan_kajian.manage');
    Route::post('/obe/bahan-kajian/{prodi}', [BahanKajianController::class, 'store'])->name('bahan_kajian.store');
    Route::put('/obe/bahan-kajian/{bahanKajian}', [BahanKajianController::class, 'update'])->name('bahan_kajian.update');
    Route::delete('/obe/bahan-kajian/{bahanKajian}', [BahanKajianController::class, 'destroy'])->name('bahan_kajian.destroy');
    
    // Kategori Bahan Kajian Routes
    Route::get('/obe/bahan-kajian/{prodi}/kategori', [BahanKajianController::class, 'manageKategori'])->name('bahan_kajian.kategori.manage');
    Route::post('/obe/bahan-kajian/{prodi}/kategori', [BahanKajianController::class, 'storeKategori'])->name('bahan_kajian.kategori.store');
    Route::put('/obe/bahan-kajian/kategori/{kategori}', [BahanKajianController::class, 'updateKategori'])->name('bahan_kajian.kategori.update');
    Route::delete('/obe/bahan-kajian/kategori/{kategori}', [BahanKajianController::class, 'destroyKategori'])->name('bahan_kajian.kategori.destroy');

    // Subject (Course) Routes
    Route::get('/obe/subjects/prodi-data', [\App\Http\Controllers\SubjectController::class, 'getProdiData'])->name('subjects.prodi_data');
    Route::get('/obe/subjects/prodi/{prodi}', [\App\Http\Controllers\SubjectController::class, 'prodiSubjects'])->name('subjects.prodi');
    Route::get('/obe/subjects/export-bk/{prodi}', [\App\Http\Controllers\SubjectController::class, 'exportMappingBK'])->name('subjects.export-bk');
    Route::get('/obe/subjects/export-plo/{prodi}', [\App\Http\Controllers\SubjectController::class, 'exportMappingPLO'])->name('subjects.export-plo');
    Route::resource('/obe/subjects', \App\Http\Controllers\SubjectController::class)->names('subjects');

    // CLO (CPMK) Routes

    // CLO (CPMK) Routes
    Route::get('/obe/clo', [CloController::class, 'index'])->name('clo.index');
    Route::get('/obe/clo/prodi/{prodi}', [CloController::class, 'prodiSubjects'])->name('clo.prodi');
    Route::get('/obe/clo/{subject}/manage', [CloController::class, 'manage'])->name('clo.manage');
    Route::post('/obe/clo/{subject}', [CloController::class, 'store'])->name('clo.store');
    Route::put('/obe/clo/{clo}', [CloController::class, 'update'])->name('clo.update');
    Route::delete('/obe/clo/{clo}', [CloController::class, 'destroy'])->name('clo.destroy');

    // Curriculum Routes
    Route::resource('/obe/kurikulum', \App\Http\Controllers\KurikulumController::class)->names('kurikulum');
    Route::get('/obe/kurikulum/{kurikulum}/manage', [\App\Http\Controllers\KurikulumController::class, 'manage'])->name('kurikulum.manage');
    Route::get('/obe/kurikulum/{kurikulum}/export-pdf', [\App\Http\Controllers\KurikulumController::class, 'exportPdf'])->name('kurikulum.export_pdf');
    Route::post('/obe/kurikulum/{kurikulum}/add-subject', [\App\Http\Controllers\KurikulumController::class, 'addSubject'])->name('kurikulum.add-subject');
    Route::delete('/obe/kurikulum/remove-subject/{kurikulumSubject}', [\App\Http\Controllers\KurikulumController::class, 'removeSubject'])->name('kurikulum.remove-subject');

    // RPS Routes
    Route::get('/obe/rps/prodi/{prodi}', [\App\Http\Controllers\RpsController::class, 'prodiRps'])->name('admin.rps.prodi');
    Route::resource('/obe/rps', \App\Http\Controllers\RpsController::class)->names('admin.rps')->except(['show', 'create']);
    Route::post('/obe/rps/{rp}/new-version', [\App\Http\Controllers\RpsController::class, 'createNewVersion'])->name('admin.rps.new_version');
    Route::post('/obe/rps/{rp}/copy', [\App\Http\Controllers\RpsController::class, 'copyToKurikulum'])->name('admin.rps.copy');
    Route::post('/obe/rps/{rp}/clone-to-prodi', [\App\Http\Controllers\RpsController::class, 'cloneToProdi'])->name('admin.rps.clone_to_prodi');
    Route::get('/obe/rps/{rp}/sessions', [\App\Http\Controllers\RpsController::class, 'manageSessions'])->name('admin.rps.sessions');
    Route::put('/obe/rps/sessions/{session}', [\App\Http\Controllers\RpsController::class, 'updateSession'])->name('admin.rps.sessions.update');
    Route::post('/obe/rps/sessions/{session}/activity', [\App\Http\Controllers\RpsController::class, 'storeActivity'])->name('admin.rps.activity.store');
    Route::delete('/obe/rps/activity/{activity}', [\App\Http\Controllers\RpsController::class, 'destroyActivity'])->name('admin.rps.activity.destroy');
    Route::get('/obe/rps/{rp}/export-pdf', [\App\Http\Controllers\RpsController::class, 'exportPdf'])->name('admin.rps.export_pdf');

    // Assessment Types Master Data
    Route::get('/obe/assessment-types', [\App\Http\Controllers\AssessmentTypeController::class, 'index'])->name('assessment_types.index');
    Route::post('/obe/assessment-types', [\App\Http\Controllers\AssessmentTypeController::class, 'store'])->name('assessment_types.store');
    Route::put('/obe/assessment-types/{assessmentType}', [\App\Http\Controllers\AssessmentTypeController::class, 'update'])->name('assessment_types.update');
    Route::delete('/obe/assessment-types/{assessmentType}', [\App\Http\Controllers\AssessmentTypeController::class, 'destroy'])->name('assessment_types.destroy');

    // System Logs
    Route::get('/obe/logs', [\App\Http\Controllers\LogController::class, 'index'])->name('logs.index');
    Route::delete('/obe/logs/clear', [\App\Http\Controllers\LogController::class, 'clear'])->name('logs.clear');

    // OBE Analytics
    Route::get('/obe/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/obe/analytics/api/prodis', [\App\Http\Controllers\AnalyticsController::class, 'getProdis'])->name('analytics.api.prodis');
    Route::get('/obe/analytics/api/angkatans', [\App\Http\Controllers\AnalyticsController::class, 'getAngkatans'])->name('analytics.api.angkatans');
    Route::get('/obe/analytics/api/students', [\App\Http\Controllers\AnalyticsController::class, 'getStudents'])->name('analytics.api.students');



    // User Management
    Route::resource('/obe/users', \App\Http\Controllers\UserController::class)->names('users');
    Route::post('/obe/users/import', [\App\Http\Controllers\UserController::class, 'import'])->name('users.import');
    Route::get('/obe/users-template', [\App\Http\Controllers\UserController::class, 'downloadTemplate'])->name('users.template');
    Route::patch('/obe/users/{user}/toggle-status', [\App\Http\Controllers\UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Student Management (Moved to LMS)

});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\LmsController::class, 'dashboard'])->name('dashboard');

    Route::get('/change-password', [\App\Http\Controllers\ChangePasswordController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [\App\Http\Controllers\ChangePasswordController::class, 'updatePassword'])->name('password.update_auth');

    // LMS Specific Change Password & Settings
    Route::get('/lms/change-password', [\App\Http\Controllers\ChangePasswordController::class, 'showLmsChangePasswordForm'])->name('lms.password.change');
    Route::post('/lms/change-password', [\App\Http\Controllers\ChangePasswordController::class, 'updateLmsPassword'])->name('lms.password.update_auth');
    Route::get('/lms/settings', [\App\Http\Controllers\SettingController::class, 'lmsIndex'])->name('lms.settings.index');
    Route::post('/lms/settings/personal', [\App\Http\Controllers\SettingController::class, 'updateLmsPersonal'])->name('lms.settings.personal');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/personal', [\App\Http\Controllers\SettingController::class, 'updatePersonal'])->name('settings.personal');

    // Dosen & Mahasiswa Management (LMS Side)
    Route::resource('/dosen', \App\Http\Controllers\DosenController::class)->names('dosen');
    Route::post('/dosen/import', [\App\Http\Controllers\DosenController::class, 'import'])->name('dosen.import');
    Route::get('/dosen-template', [\App\Http\Controllers\DosenController::class, 'downloadTemplate'])->name('dosen.template');

    Route::post('/mahasiswa/bulk-frozen', [\App\Http\Controllers\StudentController::class, 'bulkUpdateFrozen'])->name('mahasiswa.bulk_update_frozen');
    Route::resource('/mahasiswa', \App\Http\Controllers\StudentController::class)->parameters(['mahasiswa' => 'student'])->names('mahasiswa');
    Route::post('/mahasiswa/import', [\App\Http\Controllers\StudentController::class, 'import'])->name('mahasiswa.import');
    Route::get('/mahasiswa-template', [\App\Http\Controllers\StudentController::class, 'downloadTemplate'])->name('mahasiswa.template');

    // Class Enrollment & Management
    // Class Enrollment & Management (Main LMS Routes with /lms prefix)
    Route::prefix('lms')->group(function() {
        Route::resource('classes', \App\Http\Controllers\ClassRoomController::class)->names('classes');
        Route::get('/classes/{class}/edit', [\App\Http\Controllers\ClassRoomController::class, 'edit'])->name('lms.classes.edit');
        Route::match(['put', 'post'], '/classes/{class}', [\App\Http\Controllers\ClassRoomController::class, 'update'])->name('lms.classes.update');
        Route::get('/classes/{class}/export-grades', [\App\Http\Controllers\ClassRoomController::class, 'exportGrades'])->name('classes.export_grades');
        Route::post('/classes/{class}/generate-lms', [\App\Http\Controllers\ClassRoomController::class, 'generateLmsFromRps'])->name('classes.generate_lms');
        Route::post('/classes/{class}/enroll', [\App\Http\Controllers\ClassRoomController::class, 'enroll'])->name('classes.enroll');
        Route::post('/classes/{class}/import-students', [\App\Http\Controllers\ClassRoomController::class, 'importStudents'])->name('classes.import_students');
        Route::get('/classes-template', [\App\Http\Controllers\ClassRoomController::class, 'downloadTemplate'])->name('classes.template');
        Route::delete('/classes/{class}/unenroll/{enrollment}', [\App\Http\Controllers\ClassRoomController::class, 'unenroll'])->name('classes.unenroll');
        Route::post('/classes/{class}/material', [\App\Http\Controllers\ClassRoomController::class, 'storeMaterial'])->name('classes.store_material');
        Route::match(['put', 'post'], '/classes/{class}/material/{material}', [\App\Http\Controllers\ClassRoomController::class, 'updateMaterial'])->name('classes.update_material');
        Route::get('/classes/{class}/material/{material}/download', [\App\Http\Controllers\ClassRoomController::class, 'downloadMaterial'])->name('classes.download_material');
        Route::post('/classes/{class}/assignment', [\App\Http\Controllers\ClassRoomController::class, 'storeAssignment'])->name('classes.store_assignment');
        Route::match(['put', 'post'], '/classes/{class}/assignment/{assignment}', [\App\Http\Controllers\ClassRoomController::class, 'updateAssignment'])->name('classes.update_assignment');
        Route::post('/classes/{class}/forum', [\App\Http\Controllers\ClassRoomController::class, 'storeForum'])->name('classes.store_forum');
        Route::match(['put', 'post'], '/classes/{class}/forum/{forum}', [\App\Http\Controllers\ClassRoomController::class, 'updateForum'])->name('classes.update_forum');
        Route::get('/classes/{class}/forums/{forum}', [\App\Http\Controllers\ForumController::class, 'show'])->name('classes.forums.show');
        Route::post('/classes/{class}/forums/{forum}/posts', [\App\Http\Controllers\ForumController::class, 'storePost'])->name('classes.forums.store_post');
        Route::delete('/classes/{class}/forums/{forum}/posts/{post}', [\App\Http\Controllers\ForumController::class, 'destroyPost'])->name('classes.forums.destroy_post');
        Route::post('/classes/{class}/quiz', [\App\Http\Controllers\ClassRoomController::class, 'storeQuiz'])->name('classes.store_quiz');
        Route::delete('/classes/{class}/topics/{topic}', [\App\Http\Controllers\ClassRoomController::class, 'destroyTopic'])->name('classes.destroy_topic');
        Route::get('/classes/{class}/quiz/{quiz}', [\App\Http\Controllers\QuizController::class, 'take'])->name('classes.take_quiz');
        Route::post('/classes/{class}/quiz/{quiz}/submit', [\App\Http\Controllers\QuizController::class, 'submit'])->name('classes.submit_quiz');
        Route::post('/classes/{class}/add-staff', [\App\Http\Controllers\ClassRoomController::class, 'addStaff'])->name('classes.add_staff');
        Route::delete('/classes/{class}/remove-staff/{user}', [\App\Http\Controllers\ClassRoomController::class, 'removeStaff'])->name('classes.remove_staff');
        Route::post('/classes/{class}/archive', [\App\Http\Controllers\ClassRoomController::class, 'archive'])->name('classes.archive');
        Route::get('/archived-classes', [\App\Http\Controllers\ClassRoomController::class, 'archivedIndex'])->name('classes.archived');
        Route::post('/classes/{class}/sessions/{session_number}/rate', [\App\Http\Controllers\SessionRatingController::class, 'store'])->name('classes.rate_session');
        Route::get('/classes/{class}/available-students', [\App\Http\Controllers\ClassRoomController::class, 'getAvailableStudents'])->name('classes.available_students');
    });

    // Fallback routes for direct /classes access without /lms prefix
    Route::resource('/classes', \App\Http\Controllers\ClassRoomController::class)->names('fallback_classes');
    Route::match(['put', 'post'], '/classes/{class}', [\App\Http\Controllers\ClassRoomController::class, 'update']);
    Route::get('/classes/{class}/export-grades', [\App\Http\Controllers\ClassRoomController::class, 'exportGrades']);
    Route::post('/classes/{class}/generate-lms', [\App\Http\Controllers\ClassRoomController::class, 'generateLmsFromRps']);
    Route::post('/classes/{class}/enroll', [\App\Http\Controllers\ClassRoomController::class, 'enroll']);
    Route::post('/classes/{class}/import-students', [\App\Http\Controllers\ClassRoomController::class, 'importStudents']);
    Route::delete('/classes/{class}/unenroll/{enrollment}', [\App\Http\Controllers\ClassRoomController::class, 'unenroll']);
    Route::post('/classes/{class}/material', [\App\Http\Controllers\ClassRoomController::class, 'storeMaterial']);
    Route::match(['put', 'post'], '/classes/{class}/material/{material}', [\App\Http\Controllers\ClassRoomController::class, 'updateMaterial']);
    Route::get('/classes/{class}/material/{material}/download', [\App\Http\Controllers\ClassRoomController::class, 'downloadMaterial']);
    Route::post('/classes/{class}/assignment', [\App\Http\Controllers\ClassRoomController::class, 'storeAssignment']);
    Route::match(['put', 'post'], '/classes/{class}/assignment/{assignment}', [\App\Http\Controllers\ClassRoomController::class, 'updateAssignment']);
    Route::post('/classes/{class}/forum', [\App\Http\Controllers\ClassRoomController::class, 'storeForum']);
    Route::match(['put', 'post'], '/classes/{class}/forum/{forum}', [\App\Http\Controllers\ClassRoomController::class, 'updateForum']);
    Route::get('/classes/{class}/forums/{forum}', [\App\Http\Controllers\ForumController::class, 'show']);
    Route::post('/classes/{class}/forums/{forum}/posts', [\App\Http\Controllers\ForumController::class, 'storePost']);
    Route::delete('/classes/{class}/forums/{forum}/posts/{post}', [\App\Http\Controllers\ForumController::class, 'destroyPost']);
    Route::post('/classes/{class}/quiz', [\App\Http\Controllers\ClassRoomController::class, 'storeQuiz']);
    Route::delete('/classes/{class}/topics/{topic}', [\App\Http\Controllers\ClassRoomController::class, 'destroyTopic']);
    Route::get('/classes/{class}/quiz/{quiz}', [\App\Http\Controllers\QuizController::class, 'take']);
    Route::post('/classes/{class}/quiz/{quiz}/submit', [\App\Http\Controllers\QuizController::class, 'submit']);
    Route::post('/classes/{class}/add-staff', [\App\Http\Controllers\ClassRoomController::class, 'addStaff']);
    Route::delete('/classes/{class}/remove-staff/{user}', [\App\Http\Controllers\ClassRoomController::class, 'removeStaff']);
    Route::post('/classes/{class}/archive', [\App\Http\Controllers\ClassRoomController::class, 'archive']);
    Route::get('/archived-classes', [\App\Http\Controllers\ClassRoomController::class, 'archivedIndex']);
    Route::post('/classes/{class}/sessions/{session_number}/rate', [\App\Http\Controllers\SessionRatingController::class, 'store']);
    Route::get('/classes/{class}/available-students', [\App\Http\Controllers\ClassRoomController::class, 'getAvailableStudents']);
});


Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/settings/global', [\App\Http\Controllers\SettingController::class, 'updateGlobal'])
        ->name('settings.global')
        ->middleware('admin.only');
});
