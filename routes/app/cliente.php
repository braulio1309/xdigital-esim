<?php

use App\Http\Controllers\App\Cliente\ClienteController;

Route::view('/admin/clientes', 'clientes.index')->name('clientes.view');
Route::resource('clientes', ClienteController::class);
