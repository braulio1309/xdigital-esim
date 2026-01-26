<?php

use App\Http\Controllers\App\Transaction\TransactionController;

Route::view('/admin/transactions', 'transactions.index')->name('transactions.view');
Route::resource('transactions', TransactionController::class);
