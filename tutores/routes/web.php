<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\DevLoginController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\Teacher;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
        ->middleware('throttle:10,1')->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
        ->middleware('throttle:10,1')->name('auth.google.callback');
});

Route::post('/logout', [SessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');

// Acceso de desarrollo: solo existe en entorno local.
if (app()->environment('local')) {
    Route::post('/dev-login/{user}', DevLoginController::class)
        ->middleware('throttle:10,1')->name('dev.login');
}

/*
|--------------------------------------------------------------------------
| Catálogo (toda la comunidad autenticada)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('catalog.index'));
    Route::view('/guia', 'guide')->name('guide');
    Route::get('/catalogo', [CatalogController::class, 'index'])->name('catalog.index');
    Route::get('/catalogo/{faculty}', [CatalogController::class, 'faculty'])->name('catalog.faculty');
    Route::get('/catalogo/{faculty}/{career}', [CatalogController::class, 'career'])
        ->scopeBindings()->name('catalog.career');
    Route::get('/materias/{subject}', [CatalogController::class, 'subject'])->name('catalog.subject');

    Route::get('/agentes/{agent}/descargar', [DownloadController::class, 'agent'])
        ->middleware('throttle:60,1')->name('agents.download');
    Route::get('/agentes/{agent}/contenido', [DownloadController::class, 'agentContent'])
        ->middleware('throttle:60,1')->name('agents.content');
    Route::get('/planes/{studyPlan}/descargar', [DownloadController::class, 'studyPlan'])
        ->middleware('throttle:60,1')->name('study-plans.download');
});

/*
|--------------------------------------------------------------------------
| Panel del docente
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:docente|admin'])->prefix('docente')->name('teacher.')->group(function () {
    Route::get('/materias', [Teacher\SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/materias/{subject}', [Teacher\SubjectController::class, 'show'])->name('subjects.show');
    Route::post('/materias/{subject}/planes', [Teacher\StudyPlanController::class, 'store'])->name('study-plans.store');
    Route::patch('/planes/{studyPlan}/activar', [Teacher\StudyPlanController::class, 'activate'])->name('study-plans.activate');
    Route::delete('/planes/{studyPlan}', [Teacher\StudyPlanController::class, 'destroy'])->name('study-plans.destroy');
});

/*
|--------------------------------------------------------------------------
| Panel de administración
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::resource('facultades', Admin\FacultyController::class)
        ->parameters(['facultades' => 'faculty'])->except('show');

    Route::resource('carreras', Admin\CareerController::class)
        ->parameters(['carreras' => 'career']);
    Route::post('carreras/{career}/ciclos', [Admin\CycleController::class, 'store'])->name('cycles.store');
    Route::delete('ciclos/{cycle}', [Admin\CycleController::class, 'destroy'])->name('cycles.destroy');

    Route::resource('categorias', Admin\SubjectCategoryController::class)
        ->parameters(['categorias' => 'category'])->except('show');

    Route::resource('materias', Admin\SubjectController::class)
        ->parameters(['materias' => 'subject']);
    Route::post('materias/{subject}/asignaciones', [Admin\SubjectAssignmentController::class, 'store'])->name('subjects.assignments.store');
    Route::delete('materias/{subject}/asignaciones/{assignment}', [Admin\SubjectAssignmentController::class, 'destroy'])->name('subjects.assignments.destroy');

    Route::post('materias/{subject}/agentes', [Admin\AgentController::class, 'store'])->name('agents.store');
    Route::patch('agentes/{agent}/activar', [Admin\AgentController::class, 'activate'])->name('agents.activate');
    Route::delete('agentes/{agent}', [Admin\AgentController::class, 'destroy'])->name('agents.destroy');

    Route::delete('planes/{studyPlan}', [Admin\StudyPlanController::class, 'destroy'])->name('study-plans.destroy');

    Route::get('usuarios', [Admin\UserController::class, 'index'])->name('users.index');
    Route::patch('usuarios/{user}/rol', [Admin\UserController::class, 'updateRole'])->name('users.role');

    Route::get('auditoria', [Admin\AuditController::class, 'index'])->name('audits.index');
});
