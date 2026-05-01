<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\MetricsController;

/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- */

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public listings
Route::get('/listings', [ListingController::class, 'index']);
Route::get('/listings/{listing}', [ListingController::class, 'show']);

// Cities & Regions
Route::get('/cities', [CityController::class, 'index']);
Route::get('/cities/{city}', [CityController::class, 'show']);
Route::get('/cities/{city}/regions', [CityController::class, 'regions']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'updateProfile']);

    // Listings (Owner/Broker)
    Route::post('/listings', [ListingController::class, 'store']);
    Route::put('/listings/{listing}', [ListingController::class, 'update']);
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy']);
    Route::get('/my-listings', [ListingController::class, 'myListings']);

    // Favorites (Student)
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{listing}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{listing}', [FavoriteController::class, 'destroy']);
    Route::get('/favorites/{listing}/check', [FavoriteController::class, 'check']);

    // Chats
    Route::get('/chats', [ChatController::class, 'index']);
    Route::post('/chats', [ChatController::class, 'store']);
    Route::get('/chats/{chat}', [ChatController::class, 'show']);
    Route::delete('/chats/{chat}', [ChatController::class, 'destroy']);

    // Messages
    Route::post('/messages', [MessageController::class, 'store']);
    Route::post('/chats/{chat}/read', [MessageController::class, 'markAsRead']);

    // AI Assistant
    Route::post('/ai/chat', [AiController::class, 'chat']);
    Route::post('/ai/support', [AiController::class, 'supportChat']);
    Route::post('/ai/resolve', [AiController::class, 'resolveConflict']);

    // Metrics
    Route::get('/metrics', [MetricsController::class, 'index']);
});

// Prometheus Scraper (Public)
Route::get('/metrics/prometheus', [MetricsController::class, 'prometheus']);
