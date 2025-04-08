<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductMasterController;

Route::middleware('api')->group(function () {
    Route::post('/products-list', [ProductMasterController::class, 'index']);
    Route::post('/store-products', [ProductMasterController::class, 'store']);
    Route::post('/update-products', [ProductMasterController::class, 'update']);
    Route::post('/delete-products', [ProductMasterController::class, 'destroy']);
});
