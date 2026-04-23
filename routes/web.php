<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\WarningLetterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});



Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user');
        Route::get('create', [UserController::class, 'create'])->name('user.create');
        Route::post('store', [UserController::class, 'store'])->name('user.store');
        Route::get('{user}', [UserController::class, 'show'])->name('user.show');
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('{user}/update', [UserController::class, 'update'])->name('user.update');
        Route::delete('{user}/destroy', [UserController::class, 'destroy'])->name('user.destroy');
    });
    Route::prefix('pertemuan')->group(function () {
        Route::get('/', [MeetingController::class, 'index'])->name('pertemuan');
        Route::get('create', [MeetingController::class, 'create'])->name('pertemuan.create');
        Route::post('store', [MeetingController::class, 'store'])->name('pertemuan.store');
        Route::get('{pertemuan}', [MeetingController::class, 'show'])->name('pertemuan.show');
        Route::get('{pertemuan}/edit', [MeetingController::class, 'edit'])->name('pertemuan.edit');
        Route::put('{pertemuan}/update', [MeetingController::class, 'update'])->name('pertemuan.update');
        Route::delete('{pertemuan}/destroy', [MeetingController::class, 'destroy'])->name('pertemuan.destroy');
    });
    Route::prefix('presensi')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('presensi');
        Route::get('create', [AttendanceController::class, 'create'])->name('presensi.create');
        Route::post('store', [AttendanceController::class, 'store'])->name('presensi.store');
        Route::get('export/excel', [AttendanceController::class, 'exportExcel'])->name('presensi.export.excel');
        Route::get('export/pdf', [AttendanceController::class, 'exportPdf'])->name('presensi.export.pdf');
        Route::get('{presensi}', [AttendanceController::class, 'show'])->name('presensi.show');
        Route::get('{presensi}/edit', [AttendanceController::class, 'edit'])->name('presensi.edit');
        Route::put('{presensi}/update', [AttendanceController::class, 'update'])->name('presensi.update');
        Route::delete('{presensi}/destroy', [AttendanceController::class, 'destroy'])->name('presensi.destroy');
    });
    Route::prefix('submission')->group(function () {
        Route::get('/', [SubmissionController::class, 'index'])->name('submission');
        Route::put('{submission}/evaluate', [SubmissionController::class, 'evaluate'])->name('submission.evaluate');
    });

    Route::prefix('warning-letters')->group(function () {
        Route::get('/', [WarningLetterController::class, 'index'])->name('warning_letters');
        Route::post('/store', [WarningLetterController::class, 'store'])->name('warning_letters.store');
    });

    Route::prefix('setting')->group(function () {
        Route::get('profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('profile/info', [ProfileController::class, 'updateInfo'])->name('profile.update_info');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update_password');
        Route::post('profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.update_photo');
    });
});
