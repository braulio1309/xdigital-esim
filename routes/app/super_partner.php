<?php

use App\Http\Controllers\App\SuperPartner\SuperPartnerController;
use App\Http\Controllers\App\SuperPartner\SuperPartnerDashboardController;

Route::view('/admin/super-partners', 'super-partners.index')->name('super-partners.view');
Route::resource('super-partners', SuperPartnerController::class);
Route::get('super-partners/{super_partner}/export-commissions', [SuperPartnerController::class, 'exportCommissions'])->name('super-partners.export-commissions');

// Super Partner dashboard routes
Route::get('super-partner/dashboard', [SuperPartnerDashboardController::class, 'index'])->name('super-partner.dashboard');
Route::get('super-partner/dashboard/data', [SuperPartnerDashboardController::class, 'data'])->name('super-partner.dashboard.data');
