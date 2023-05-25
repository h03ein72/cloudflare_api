<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CloudflareController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/getDomains', [CloudflareController::class, 'getDomains']);
Route::post('/getDns', [CloudflareController::class, 'getDns']);
Route::post('/setDns', [CloudflareController::class, 'setDns']);
Route::post('/editDns', [CloudflareController::class, 'editDns']);
Route::post('/deleteDns', [CloudflareController::class, 'deleteDns']);
