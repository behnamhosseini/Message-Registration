<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [AuthController::class,'login'])->middleware('throttle:30,1');
Route::post('auth/logout', [AuthController::class,'logout']);
