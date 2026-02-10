<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReportController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::post('products', [ProductController::class, 'store'])->name('products.store');
Route::get('products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::put('products/{id}', [ProductController::class, 'update'])->name('products.update');

Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('orders/{id}', [OrderController::class, 'show'])->name('orders.show');

Route::get('reports/top-products', [ReportController::class, 'topProducts'])->name('reports.top-products');
