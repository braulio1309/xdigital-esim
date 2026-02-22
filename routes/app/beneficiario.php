<?php

use App\Http\Controllers\App\Beneficiario\BeneficiarioController;
use App\Http\Controllers\App\Beneficiario\BeneficiarioDashboardController;
use App\Http\Controllers\App\Settings\BeneficiaryPlanMarginController;

Route::view('/admin/beneficiarios', 'beneficiarios.index')->name('beneficiarios.view');
Route::resource('beneficiarios', BeneficiarioController::class);
Route::get('beneficiarios/{beneficiario}/export-commissions', [BeneficiarioController::class, 'exportCommissions'])->name('beneficiarios.export-commissions');

// Beneficiario dashboard routes
Route::get('beneficiario/dashboard', [BeneficiarioDashboardController::class, 'index'])->name('beneficiario.dashboard');
Route::get('beneficiario/dashboard/data', [BeneficiarioDashboardController::class, 'data'])->name('beneficiario.dashboard.data');

// Beneficiary Plan Margins routes
Route::get('beneficiario/plan-margins', [BeneficiaryPlanMarginController::class, 'index'])->name('beneficiario.plan-margins.index');
Route::post('beneficiario/plan-margins', [BeneficiaryPlanMarginController::class, 'update'])->name('beneficiario.plan-margins.update');
