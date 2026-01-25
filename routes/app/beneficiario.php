<?php

use App\Http\Controllers\App\Beneficiario\BeneficiarioController;

Route::view('/beneficiarios', 'beneficiarios.index')->name('beneficiarios.view');
Route::resource('beneficiarios', BeneficiarioController::class);
