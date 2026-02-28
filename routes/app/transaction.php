<?php

use App\Http\Controllers\App\Transaction\TransactionController;

Route::view('/admin/transactions', 'transactions.index')->name('transactions.view');
Route::get('transactions/payment-stats', [TransactionController::class, 'paymentStats'])->name('transactions.payment-stats');
Route::get('transactions/calculate-payment-amount', [TransactionController::class, 'calculatePaymentAmount'])->name('transactions.calculate-payment-amount');
Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
Route::post('transactions/mark-as-paid', [TransactionController::class, 'markAsPaid'])->name('transactions.mark-as-paid');
Route::get('transactions/{transaction}/esim-status', [TransactionController::class, 'esimStatus'])->name('transactions.esim-status');
Route::post('transactions/{transaction}/terminate-subscription', [TransactionController::class, 'terminateSubscription'])->name('transactions.terminate-subscription');
Route::resource('transactions', TransactionController::class);
