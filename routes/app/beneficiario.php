<?php

use App\Http\Controllers\App\Beneficiario\BeneficiarioController;

Route::view('/admin/beneficiarios', 'beneficiarios.index')->name('beneficiarios.view');
Route::resource('beneficiarios', BeneficiarioController::class);
