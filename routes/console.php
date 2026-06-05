<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// OCR CLI Testing Tool
Artisan::command('receipt:test-ocr {path}', function (\App\OCR\OCRService $ocrService) {
    $path = $this->argument('path');
    $this->info("Running OCR and Preprocessing Pipeline for: {$path}");
    
    if (!file_exists($path)) {
        $this->error("Error: File not found at path: {$path}");
        return;
    }

    $result = $ocrService->process($path);
    
    if ($result['success']) {
        $this->info("=========================================");
        $this->info("✅ SUCCESS: RECEIPT PARSED SUCCESSFULLY");
        $this->info("=========================================");
        $this->info("🏪 Store Name : " . $result['store_name']);
        $this->info("📅 Date       : " . $result['receipt_date']);
        $this->info("💰 Total Price: Rp " . number_format($result['total_price'], 0, ',', '.'));
        $this->info("🛒 Total Items: " . count($result['items']) . " item(s)");
        $this->info("=========================================");
        $this->info("🛍️ Item List:");
        foreach ($result['items'] as $index => $item) {
            $this->line("  " . ($index + 1) . ". " . str_pad($item['item_name'], 30) . " x" . $item['qty'] . " @ Rp " . number_format($item['price'], 0, ',', '.') . " = Rp " . number_format($item['subtotal'], 0, ',', '.'));
        }
        $this->info("=========================================");
    } else {
        $this->error("❌ OCR FAILED: " . $result['message']);
    }
})->purpose('Run local OCR receipt scanning and data extraction on a test image file');

// Reports / Analytics compiler Command
Artisan::command('receipt:generate-reports', function (\App\Services\AnalyticsService $analyticsService) {
    $this->info("Compiling daily spend aggregates and rebuilding cache...");
    $stats = $analyticsService->generateDailyReport();
    $this->info("✅ Success: Spending aggregates cached successfully in 'analytics' table.");
    $this->info("  - Today Total : Rp " . number_format($stats['today']['total'], 0, ',', '.'));
    $this->info("  - Week Total  : Rp " . number_format($stats['week']['total'], 0, ',', '.'));
    $this->info("  - Month Total : Rp " . number_format($stats['month']['total'], 0, ',', '.'));
    $this->info("  - Year Total  : Rp " . number_format($stats['year']['total'], 0, ',', '.'));
})->purpose('Compile daily spend aggregates and rebuild analytics cache in DB');

// Schedule daily expenditure report rebuilds
use Illuminate\Support\Facades\Schedule;

Schedule::command('receipt:generate-reports')->daily();

