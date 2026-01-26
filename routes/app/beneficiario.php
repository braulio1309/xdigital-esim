<?php

use App\Http\Controllers\App\Beneficiario\BeneficiarioController;
use App\Http\Controllers\App\Beneficiario\BeneficiarioDashboardController;

Route::view('/admin/beneficiarios', 'beneficiarios.index')->name('beneficiarios.view');
Route::resource('beneficiarios', BeneficiarioController::class);

// Beneficiario dashboard routes
Route::get('beneficiario/dashboard', [BeneficiarioDashboardController::class, 'index'])->name('beneficiario.dashboard');
Route::get('beneficiario/dashboard/data', [BeneficiarioDashboardController::class, 'data'])->name('beneficiario.dashboard.data');
