<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\MemberController;
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
        Route::get('/meetings',     [MeetingController::class, 'index']);
        Route::get('/meetings/{id}', [MeetingController::class, 'show']);

        Route::post('/attendances', [AttendanceController::class, 'store']);
        Route::post('/submissions', [SubmissionController::class, 'store']);

        Route::get('/attendances-history', [HistoryController::class, 'attendanceHistory']);
        Route::get('/submissions-history', [HistoryController::class, 'submissionHistory']);
        Route::get('/warning-letters', [HistoryController::class, 'warningLetterHistory']);
    });


    Route::middleware('auth:sanctum')->prefix('member')->group(function(){
        Route::get('/home', [MemberController::class, 'index']);
    });


    // Profile
    // Route::prefix('profile')->group(function () {
    //     Route::get('/',    [ProfileController::class, 'show']);
    // });

});


