<?php

use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrdersProductsController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ShoppingCartController;
use App\Http\Controllers\Api\ShoppingController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\AuthController;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () { 
    Route::apiResources([
        'orders'=> OrderController::class,
        'favorites' => FavoriteController::class,
    ]);
        
    Route::middleware('allowed')->group(function () { 
        Route::apiResource('stores', StoreController::class)->except(['index', 'show']);
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    });

    Route::apiResource('stores', StoreController::class)->only(['index', 'show']);
    Route::get('/stores/get_products/{id}',[StoreController::class, 'get_products']);

    Route::apiResource('products', ProductController::class)->only(['index', 'show']);
    Route::apiResource('shoppings', ShoppingController::class)->except('show');
    Route::get('shoppings/apply', [ShoppingController::class, 'apply']);
});

Route::controller(AuthController::class)->group(function () { 
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::get('/logout', 'logout');
});