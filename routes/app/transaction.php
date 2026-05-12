<?php

use App\Http\Controllers\App\Transaction\TransactionController;

Route::view('/admin/transactions', 'transactions.index')->name('transactions.view');
Route::view('/admin/nomad-transactions', 'transactions.nomad')->name('transactions.nomad.view');
Route::get('transactions/nomad-debt-stats', [TransactionController::class, 'nomadDebtStats'])->name('transactions.nomad-debt-stats');
Route::get('transactions/sale-commission-total', [TransactionController::class, 'saleCommissionTotal'])->name('transactions.sale-commission-total');
Route::get('transactions/calculate-payment-amount', [TransactionController::class, 'calculatePaymentAmount'])->name('transactions.calculate-payment-amount');
Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
Route::post('transactions/mark-as-paid', [TransactionController::class, 'markAsPaid'])->name('transactions.mark-as-paid');
Route::get('transactions/{transaction}/esim-status', [TransactionController::class, 'esimStatus'])->name('transactions.esim-status');
Route::post('transactions/{transaction}/terminate-subscription', [TransactionController::class, 'terminateSubscription'])->name('transactions.terminate-subscription');
Route::post('transactions/{transaction}/recharge', [TransactionController::class, 'recharge'])->name('transactions.recharge');
Route::resource('transactions', TransactionController::class);
