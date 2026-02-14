<?php

use App\Http\Controllers\App\Transaction\TransactionController;

Route::view('/admin/transactions', 'transactions.index')->name('transactions.view');
Route::get('transactions/payment-stats', [TransactionController::class, 'paymentStats'])->name('transactions.payment-stats');
Route::post('transactions/mark-as-paid', [TransactionController::class, 'markAsPaid'])->name('transactions.mark-as-paid');
Route::resource('transactions', TransactionController::class);
