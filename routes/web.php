<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\App\Cliente\PlanesDisponiblesController;
use App\Http\Controllers\App\Cliente\RegistroEsimController;
use App\Http\Controllers\App\Settings\SettingsApiController;
use App\Http\Controllers\Core\Auth\User\UserPasswordController;
use App\Http\Controllers\Core\LanguageController;
use App\Http\Controllers\Core\Setting\SettingController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\InstallDemoDataController;
use App\Http\Controllers\SymlinkController;
use App\Http\Middleware\PermissionMiddleware;

/**
 * This route is only for user dashboard
 * And for some additional route
 */
//auth()->loginUsingId(1);

Route::redirect('/', 'admin/users/login');
Route::get('/get-basic-setting-data', [SettingsApiController::class, 'getBasicSettingData']);

// Ruta pública para registro de clientes eSIM (sin autenticación)
Route::get('/registro/esim/{referralCode?}', [RegistroEsimController::class, 'mostrarFormulario'])->name('registro.esim.form');
Route::post('/registro/esim', [RegistroEsimController::class, 'registrarCliente'])->name('registro.esim.store');

// Rutas públicas para planes disponibles
Route::get('/planes-disponibles', [PlanesDisponiblesController::class, 'index'])->name('planes.index');
Route::post('/planes/get-by-country', [PlanesDisponiblesController::class, 'getPlanes'])->name('planes.get');

// Rutas de API para autenticación AJAX (públicas)
Route::post('/api/auth/login', [AuthController::class, 'login'])->name('api.auth.login');
Route::post('/api/auth/register', [AuthController::class, 'register'])->name('api.auth.register');
Route::get('/api/auth/check', [AuthController::class, 'check'])->name('api.auth.check');
Route::post('/api/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');

// Rutas que requieren autenticación
Route::middleware(['auth'])->group(function () {
    Route::post('/planes/create-payment-intent', [PlanesDisponiblesController::class, 'createPaymentIntent'])->name('planes.payment.intent');
    Route::post('/planes/procesar-pago', [PlanesDisponiblesController::class, 'procesarPago'])->name('planes.pago');
});

Route::group(['middleware' => ['auth', 'authorize']], function () {
    include_route_files(__DIR__ . '/app/');
});

Route::get("doc/core/components", [DocumentationController::class, 'index']);
Route::get("doc/core/components/{component_name}", [DocumentationController::class, 'show']);

Route::get('/forget-password', [UserPasswordController::class, 'passwordReset']);
//Route::get('user/registration',[\App\Http\Controllers\Core\Auth\User\RegistrationController::class,'index']);

// Switch between the included languages
Route::get('lang/{lang}', [LanguageController::class, 'swap'])->name('language.change');

// available languages
Route::get('languages', [LanguageController::class, 'index'])->name('languages.index');

/*
 * All login related route will be go there
 * Only guest user can access this route
 */

// Add 'middleware' => 'guest' inside the group to disable auth view for authenticated users.
Route::group(['prefix' => 'user'], function () {
    include_route_files(__DIR__ . '/user/');
});

Route::group(['middleware' => 'guest', 'prefix' => 'admin/users'], function () {
    include_route_files(__DIR__ . '/login/');
});

/**
 * This route is only for brand redirection
 * And for some additional route
 */
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'authorize']], function () {
    include __DIR__ . '/additional.php';
});

Route::any('install-demo-data', [InstallDemoDataController::class, 'run'])
    ->name('install-demo-data');

Route::any('symlink', [SymlinkController::class, 'run'])
    ->name('storage.symlink');
/**
 * Backend Routes
 * Namespaces indicate folder structure
 * All your route in sub file must have a name with not more than 2 index
 * Example: brand.index or dashboard
 * See @var PermissionMiddleware for more information
 */
Route::group(['prefix' => 'admin', 'middleware' => 'admin', 'as' => 'core.'], function () {

    /*
     * (good if you want to allow more than one group in the core,
     * then limit the core features by different roles or permissions)
     *
     * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
     * These routes can not be hit if the password is expired
     */
    include_route_files(__DIR__ . '/core/');

});
