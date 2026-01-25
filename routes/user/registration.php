<?php

use App\Http\Controllers\Core\Auth\UserRegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('registration', [ UserRegistrationController::class, 'index' ])
    ->name('user-registration.index');

Route::post('register', [ UserRegistrationController::class, 'register' ])
    ->name('user-registration.confirm');

Route::get('verify', [ UserRegistrationController::class, 'verify' ])
    ->name('verify-user.index')
    ->middleware('signed');


