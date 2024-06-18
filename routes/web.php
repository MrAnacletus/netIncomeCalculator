<?php

use Illuminate\Support\Facades\Route;


Route::get('/', ['App\Http\Controllers\CalculatorController', 'calculateNetIncome']);
Route::post('/calculate', ['App\Http\Controllers\CalculatorController', 'calculate']);
Route::get('/isapres', ['App\Http\Controllers\CalculatorController', 'getIsapres']);
