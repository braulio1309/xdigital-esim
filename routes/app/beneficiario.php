<?php

use App\Http\Controllers\App\Beneficiario\BeneficiarioController;
use App\Http\Controllers\App\Beneficiario\BeneficiarioDashboardController;
use App\Http\Controllers\App\Settings\BeneficiaryPlanMarginController;

Route::view('/admin/beneficiarios', 'beneficiarios.index')->name('beneficiarios.view');
Route::get('beneficiarios/seguimiento', [BeneficiarioController::class, 'followUp'])
    ->withoutMiddleware('authorize')
    ->name('beneficiarios.follow-up');
Route::get('beneficiarios/seguimiento/export', [BeneficiarioController::class, 'exportFollowUp'])
    ->withoutMiddleware('authorize')
    ->name('beneficiarios.follow-up-export');
Route::get('beneficiarios/seguimiento/{beneficiario}/clientes/export', [BeneficiarioController::class, 'exportRegisteredClients'])
    ->withoutMiddleware('authorize')
    ->name('beneficiarios.follow-up-clients-export');
Route::resource('beneficiarios', BeneficiarioController::class);
Route::post('beneficiarios/{beneficiario}/inactivate', [BeneficiarioController::class, 'inactivate'])->name('beneficiarios.inactivate');
Route::post('beneficiarios/{beneficiario}/activate', [BeneficiarioController::class, 'activate'])->name('beneficiarios.activate');
Route::get('beneficiarios/{beneficiario}/visual-commissions', [BeneficiarioController::class, 'getVisualCommissions'])->name('beneficiarios.visual-commissions.show');
Route::post('beneficiarios/{beneficiario}/visual-commissions', [BeneficiarioController::class, 'updateVisualCommissions'])->name('beneficiarios.visual-commissions.update');
Route::get('beneficiarios/{beneficiario}/export-commissions', [BeneficiarioController::class, 'exportCommissions'])->name('beneficiarios.export-commissions');

// Beneficiario dashboard routes
Route::get('beneficiario/dashboard', [BeneficiarioDashboardController::class, 'index'])->name('beneficiario.dashboard');
Route::get('beneficiario/dashboard/data', [BeneficiarioDashboardController::class, 'data'])->name('beneficiario.dashboard.data');

// Beneficiary Plan Margins routes
Route::get('beneficiario/plan-margins', [BeneficiaryPlanMarginController::class, 'index'])->name('beneficiario.plan-margins.index');
Route::post('beneficiario/plan-margins', [BeneficiaryPlanMarginController::class, 'update'])->name('beneficiario.plan-margins.update');
