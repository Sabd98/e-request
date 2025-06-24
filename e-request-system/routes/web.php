<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// routes/web.php
Auth::routes();
Route::get('/reset-session', function () {
    session()->flush();
    return redirect('/login');
});
Route::middleware(['auth'])->group(function () {
    // Dashboard umum
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Requestor routes
    Route::prefix('requests')->group(function () {
        Route::get('/', [RequestController::class, 'index'])->name('requests.index');
        Route::get('/create', [RequestController::class, 'create'])->name('requests.create');
        Route::post('/', [RequestController::class, 'store'])->name('requests.store');
        Route::get('/{request}', [RequestController::class, 'show'])->name('requests.show');
        Route::get('/{request}/edit', [RequestController::class, 'edit'])->name('requests.edit');
        Route::put('/{request}', [RequestController::class, 'update'])->name('requests.update');
        Route::delete('/{request}', [RequestController::class, 'destroy'])->name('requests.destroy');
        Route::post('/{request}/submit', [RequestController::class, 'submit'])->name('requests.submit');
    });
    // routes/web.php

    // // Approver routes
    Route::prefix('approvals')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::get('/{request}', [ApprovalController::class, 'show'])->name('approvals.show');
        // routes/web.php
        Route::get('/requests/{request}/download', [RequestController::class, 'downloadAttachment'])
            ->name('requests.download');
        Route::post('/{request}/approve', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('/{request}/reject', [ApprovalController::class, 'reject'])->name('reject');
    });

    // Approver routes     
    Route::middleware(['role:approver'])->group(function () {
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::get('/approvals/{request}', [ApprovalController::class, 'show'])->name('approvals.show');
        Route::post('/approve/{request}', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('/reject/{request}', [ApprovalController::class, 'reject'])->name('reject');
    });


    Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/trash', [RequestController::class, 'trash'])->name('requests.trash');
        Route::post('/restore/{id}', [RequestController::class, 'restore'])->name('requests.restore');
        Route::delete('/force-delete/{id}', [RequestController::class, 'forceDelete'])->name('requests.force-delete');
    });
    // Route::prefix('role:admin')->group(function () {
       
    // });
});