<?php

use App\Http\Controllers\SuperAdminController;
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
Route::get('/panel', function () {
    return view('panel.index');
})->middleware(['auth', 'role:alumno'])->name('panel');

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
Route::get('/profesor', function () {
    return view('profesor.index');
})->middleware(['auth', 'role:profesor'])->name('profesor');

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
