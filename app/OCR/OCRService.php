<?php

namespace App\OCR;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OCRService
{
    /**
     * Run the complete OCR and parsing pipeline.
     */
    public function process(string $imagePath): array
    {
        Log::info("Starting OCR pipeline for: {$imagePath}");

        if (!file_exists($imagePath)) {
            Log::error("Image file not found: {$imagePath}");
            return $this->getEmptyResult("Image file not found");
        }

        // Use Anthropic if key is configured
        $anthropicKey = env('ANTHROPIC_API_KEY');
        if (!empty($anthropicKey) && $anthropicKey !== 'YOUR_ANTHROPIC_KEY_HERE') {
            return $this->processWithAnthropic($imagePath, $anthropicKey);
        }

        // Use OpenAI if key is configured
        $openaiKey = env('OPENAI_API_KEY');
        if (!empty($openaiKey) && $openaiKey !== 'YOUR_OPENAI_KEY_HERE') {
            return $this->processWithOpenAI($imagePath, $openaiKey);
        }

        // Use Gemini AI if key is configured
        $geminiKey = env('GEMINI_API_KEY');
        if (!empty($geminiKey) && $geminiKey !== 'YOUR_GEMINI_KEY_HERE') {
            return $this->processWithGemini($imagePath, $geminiKey);
        }

        return $this->processWithTesseract($imagePath);
    }

    /**
     * Process image via Anthropic Claude API.
     */
    private function processWithAnthropic(string $imagePath, string $apiKey): array
    {
        Log::info("Processing receipt with Anthropic Claude API: {$imagePath}");
        
        try {
            $imageData = base64_encode(file_get_contents($imagePath));
            
            // Get mime type of the image
            $mimeType = 'image/jpeg';
            if (function_exists('mime_content_type')) {
                $mimeType = @mime_content_type($imagePath) ?: 'image/jpeg';
            } else {
                $pathInfo = pathinfo($imagePath);
                $ext = strtolower($pathInfo['extension'] ?? '');
                if ($ext === 'png') {
                    $mimeType = 'image/png';
                } elseif ($ext === 'webp') {
                    $mimeType = 'image/webp';
                }
            }
            
            $prompt = "Identify the receipt/struk belanja details and return a clean JSON object.
Ensure the output matches this exact JSON structure:
{
  \"store_name\": \"Name of the store/merchant (e.g. ALFAMART, INDOMARET, etc. in uppercase)\",
  \"receipt_date\": \"Date on the receipt in YYYY-MM-DD format (if not found, use today's date)\",
  \"total_price\": numeric_total_price (e.g. 45000),
  \"items\": [
    {
      \"item_name\": \"Item description\",
      \"price\": numeric_unit_price,
      \"qty\": numeric_quantity,
      \"subtotal\": numeric_subtotal
    }
  ],
  \"raw_text\": \"All readable text on the receipt\"
}

Do not wrap the JSON in markdown code blocks. Return ONLY the raw JSON string matching the schema.";

            $model = env('ANTHROPIC_MODEL', 'claude-3-5-sonnet-20241022');

            $response = Http::timeout(30)
                ->withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type' => 'application/json'
                ])
                ->post("https://api.anthropic.com/v1/messages", [
                    'model' => $model,
                    'max_tokens' => 1024,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $prompt
                                ],
                                [
                                    'type' => 'image',
                                    'source' => [
                                        'type' => 'base64',
                                        'media_type' => $mimeType,
                                        'data' => $imageData
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $resultText = $response->json('content.0.text');
                Log::info("Anthropic Raw Response: " . $resultText);
                
                $parsedJson = json_decode(trim($resultText), true);
                
                if (json_last_error() === JSON_ERROR_NONE && isset($parsedJson['store_name'])) {
                    $parsedJson['success'] = true;
                    $parsedJson['message'] = 'Receipt successfully parsed via Anthropic Claude API';
                    
                    // Guarantee fields exist
                    if (!isset($parsedJson['total_price'])) {
                        $parsedJson['total_price'] = 0;
                    }
                    if (!isset($parsedJson['receipt_date'])) {
                        $parsedJson['receipt_date'] = Carbon::today()->toDateString();
                    }
                    if (!isset($parsedJson['items'])) {
                        $parsedJson['items'] = [];
                    }
                    if (!isset($parsedJson['raw_text'])) {
                        $parsedJson['raw_text'] = '';
                    }
                    
                    return $parsedJson;
                }
                
                Log::warning("Anthropic did not return a valid JSON: " . $resultText);
            } else {
                Log::error("Anthropic API call failed with status " . $response->status() . ": " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Exception in Anthropic processing: " . $e->getMessage());
        }

        // Fallback to OpenAI if Anthropic fails
        Log::warning("Anthropic API failed, trying OpenAI as fallback");
        
        $openaiKey = env('OPENAI_API_KEY');
        if (!empty($openaiKey) && $openaiKey !== 'YOUR_OPENAI_KEY_HERE') {
            return $this->processWithOpenAI($imagePath, $openaiKey);
        }
        
        $geminiKey = env('GEMINI_API_KEY');
        if (!empty($geminiKey) && $geminiKey !== 'YOUR_GEMINI_KEY_HERE') {
            return $this->processWithGemini($imagePath, $geminiKey);
        }
        
        return $this->processWithTesseract($imagePath);
    }

    /**
     * Process image via OpenAI API.
     */
    private function processWithOpenAI(string $imagePath, string $apiKey): array
    {
        Log::info("Processing receipt with OpenAI API: {$imagePath}");
        
        try {
            $imageData = base64_encode(file_get_contents($imagePath));
            
            // Get mime type of the image
            $mimeType = 'image/jpeg';
            if (function_exists('mime_content_type')) {
                $mimeType = @mime_content_type($imagePath) ?: 'image/jpeg';
            } else {
                $pathInfo = pathinfo($imagePath);
                $ext = strtolower($pathInfo['extension'] ?? '');
                if ($ext === 'png') {
                    $mimeType = 'image/png';
                } elseif ($ext === 'webp') {
                    $mimeType = 'image/webp';
                }
            }
            
            $prompt = "Identify the receipt/struk belanja details and return a clean JSON object.
Ensure the output matches this exact JSON structure:
{
  \"store_name\": \"Name of the store/merchant (e.g. ALFAMART, INDOMARET, etc. in uppercase)\",
  \"receipt_date\": \"Date on the receipt in YYYY-MM-DD format (if not found, use today's date)\",
  \"total_price\": numeric_total_price (e.g. 45000),
  \"items\": [
    {
      \"item_name\": \"Item description\",
      \"price\": numeric_unit_price,
      \"qty\": numeric_quantity,
      \"subtotal\": numeric_subtotal
    }
  ],
  \"raw_text\": \"All readable text on the receipt\"
}

Do not wrap the JSON in markdown code blocks. Return ONLY the raw JSON string matching the schema.";

            $model = env('OPENAI_MODEL', 'gpt-4o-mini');

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json'
                ])
                ->post("https://api.openai.com/v1/chat/completions", [
                    'model' => $model,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $prompt
                                ],
                                [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => "data:{$mimeType};base64,{$imageData}"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $resultText = $response->json('choices.0.message.content');
                Log::info("OpenAI Raw Response: " . $resultText);
                
                $parsedJson = json_decode(trim($resultText), true);
                
                if (json_last_error() === JSON_ERROR_NONE && isset($parsedJson['store_name'])) {
                    $parsedJson['success'] = true;
                    $parsedJson['message'] = 'Receipt successfully parsed via OpenAI API';
                    
                    // Guarantee fields exist
                    if (!isset($parsedJson['total_price'])) {
                        $parsedJson['total_price'] = 0;
                    }
                    if (!isset($parsedJson['receipt_date'])) {
                        $parsedJson['receipt_date'] = Carbon::today()->toDateString();
                    }
                    if (!isset($parsedJson['items'])) {
                        $parsedJson['items'] = [];
                    }
                    if (!isset($parsedJson['raw_text'])) {
                        $parsedJson['raw_text'] = '';
                    }
                    
                    return $parsedJson;
                }
                
                Log::warning("OpenAI did not return a valid JSON: " . $resultText);
            } else {
                Log::error("OpenAI API call failed with status " . $response->status() . ": " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Exception in OpenAI processing: " . $e->getMessage());
        }

        // Fallback to Gemini if OpenAI fails
        Log::warning("OpenAI API failed, trying Gemini as fallback");
        
        $geminiKey = env('GEMINI_API_KEY');
        if (!empty($geminiKey) && $geminiKey !== 'YOUR_GEMINI_KEY_HERE') {
            return $this->processWithGemini($imagePath, $geminiKey);
        }
        
        return $this->processWithTesseract($imagePath);
    }

    /**
     * Process image via Google Gemini API.
     */
    private function processWithGemini(string $imagePath, string $apiKey): array
    {
        Log::info("Processing receipt with Gemini AI: {$imagePath}");
        
        try {
            $imageData = base64_encode(file_get_contents($imagePath));
            
            // Get mime type of the image
            $mimeType = 'image/jpeg';
            if (function_exists('mime_content_type')) {
                $mimeType = @mime_content_type($imagePath) ?: 'image/jpeg';
            } else {
                $pathInfo = pathinfo($imagePath);
                $ext = strtolower($pathInfo['extension'] ?? '');
                if ($ext === 'png') {
                    $mimeType = 'image/png';
                } elseif ($ext === 'webp') {
                    $mimeType = 'image/webp';
                } elseif ($ext === 'gif') {
                    $mimeType = 'image/gif';
                }
            }
            
            $prompt = "Identify the receipt/struk belanja details and return a clean JSON object.
Ensure the output matches this exact JSON structure:
{
  \"store_name\": \"Name of the store/merchant (e.g. ALFAMART, INDOMARET, etc. in uppercase)\",
  \"receipt_date\": \"Date on the receipt in YYYY-MM-DD format (if not found, use today's date)\",
  \"total_price\": numeric_total_price (e.g. 45000),
  \"items\": [
    {
      \"item_name\": \"Item description\",
      \"price\": numeric_unit_price,
      \"qty\": numeric_quantity,
      \"subtotal\": numeric_subtotal
    }
  ],
  \"raw_text\": \"All readable text on the receipt\"
}

Do not wrap the JSON in markdown code blocks. Return ONLY the raw JSON string matching the schema.";

            $model = env('GEMINI_MODEL', 'gemini-1.5-flash');
            $response = Http::timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeType,
                                    'data' => $imageData
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->successful()) {
                $resultText = $response->json('candidates.0.content.parts.0.text');
                Log::info("Gemini Raw Response: " . $resultText);
                
                $parsedJson = json_decode(trim($resultText), true);
                
                if (json_last_error() === JSON_ERROR_NONE && isset($parsedJson['store_name'])) {
                    $parsedJson['success'] = true;
                    $parsedJson['message'] = 'Receipt successfully parsed via Gemini AI';
                    
                    // Guarantee fields exist
                    if (!isset($parsedJson['total_price'])) {
                        $parsedJson['total_price'] = 0;
                    }
                    if (!isset($parsedJson['receipt_date'])) {
                        $parsedJson['receipt_date'] = Carbon::today()->toDateString();
                    }
                    if (!isset($parsedJson['items'])) {
                        $parsedJson['items'] = [];
                    }
                    if (!isset($parsedJson['raw_text'])) {
                        $parsedJson['raw_text'] = '';
                    }
                    
                    return $parsedJson;
                }
                
                Log::warning("Gemini did not return a valid JSON matching the format: " . $resultText);
            } else {
                Log::error("Gemini API call failed with status " . $response->status() . ": " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Exception in Gemini processing: " . $e->getMessage());
        }

        // Fallback to Tesseract if Gemini fails
        Log::warning("Gemini AI failed, falling back to local Tesseract OCR");
        return $this->processWithTesseract($imagePath);
    }

    /**
     * Process image via local Tesseract OCR (Fallback).
     */
    private function processWithTesseract(string $imagePath): array
    {
        // 1. Preprocess the image to optimize Tesseract reading
        $preprocessedPath = $this->preprocess($imagePath);
        if (!$preprocessedPath) {
            // Fallback to original image if preprocessing fails
            $preprocessedPath = $imagePath;
        }

        // 2. Run Tesseract OCR command
        $rawText = $this->runTesseract($preprocessedPath);
        
        // Clean up preprocessed temp file if it was created
        if ($preprocessedPath !== $imagePath && file_exists($preprocessedPath)) {
            @unlink($preprocessedPath);
        }

        if (empty($rawText)) {
            Log::warning("Tesseract returned empty text");
            return $this->getEmptyResult("Failed to extract text from image");
        }

        // 3. Parse the raw text
        return $this->parseReceiptText($rawText);
    }

    /**
     * Preprocess image: Grayscale, Increase Contrast, and Sharpen.
     */
    private function preprocess(string $imagePath): ?string
    {
        try {
            $info = getimagesize($imagePath);
            if (!$info) return null;

            $mime = $info['mime'];
            switch ($mime) {
                case 'image/jpeg':
                case 'image/jpg':
                    $image = imagecreatefromjpeg($imagePath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($imagePath);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($imagePath);
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($imagePath);
                    break;
                default:
                    return null;
            }

            if (!$image) return null;

            // A. Convert to Grayscale
            imagefilter($image, IMG_FILTER_GRAYSCALE);

            // B. Increase Contrast (In GD, negative values increase contrast)
            imagefilter($image, IMG_FILTER_CONTRAST, -35);

            // C. Sharpen convolution matrix
            $sharpenMatrix = [
                [0, -1, 0],
                [-1, 5, -1],
                [0, -1, 0]
            ];
            $divisor = array_sum(array_map('array_sum', $sharpenMatrix));
            imageconvolution($image, $sharpenMatrix, $divisor ?: 1, 0);

            // Save preprocessed image to a temp path
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $tempPath = $tempDir . '/preprocessed_' . time() . '_' . uniqid() . '.png';
            imagepng($image, $tempPath);
            imagedestroy($image);

            return $tempPath;
        } catch (\Exception $e) {
            Log::error("Image preprocessing failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Execute local Tesseract OCR.
     */
    private function runTesseract(string $imagePath): string
    {
        $tesseractPath = env('TESSERACT_PATH', 'C:\Program Files\Tesseract-OCR\tesseract.exe');
        
        // Escape paths for shell execution on Windows/Linux
        $escapedTesseract = escapeshellarg($tesseractPath);
        $escapedImage = escapeshellarg($imagePath);
        
        // Command to print output to stdout
        $command = "{$escapedTesseract} {$escapedImage} stdout --psm 4";
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Under Windows shell, standard wrapper
            $command = "\"{$tesseractPath}\" \"{$imagePath}\" stdout --psm 4";
        }

        Log::info("Executing command: {$command}");
        
        try {
            $output = shell_exec($command);
            return $output ? trim($output) : '';
        } catch (\Exception $e) {
            Log::error("Tesseract execution failed: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Robust Regex Receipt Parser for Indonesian Struk (Indomaret, Alfamart, etc.).
     */
    public function parseReceiptText(string $rawText): array
    {
        $lines = explode("\n", $rawText);
        $cleanLines = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $cleanLines[] = $line;
            }
        }

        // 1. Parse Store Name
        $storeName = $this->extractStoreName($cleanLines);

        // 2. Parse Date
        $receiptDate = $this->extractDate($rawText);

        // 3. Parse Total Price
        $totalPrice = $this->extractTotal($rawText, $cleanLines);

        // 4. Parse Items & Prices
        $items = $this->extractItems($cleanLines);

        // Fallback: If total price is 0 but we parsed items, sum up item subtotals
        if ($totalPrice == 0 && !empty($items)) {
            foreach ($items as $item) {
                $totalPrice += $item['subtotal'];
            }
        }

        return [
            'store_name' => $storeName,
            'receipt_date' => $receiptDate,
            'total_price' => $totalPrice,
            'items' => $items,
            'raw_text' => $rawText,
            'success' => true,
            'message' => 'Receipt successfully parsed'
        ];
    }

    /**
     * Extracts store name based on keywords or prominent first lines.
     */
    private function extractStoreName(array $lines): string
    {
        $commonStores = [
            'INDOMARET', 'ALFAMART', 'SUPER INDO', 'ALFAMIDI', 
            'GIANT', 'CARREFOUR', 'YOGYA', 'HYPERMART', 
            'TRANS MART', 'LAWSON', 'CIRCLE K', 'HERO', 'MATAHARI'
        ];

        // Search for known keywords in the first 8 lines
        $searchLimit = min(8, count($lines));
        for ($i = 0; $i < $searchLimit; $i++) {
            $upperLine = strtoupper($lines[$i]);
            foreach ($commonStores as $store) {
                if (str_contains($upperLine, $store)) {
                    return $store;
                }
            }
        }

        // Fallback: Use the first non-numeric, clean-looking line
        for ($i = 0; $i < $searchLimit; $i++) {
            $line = trim($lines[$i]);
            // Avoid address lines, phone numbers, or code looking lines
            if (strlen($line) > 3 
                && !preg_match('/^[0-9\-\.\/\s\:]+$/', $line)
                && !str_contains(strtolower($line), 'jl.')
                && !str_contains(strtolower($line), 'jalan')
                && !str_contains(strtolower($line), 'telp')
                && !str_contains(strtolower($line), 'npwp')
            ) {
                return $line;
            }
        }

        return 'Unknown Store';
    }

    /**
     * Extracts date using common regex patterns.
     */
    private function extractDate(string $text): string
    {
        // Patterns:
        // DD-MM-YYYY or DD/MM/YYYY or DD.MM.YYYY
        // YYYY-MM-DD
        // DD-MM-YY or DD/MM/YY
        $datePatterns = [
            '/(\d{2})[-.\/](\d{2})[-.\/](\d{4})/',
            '/(\d{4})[-.\/](\d{2})[-.\/](\d{2})/',
            '/(\d{2})[-.\/](\d{2})[-.\/](\d{2})/'
        ];

        foreach ($datePatterns as $index => $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                try {
                    if ($index === 0) {
                        // DD-MM-YYYY
                        return Carbon::createFromDate($matches[3], $matches[2], $matches[1])->toDateString();
                    } elseif ($index === 1) {
                        // YYYY-MM-DD
                        return Carbon::createFromDate($matches[1], $matches[2], $matches[3])->toDateString();
                    } elseif ($index === 2) {
                        // DD-MM-YY
                        $year = (int)$matches[3] > 80 ? '19' . $matches[3] : '20' . $matches[3];
                        return Carbon::createFromDate($year, $matches[2], $matches[1])->toDateString();
                    }
                } catch (\Exception $e) {
                    // Fail silently, continue checking
                }
            }
        }

        // If no date found or parsing failed, default to today
        return Carbon::today()->toDateString();
    }

    /**
     * Extract Grand Total from text.
     */
    private function extractTotal(string $text, array $lines): float
    {
        // Direct search for Total in text
        $totalKeywords = 'TOTAL|GRAND\s+TOTAL|JUMLAH|NETTO|BAYAR|TAGIHAN|SUBTOTAL';
        $pattern = '/(?:' . $totalKeywords . ')\s*[:=]?\s*(?:Rp\.?\s*)?(\d{1,3}(?:[\.,]\d{3})+|\d{3,9})/i';

        if (preg_match_all($pattern, $text, $matches)) {
            // Return the last match (often grand total is at the bottom)
            $lastIndex = count($matches[1]) - 1;
            $rawNumber = $matches[1][$lastIndex];
            return $this->cleanPrice($rawNumber);
        }

        // Search lines in reverse (often at the bottom of the receipt)
        for ($i = count($lines) - 1; $i >= 0; $i--) {
            $line = $lines[$i];
            if (preg_match('/(?:TOTAL|GRAND|JUMLAH|BAYAR)/i', $line)) {
                if (preg_match('/(\d{1,3}(?:[\.,]\d{3})+|\d{3,9})/', $line, $numMatch)) {
                    return $this->cleanPrice($numMatch[1]);
                }
            }
        }

        return 0;
    }

    /**
     * Extracts items, pricing and quantities.
     */
    private function extractItems(array $lines): array
    {
        $items = [];
        
        // Loop through lines, ignoring known store headers, payments, addresses
        foreach ($lines as $line) {
            $upperLine = strtoupper($line);
            
            // Skip lines that represent totals, card details, VAT, taxes or address details
            if (preg_match('/(?:TOTAL|GRAND|JUMLAH|BAYAR|CASH|KEMBALI|DEBIT|EDC|KARTU|CHANGE|TUNAI|NPWP|JL\.|JALAN|TELP|PPN|TAX|DISKON|DISCOUNT|PROMO|MEMBER)/i', $upperLine)) {
                continue;
            }

            // Look for patterns like: Product Name followed by Price (and optional Qty)
            // e.g. "AQUA 600ML 5.000"
            // or "INDOMIE AYAM BAWANG 3,500"
            // or "SHAMPOO 2 x 18000 36000"
            
            // 1. Check for item lines with qty multiplier: e.g. "2 x 15.000" or "2x15000" or "3 * 4000"
            if (preg_match('/(.+?)\s+(\d+)\s*[x\*]\s*(\d{1,3}(?:[\.,]\d{3})+|\d{3,9})\s*(\d{1,3}(?:[\.,]\d{3})+|\d{3,9})?/i', $line, $matches)) {
                $itemName = trim($matches[1]);
                $qty = (int)$matches[2];
                $price = $this->cleanPrice($matches[3]);
                $subtotal = isset($matches[4]) ? $this->cleanPrice($matches[4]) : ($price * $qty);

                if (strlen($itemName) > 2 && $price > 0) {
                    $items[] = [
                        'item_name' => $itemName,
                        'price' => $price,
                        'qty' => $qty,
                        'subtotal' => $subtotal
                    ];
                    continue;
                }
            }

            // 2. Check for simple: Product Name followed by Price
            // e.g. "AQUA GEL 5,500" or "ROTI BAKAR COKLAT 15000"
            // Look for text, ending with a numeric price
            if (preg_match('/(.+?)\s+(\d{1,3}(?:[\.,]\d{3})+|\d{3,7})$/', $line, $matches)) {
                $itemName = trim($matches[1]);
                $price = $this->cleanPrice($matches[2]);

                if (strlen($itemName) > 2 && $price > 100) { // Assume items cost more than 100 rupiah
                    $items[] = [
                        'item_name' => $itemName,
                        'price' => $price,
                        'qty' => 1,
                        'subtotal' => $price
                    ];
                }
            }
        }

        return $items;
    }

    /**
     * Cleans raw text numbers (removes dots, commas, currency symbols) and returns float/integer.
     */
    private function cleanPrice(string $rawPrice): float
    {
        // Strip out currency signs and letters, leave numbers, commas, and dots
        $clean = preg_replace('/[^0-9\.,]/', '', $rawPrice);
        
        // Handle common thousand/decimal separators in Indonesia
        // If there's a dot followed by 3 digits at the end (e.g. 5.000), it's a thousand separator.
        // We remove dots if they are thousand separators.
        
        // A common way: count the number of dots and commas
        // If it's like 5,000 or 5.000 -> typically 5000 in IDR.
        // If there is only one separator, and it is followed by exactly 3 digits, we remove it.
        if (preg_match('/[\.,](\d{3})$/', $clean)) {
            $clean = preg_replace('/[\.,]/', '', $clean);
        } else {
            // Replace commas with dots for decimal conversion if they are decimal separators
            $clean = str_replace(',', '.', $clean);
        }

        return (float) $clean;
    }

    /**
     * Returns an empty response structure.
     */
    private function getEmptyResult(string $message): array
    {
        return [
            'store_name' => 'Unknown Store',
            'receipt_date' => Carbon::today()->toDateString(),
            'total_price' => 0,
            'items' => [],
            'raw_text' => '',
            'success' => false,
            'message' => $message
        ];
    }
}
