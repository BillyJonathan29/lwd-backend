<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// coba coba
// Route::post('/register', [AuthController::class, 'register']);


// Route::post('/login', [AuthController::class, 'login']);

// Route::middleware('auth::sanctum')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
// });


Route::prefix('auth')->group(function () {
    Route::post('/login',        [AuthController::class, 'login']);
    Route::post('/login-google', [AuthController::class, 'loginWithGoogle']);
});

// ── Protected Routes ───────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout',     [AuthController::class, 'logout']);
        Route::put('/update-fcm',  [AuthController::class, 'updateFcm']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    });

    // Profile
    // Route::prefix('profile')->group(function () {
    //     Route::get('/',    [ProfileController::class, 'show']);
    // });

});
