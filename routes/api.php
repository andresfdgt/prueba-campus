<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('warehouse')->group(function () {
        Route::get('/', [WarehouseController::class, 'list']);
        Route::get('/{id}', [WarehouseController::class, 'show']);
        Route::post('/', [WarehouseController::class, 'store']);
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'list']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::post('/', [ProductController::class, 'store']);
    });

    Route::prefix('inventories')->group(function () {
        Route::post('/', [InventoryController::class, 'store']);
        Route::post('/move', [InventoryController::class, 'move']);
    });
});
