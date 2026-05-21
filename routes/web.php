<?php

use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Profesor\AttendanceController;
use App\Http\Controllers\Profesor\PerformanceController;
use App\Http\Controllers\ProductController;
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
        $products = \App\Models\Product::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
        $deletedProducts = \App\Models\Product::where('is_active', false)
            ->orderBy('updated_at', 'desc')
            ->get();
        return view('panel.index', compact('products', 'deletedProducts'));
    })->name('panel');

    // Kiosco POS API
    Route::get('/api/kiosco/categories', [\App\Http\Controllers\Panel\KioscoController::class, 'categories']);
    Route::post('/api/kiosco/sale',      [\App\Http\Controllers\Panel\KioscoController::class, 'sale']);

    // Productos API (búsqueda para autocompletado en compras)
    Route::get('/api/products/search',   [ProductController::class, 'search']);
    Route::post('/api/products',         [ProductController::class, 'quickCreate']);

    // Elaborados: carga manual de unidades y baja de sobrantes
    Route::post('/api/products/{product}/cargar-unidades', [ProductController::class, 'apiCargarUnidades']);
    Route::post('/api/products/{product}/baja-sobrantes',  [ProductController::class, 'apiBajaElaborados']);
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
| Proveedores – Lectura y escritura (cualquier usuario autenticado)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('superadmin')->group(function () {
    Route::get('/proveedores',         [SuperAdminController::class, 'getProveedores']);
    Route::post('/proveedores',        [SuperAdminController::class, 'storeProveedor']);
    Route::put('/proveedores/{id}',    [SuperAdminController::class, 'updateProveedor']);
    Route::delete('/proveedores/{id}', [SuperAdminController::class, 'destroyProveedor']);
});

/*
|--------------------------------------------------------------------------
| Compras – Lectura y escritura (cualquier usuario autenticado)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/compras',         [SuperAdminController::class, 'getCompras']);
    Route::post('/compras',        [SuperAdminController::class, 'storeCompra']);
    Route::delete('/compras/{id}', [SuperAdminController::class, 'destroyCompra']);
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

/*
|--------------------------------------------------------------------------
| Productos
|--------------------------------------------------------------------------
*/
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/',                               [ProductController::class, 'index'])->name('index');
    Route::post('/',                              [ProductController::class, 'store'])->name('store');
    Route::get('/{product}/edit',                 [ProductController::class, 'edit'])->name('edit');
    Route::put('/{product}',                      [ProductController::class, 'update'])->name('update');
    Route::delete('/{product}',                   [ProductController::class, 'destroy'])->name('destroy');
    Route::patch('/{product}/restore',            [ProductController::class, 'restore'])->name('restore');
    Route::post('/{product}/cargar-unidades',     [ProductController::class, 'cargarUnidades'])->name('cargarUnidades');
    Route::post('/{product}/baja-sobrantes',      [ProductController::class, 'bajaElaborados'])->name('bajaElaborados');
});
