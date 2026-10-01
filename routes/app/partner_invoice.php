<?php

use App\Http\Controllers\App\Billing\PartnerInvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/partner-invoices', [PartnerInvoiceController::class, 'index'])->name('partner-invoices.index');
Route::get('/admin/partner-invoices/{partnerInvoice}/download', [PartnerInvoiceController::class, 'download'])
    ->name('partner-invoices.download');
Route::get('/admin/partner-invoices/{partnerInvoice}/commission-note', [PartnerInvoiceController::class, 'downloadCommissionNote'])
    ->name('partner-invoices.commission-note');