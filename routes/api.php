<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PricingGroupController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Pricing Group Routes
Route::middleware('auth:sanctum')->group(function () {
    // Pricing Groups
    Route::get('/properties/{propertyId}/pricing-groups', [PricingGroupController::class, 'index']);
    Route::post('/properties/{propertyId}/pricing-groups', [PricingGroupController::class, 'store']);
    Route::get('/properties/{propertyId}/pricing-groups/{groupId}', [PricingGroupController::class, 'show']);
    Route::put('/properties/{propertyId}/pricing-groups/{groupId}', [PricingGroupController::class, 'update']);
    Route::delete('/properties/{propertyId}/pricing-groups/{groupId}', [PricingGroupController::class, 'destroy']);
}); 