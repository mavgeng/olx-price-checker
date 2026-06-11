<?php

use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\VerifySubscriberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/subscriptions', SubscriptionController::class)->only(['store']);

Route::get('/subscribers/{subscriber}/verify/{hash}', VerifySubscriberController::class)
    ->name('subscribers.verify')
    ->middleware('signed');
