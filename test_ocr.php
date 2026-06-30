<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ocr = app(App\OCR\OCRService::class);
$result = $ocr->process('storage/app/public/receipts/RCP_1780630537_ARs4HP9R.jpg');
echo json_encode($result, JSON_PRETTY_PRINT);
