use App\Http\Controllers\Api\CitizenController;

// Protect this route so only authenticated System Users can register a citizen
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/citizens', [CitizenController::class, 'store']);
});
