<?php

use App\Http\Controllers\App\PaymentHistory\PaymentHistoryController;

Route::view('/admin/payment-histories', 'payment-histories.index')->name('payment-histories.view');
Route::get('payment-histories/{paymentHistory}/download-support', [PaymentHistoryController::class, 'downloadSupport'])->name('payment-histories.download-support');
Route::post('payment-histories/{paymentHistory}/cancel', [PaymentHistoryController::class, 'cancel'])->name('payment-histories.cancel');
Route::resource('payment-histories', PaymentHistoryController::class)->only(['index', 'show', 'destroy']);
