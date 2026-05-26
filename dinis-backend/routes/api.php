<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PersonRegistrationController;
use App\Http\Controllers\Api\V1\SecondaryIdentityController;
use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function () {
    
    Route::post('/login', [AuthController::class, 'login']);

    // Protect all these routes with Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        

        // 1. Citizen Lookup
        Route::get('/citizens/{nrc}', [PersonRegistrationController::class, 'show']);

        // 2. Register Citizen
        Route::post('/citizens/register', [PersonRegistrationController::class, 'store']);

        // 3. Issue Passport (Only for Admins)
        Route::post('/identity/issue-passport', [SecondaryIdentityController::class, 'issuePassport'])
            ->middleware('role:admin');
    });

});