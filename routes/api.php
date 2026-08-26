<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\PurchaseOrderItemController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\SaleItemController;
use App\Http\Controllers\Api\StockMovementsController;
use App\Http\Controllers\Api\SupliersController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected routes (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Users
    Route::get('/users/trashed', [UserController::class, 'trashed']);
    Route::post('/users/{id}/restore', [UserController::class, 'restore']);
    Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete']);
    Route::apiResource('/users', UserController::class)->except(['store']);

    // Categories
    Route::get('/categories/trashed', [CategoriesController::class, 'trashed']);
    Route::post('/categories/{id}/restore', [CategoriesController::class, 'restore']);
    Route::delete('/categories/{id}/force-delete', [CategoriesController::class, 'forceDelete']);
    Route::apiResource('/categories', CategoriesController::class);

    // Supliers
    Route::get('/supliers/trashed', [SupliersController::class, 'trashed']);
    Route::post('/supliers/{id}/restore', [SupliersController::class, 'restore']);
    Route::delete('/supliers/{id}/force-delete', [SupliersController::class, 'forceDelete']);
    Route::apiResource('/supliers', SupliersController::class);

    // Products
    Route::get('/products/trashed', [ProductsController::class, 'trashed']);
    Route::post('/products/{id}/restore', [ProductsController::class, 'restore']);
    Route::delete('/products/{id}/force-delete', [ProductsController::class, 'forceDelete']);
    Route::apiResource('/products', ProductsController::class);

    // Stock Movements (read only)
    Route::get('/stock-movements', [StockMovementsController::class, 'index']);
    Route::get('/stock-movements/{id}', [StockMovementsController::class, 'show']);

    // Purchase Orders
    Route::apiResource('/purchase-orders', PurchaseOrderController::class);
    Route::prefix('/purchase-orders/{poId}/items')->group(function () {
        Route::get('/', [PurchaseOrderItemController::class, 'index']);
        Route::post('/', [PurchaseOrderItemController::class, 'store']);
        Route::put('/{itemId}', [PurchaseOrderItemController::class, 'update']);
        Route::patch('/{itemId}', [PurchaseOrderItemController::class, 'update']);
        Route::delete('/{itemId}', [PurchaseOrderItemController::class, 'destroy']);
    });

    // Sales
    Route::apiResource('/sales', SaleController::class);
    Route::prefix('/sales/{saleId}/items')->group(function () {
        Route::get('/', [SaleItemController::class, 'index']);
        Route::post('/', [SaleItemController::class, 'store']);
        Route::delete('/{itemId}', [SaleItemController::class, 'destroy']);
    });
});
