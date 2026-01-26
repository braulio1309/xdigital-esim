<?php

use App\Http\Controllers\App\Cliente\ClienteController;
use App\Http\Controllers\App\Cliente\ClienteDashboardController;

Route::view('/admin/clientes', 'clientes.index')->name('clientes.view');
Route::resource('clientes', ClienteController::class);

// Cliente dashboard routes
Route::get('cliente/dashboard', [ClienteDashboardController::class, 'index'])->name('cliente.dashboard');
Route::get('cliente/dashboard/data', [ClienteDashboardController::class, 'data'])->name('cliente.dashboard.data');
