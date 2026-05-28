<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PersonRegistrationController;
use App\Http\Controllers\Api\V1\SecondaryIdentityController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FamilyTreeController; // Add this to the top with the others
use App\Http\Controllers\Api\V1\IdentityController; // Put at the top
use App\Http\Controllers\Api\V1\OrphanController;
use App\Http\Controllers\Api\V1\MarriageController;
use App\Http\Controllers\UserController;

Route::prefix('v1')->group(function () {

    Route::post('/marriages', [MarriageController::class, 'store']);
    Route::post('/marriages/divorce', [MarriageController::class, 'divorce']);

    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users/create', [UserController::class, 'store']);
    
    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/orphans', [OrphanController::class, 'store']);

    // Protect all these routes with Sanctum
    Route::middleware('auth:sanctum')->group(function () {

    // Put inside the auth:sanctum group:
    Route::get('/identity/{nrc}', [IdentityController::class, 'lookup'])->where('nrc', '.*');
        
    // Add this inside the auth:sanctum group:
    Route::get('/family-tree/{nrc}', [FamilyTreeController::class, 'show'])->where('nrc', '.*');
        // 1. Citizen Lookup
        Route::get('/citizens/{nrc}', [PersonRegistrationController::class, 'show'])->where('nrc', '.*');

        // 2. Register Citizen
        Route::post('/citizens/register', [PersonRegistrationController::class, 'store']);

        // 3. Issue Passport (Only for Admins)
        Route::post('/identity/issue-passport', [SecondaryIdentityController::class, 'issuePassport'])
            ->middleware('role:admin');
    });

});