<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [ApiController::class, 'login']);
Route::post('/register', [ApiController::class, 'register']);
Route::get('/services', [ApiController::class, 'getServices']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [ApiController::class, 'getProfile']);
    Route::post('/user/update', [ApiController::class, 'updateProfile']);
    Route::get('/orders', [ApiController::class, 'getOrders']);
    Route::post('/orders', [ApiController::class, 'createOrder']);
    Route::get('/orders/{id}', [ApiController::class, 'getOrderDetails']);
    Route::post('/orders/{id}/reschedule', [ApiController::class, 'rescheduleOrder']);
    Route::get('/orders/{id}/payment-token', [ApiController::class, 'getPaymentToken']);

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead']);

    // Rewards & Point Redemptions
    Route::get('/rewards', [ApiController::class, 'getRewards']);
    Route::post('/rewards/{id}/redeem', [ApiController::class, 'redeemReward']);
    Route::get('/points/transactions', [ApiController::class, 'getPointTransactions']);
    Route::get('/points/redemptions', [ApiController::class, 'getRedemptions']);
});

