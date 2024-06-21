<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\StripeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/paypal', [PaypalController::class, 'show']);
Route::post('/paypal', [PaypalController::class, 'pay'])->name('pay.paypal');
Route::get('/paypal-success', [PaypalController::class, 'success']);
Route::get('/paypal-error', [PaypalController::class, 'error']);

Route::get('/stripe', [StripeController::class, 'show']);
Route::post('/stripe', [StripeController::class, 'pay'])->name('pay.stripe');