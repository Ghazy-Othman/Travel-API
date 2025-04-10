<?php

use App\Http\Controllers\Api\V1\Admin\TourController as AdminTourController;
use App\Http\Controllers\Api\V1\Admin\TravelController as AdminTravelController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\TourController;
use App\Http\Controllers\Api\V1\TravelController;
use Illuminate\Support\Facades\Route;

Route::get('travels', [TravelController::class, 'index']);

Route::get('travels/{travel:slug}/tours', [TourController::class, 'index']);

Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {

    Route::middleware('role:admin')->group(function () {
        Route::post('travels', [AdminTravelController::class, 'store']);
        Route::post('travels/{travel}/tours', [AdminTourController::class, 'store']);
    });

    // No need to add another role middleware because we have just two roles
    Route::put('travels/{travel}', [AdminTravelController::class, 'update']);
});

Route::prefix('auth')->group(function () {

    Route::post('/login', LoginController::class);
});
