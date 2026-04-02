<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PizzaController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\OrderController;
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Pizzas públicas
Route::get('pizzas', [PizzaController::class, 'index']); 

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Rutas admin: middleware de rol
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('ingredients', IngredientController::class);
        Route::apiResource('pizzas', PizzaController::class)->except(['index']); // admin puede crear/editar
        Route::get('orders', [OrderController::class, 'index']); 
    });

    // Rutas de usuario
    Route::post('orders/checkout', [OrderController::class, 'checkout']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']); 
    Route::get('my-orders', [OrderController::class, 'myOrders']);
});