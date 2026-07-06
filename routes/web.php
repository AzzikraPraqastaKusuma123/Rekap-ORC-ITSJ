<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


// Telegram Webhook Endpoint
Route::post('/api/telegram/webhook', [\App\Http\Controllers\Api\TelegramWebhookController::class, 'handle']);

// Telegram WebApp direct edit routes (secured via secret hash)
Route::get('/receipts/{id}/telegram-edit/{hash}', [\App\Http\Controllers\ReceiptWebController::class, 'telegramEdit'])->name('receipts.telegram-edit');
Route::post('/receipts/{id}/telegram-edit/{hash}', [\App\Http\Controllers\ReceiptWebController::class, 'telegramUpdate'])->name('receipts.telegram-update');


use App\Http\Controllers\ReceiptWebController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Shared Dashboard (Logic handled in controller)
    Route::get('/dashboard', [ReceiptWebController::class, 'dashboard'])->name('dashboard');
    Route::get('/api/internal/dashboard', [ReceiptWebController::class, 'apiDashboardStats'])->name('api.dashboard.stats');

    // Shared Receipt Management
    Route::get('/receipts', [ReceiptWebController::class, 'receiptsIndex'])->name('receipts.index');
    Route::post('/receipts/upload', [ReceiptWebController::class, 'upload'])->name('receipts.upload');
    Route::post('/receipts/{id}/update', [ReceiptWebController::class, 'updateReceipt'])->name('receipts.update');
    Route::get('/receipts/print', [ReceiptWebController::class, 'printReport'])->name('receipts.print');
    // Approval API (Finance / Admin only)
    Route::middleware('role:superadmin,admin')->post('/receipts/{id}/approve', [ReceiptWebController::class, 'approveReceipt'])->name('receipts.approve');
    Route::middleware('role:superadmin,admin')->post('/receipts/{id}/reject', [ReceiptWebController::class, 'rejectReceipt'])->name('receipts.reject');

    // Elevated Privileges (Admin & Finance)
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::delete('/receipts/{id}', [ReceiptWebController::class, 'deleteReceipt'])->name('receipts.delete');
        Route::get('/receipts/export/csv', [ReceiptWebController::class, 'exportCsv'])->name('receipts.export.csv');
        Route::get('/receipts/export/excel', [ReceiptWebController::class, 'exportExcel'])->name('receipts.export.excel');
    });

    // Extreme Privileges (Superadmin Only)
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/settings', [ReceiptWebController::class, 'settings'])->name('settings');
        Route::post('/settings', [ReceiptWebController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/webhook', [ReceiptWebController::class, 'setTelegramWebhook'])->name('settings.webhook');

        // User Management
        Route::get('/admin/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
        Route::post('/admin/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('users.store');
        Route::post('/admin/users/{id}/role', [\App\Http\Controllers\Admin\UserManagementController::class, 'updateRole'])->name('users.updateRole');
        Route::delete('/admin/users/{id}', [\App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('users.destroy');
    });

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
