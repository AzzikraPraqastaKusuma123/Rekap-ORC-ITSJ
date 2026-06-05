<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


// Telegram Webhook Endpoint
Route::post('/api/telegram/webhook', [\App\Http\Controllers\Api\TelegramWebhookController::class, 'handle']);


use App\Http\Controllers\ReceiptWebController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard Overview
    Route::get('/dashboard', [ReceiptWebController::class, 'dashboard'])->name('dashboard');
    
    // Receipt Management
    Route::get('/receipts', [ReceiptWebController::class, 'receiptsIndex'])->name('receipts.index');
    Route::post('/receipts/{id}/update', [ReceiptWebController::class, 'updateReceipt'])->name('receipts.update');
    Route::delete('/receipts/{id}', [ReceiptWebController::class, 'deleteReceipt'])->name('receipts.delete');
    
    // Export Data
    Route::get('/receipts/export/csv', [ReceiptWebController::class, 'exportCsv'])->name('receipts.export.csv');
    Route::get('/receipts/export/excel', [ReceiptWebController::class, 'exportExcel'])->name('receipts.export.excel');
    
    // Bot & Settings Configuration
    Route::get('/settings', [ReceiptWebController::class, 'settings'])->name('settings');
    Route::post('/settings', [ReceiptWebController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/webhook', [ReceiptWebController::class, 'setTelegramWebhook'])->name('settings.webhook');

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
