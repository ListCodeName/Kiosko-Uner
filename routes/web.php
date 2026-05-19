<?php

use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Profesor\AttendanceController;
use App\Http\Controllers\Profesor\PerformanceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Landing Page (Página de inicio pública)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('landing.index');
});

/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'login'    => 'required|string',
        'password' => 'required',
    ]);

    $loginField = $request->input('login');
    $password   = $request->input('password');
    $remember   = $request->boolean('remember');

    // Determine if input is email or username
    $fieldName = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    if (Auth::attempt([$fieldName => $loginField, 'password' => $password], $remember)) {
        $request->session()->regenerate();

        // Redirect based on user role
        $user = Auth::user();
        return redirect()->intended($user->panelRoute());
    }

    return back()->withErrors([
        'login' => 'Las credenciales no coinciden con nuestros registros.',
    ])->onlyInput('login');
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Panel Alumno (requiere autenticación + rol alumno)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:alumno'])->prefix('panel')->group(function () {
    Route::get('/', function () {
        return view('panel.index');
    })->name('panel');

    // Kiosco POS API
    Route::get('/api/kiosco/categories', [\App\Http\Controllers\Panel\KioscoController::class, 'categories']);
    Route::post('/api/kiosco/sale',      [\App\Http\Controllers\Panel\KioscoController::class, 'sale']);
});

/*
|--------------------------------------------------------------------------
| Panel Super Administrador
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/',                          [SuperAdminController::class, 'index']);
    Route::get('/personnel',                 [SuperAdminController::class, 'getPersonnel']);
    Route::post('/personnel',                [SuperAdminController::class, 'storePersonnel']);
    Route::put('/personnel/{id}',            [SuperAdminController::class, 'updatePersonnel']);
    Route::put('/personnel/{id}/role',       [SuperAdminController::class, 'updateRole']);
    Route::put('/personnel/{id}/password',   [SuperAdminController::class, 'updatePassword']);
    Route::delete('/personnel/{id}',         [SuperAdminController::class, 'destroyPersonnel']);
});

/*
|--------------------------------------------------------------------------
| Panel Profesor (placeholder)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:profesor'])->prefix('profesor')->group(function () {
    Route::get('/', function () {
        return view('profesor.index');
    })->name('profesor');

    // Groups API
    Route::get('/api/groups', [\App\Http\Controllers\Profesor\GroupController::class, 'index']);
    Route::post('/api/groups', [\App\Http\Controllers\Profesor\GroupController::class, 'store']);
    Route::put('/api/groups/{group}', [\App\Http\Controllers\Profesor\GroupController::class, 'update']);
    Route::delete('/api/groups/{group}', [\App\Http\Controllers\Profesor\GroupController::class, 'destroy']);
    Route::post('/api/groups/{group}/members', [\App\Http\Controllers\Profesor\GroupController::class, 'addStudent']);
    Route::delete('/api/groups/{group}/members/{user}', [\App\Http\Controllers\Profesor\GroupController::class, 'removeStudent']);
    Route::get('/api/students', [\App\Http\Controllers\Profesor\GroupController::class, 'searchStudents']);

    // Attendance API
    Route::get('/api/attendance',  [AttendanceController::class, 'index']);
    Route::post('/api/attendance', [\App\Http\Controllers\Profesor\AttendanceController::class, 'upsert']);

    // Users API (Módulo de Usuarios)
    Route::get('/api/users', [\App\Http\Controllers\Profesor\UserController::class, 'index']);
    Route::post('/api/users', [\App\Http\Controllers\Profesor\UserController::class, 'store']);
    Route::put('/api/users/{id}', [\App\Http\Controllers\Profesor\UserController::class, 'update']);
    Route::put('/api/users/{id}/password', [\App\Http\Controllers\Profesor\UserController::class, 'updatePassword']);
    Route::delete('/api/users/{id}', [\App\Http\Controllers\Profesor\UserController::class, 'destroy']);

    // Performance API
    Route::get('/api/performance/individual',        [PerformanceController::class, 'individual']);
    Route::get('/api/performance/attendance-detail', [PerformanceController::class, 'attendanceDetail']);
    Route::get('/api/performance/activity-detail',   [PerformanceController::class, 'activityDetail']);
});


/*
|--------------------------------------------------------------------------
| Panel Directivo (placeholder)
|--------------------------------------------------------------------------
*/
Route::get('/directivo', function () {
    return view('directivo.index');
})->middleware(['auth', 'role:directivo'])->name('directivo');

/*
|--------------------------------------------------------------------------
| Showcase (UI showcase)
|--------------------------------------------------------------------------
*/
Route::get('/showcase', function () {
    return view('showcase.index');
});
