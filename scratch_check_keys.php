<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

$geminiKey = env('GEMINI_API_KEY');

echo "Testing Gemini API Key: " . substr($geminiKey, 0, 12) . "...\n";
try {
    $model = 'gemini-1.5-flash';
    $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$geminiKey}", [
        'contents' => [
            [
                'parts' => [
                    ['text' => 'Hello, respond with "OK" if you receive this.']
                ]
            ]
        ]
    ]);
    
    echo "Gemini Status: " . $response->status() . "\n";
    echo "Gemini Body: " . $response->body() . "\n\n";
} catch (\Exception $e) {
    echo "Gemini Exception: " . $e->getMessage() . "\n\n";
}
