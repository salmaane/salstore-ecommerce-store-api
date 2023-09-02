<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SneakerController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public Routes
Route::prefix('sneakers')->group(function() {
    Route::get('/', [SneakerController::class, 'index']);
    Route::get('/{id}', [SneakerController::class, 'show'])->whereNumber('id');
    Route::get('search/{name}', [SneakerController::class, 'search'])->whereAlpha('name');
});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// Protected Routes
Route::middleware(['auth:sanctum', 'abilities:admin'])->group(function() {
    
    Route::prefix('sneakers')->group(function () {
        Route::post('/', [SneakerController::class, 'store']);
        Route::patch('/{id}', [SneakerController::class, 'update'])->whereNumber('id');
        Route::delete('/{id}', [SneakerController::class, 'destroy'])->whereNumber('id');
        Route::post('/store-sneakers', [SneakerController::class, 'storeSneakers']);
    });

    Route::prefix('users')->group(function() {
        Route::get('/', [UsersController::class, 'index']);
        Route::get('/{id}', [UsersController::class, 'show'])->whereNumber('id');
        Route::patch('/{id}', [UsersController::class, 'update'])->whereNumber('id');
        Route::delete('/{id}', [UsersController::class, 'destroy'])->whereNumber('id');
        Route::patch('/update-profile/{id}',[UsersController::class, 'update_profile'])->whereNumber('id');
        Route::delete('/delete-profile/{id}',[UsersController::class, 'delete_profile'])->whereNumber('id');
    });

    Route::prefix('analytics')->group(function() { // For tests
        Route::get('/',[SalesController::class, 'totalSales']);
    });

});