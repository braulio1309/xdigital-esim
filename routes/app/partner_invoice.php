<?php

use App\Http\Controllers\App\Billing\PartnerInvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/partner-invoices', [PartnerInvoiceController::class, 'index'])->name('partner-invoices.index');
Route::patch('/admin/partner-invoices/{partnerInvoice}/payment-status', [PartnerInvoiceController::class, 'updatePaymentStatus'])
    ->name('partner-invoices.payment-status');
Route::get('/admin/partner-invoices/{partnerInvoice}/download', [PartnerInvoiceController::class, 'download'])
    ->name('partner-invoices.download');
Route::get('/admin/partner-invoices/monthly-invoice/{period}', [PartnerInvoiceController::class, 'downloadMonthlyPartnerInvoice'])
    ->name('partner-invoices.monthly-invoice');
Route::get('/admin/partner-invoices/{partnerInvoice}/commission-note', [PartnerInvoiceController::class, 'downloadCommissionNote'])
    ->name('partner-invoices.commission-note');
Route::get('/admin/partner-invoices/monthly-commissions/{period}', [PartnerInvoiceController::class, 'downloadMonthlyCommissionReport'])
    ->name('partner-invoices.monthly-commission-report');