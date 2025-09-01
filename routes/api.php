<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WalletController;
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
Route::post('/contact', [ContactController::class, 'send']);

Route::apiResource('/orders', OrderController::class)->middleware('auth:sanctum');
Route::apiResource('/reviews',ReviewController::class)->middleware('auth:sanctum')->except('index');
Route::apiResource('/reviews',ReviewController::class)->only('index');
Route::post('/user/review-status', [ReviewController::class, 'updateReviewStatus'])->middleware('auth:sanctum');
Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel'])->middleware('auth:sanctum');
Route::get('admin/orders', [OrderController::class, 'adminIndex'])->middleware('auth:sanctum','admin');
Route::apiResource('/products', ProductController::class)->only( 'index', 'show');
Route::apiResource('/categories', CategoryController::class)->only('index', 'show');
Route::get('/search', [SearchController::class, 'search']);
Route::post('/login', [AuthController::class, 'login']);
Route::put('/update/profile', [AuthController::class, 'updateProfile'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/wallet', [WalletController::class, 'index']);
Route::apiResource('/wallets',WalletController::class)->middleware('auth:sanctum')->only('index');






