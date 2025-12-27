<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

// Registration Wizard
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showStep1'])->name('register.step1');
    Route::post('/register/step1', [RegisterController::class, 'processStep1'])->name('register.step1.process');
    Route::get('/register/step2', [RegisterController::class, 'showStep2'])->name('register.step2');
    Route::post('/register/step2', [RegisterController::class, 'processStep2'])->name('register.step2.process');
});
