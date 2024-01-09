<?php

use Framework\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| This section defines the API routes for the application. Any routes registered here will be loaded by the
| RouteServiceProvider and associated with the 'api' interface. These routes are used to handle API requests and define
| the behavior for various endpoints. Each route is defined using the Route class, allowing for the definition
| of routes with different HTTP methods, paths, and handlers, such as controller methods or closures.
|
*/

Route::guard(function() {
    Route::get('/products', [UserProductController::class, 'index']);
    Route::get('/product/{product_id}', [UserProductController::class, 'show']);
    Route::post('/product', [ProductController::class, 'store']);
    Route::post('/product/update', [UserProductController::class, 'update']);
    Route::delete('/product/{product_id}', [UserProductController::class, 'delete']);

    Route::group('/subscription', function() {
        Route::post('/create', [SubscriberController::class, 'create']);
        Route::post('/update', [SubscriberController::class, 'update']);
        Route::post('/delete', [SubscriberController::class, 'delete']);
    });

    Route::group('/auth', function() {
        Route::get('/user', [UserController::class, 'user']);
        Route::get('/logout', [UserController::class, 'logout']);
    });

    Route::get('/settings', [ScrapController::class, 'settings']);
});

Route::get('/scrap', [ScrapController::class, 'scrapAll']);
Route::get('/scrap/{productId}', [ScrapController::class, 'scrapOne']);

Route::group('/auth', function() {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
});

Route::fallback(function() {
    return response()->json([
        'status' => "Error",
        'message' => "Ups, it seems like you tried to access an endpoint that doesn't exist!",
    ], 404);
});