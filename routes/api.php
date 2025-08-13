<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/',function(){
    return response()->json(['message' => 'Welcome to the API']);
});
Route::middleware('auth:sanctum','admin')->group(function () {
    Route::apiResource('/products', ProductController::class)->only( 'store', 'update', 'destroy');
    Route::apiResource('/categories', CategoryController::class)->only('store', 'update', 'destroy');
});
Route::apiResource('/products', ProductController::class)->only( 'index', 'show');
Route::apiResource('/categories', CategoryController::class)->only('index', 'show');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');