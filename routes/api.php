<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\SalesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

Route::apiResource('products', ProductController::class);
Route::apiResource('category', CategoryController::class);
Route::apiResource('sales', SalesController::class);
Route::apiResource('discounts', DiscountController::class);
Route::post('/send-email', [EmailController::class, 'send']);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
