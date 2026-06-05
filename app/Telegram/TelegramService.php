<?php

namespace App\Telegram;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $token;
    protected $apiUrl;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
        $this->apiUrl = "https://api.telegram.org/bot{$this->token}";
    }

    /**
     * Send a standard chat message.
     */
    public function sendMessage(string|int $chatId, string $text, array $extraOptions = []): bool
    {
        if (empty($this->token) || $this->token === 'YOUR_BOT_TOKEN_HERE') {
            Log::error("Telegram Bot Token is not configured in .env");
            return false;
        }

        $payload = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ], $extraOptions);

        try {
            $response = Http::post("{$this->apiUrl}/sendMessage", $payload);
            if ($response->successful()) {
                return true;
            }
            
            Log::error("Telegram sendMessage API error: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Telegram sendMessage request exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a reply to a specific user message.
     */
    public function sendReply(string|int $chatId, int $messageId, string $text): bool
    {
        return $this->sendMessage($chatId, $text, [
            'reply_to_message_id' => $messageId
        ]);
    }

    /**
     * Fetch file details (such as file_path) using file_id.
     */
    public function getFilePath(string $fileId): ?string
    {
        if (empty($this->token) || $this->token === 'YOUR_BOT_TOKEN_HERE') {
            Log::error("Telegram Bot Token is not configured in .env");
            return null;
        }

        try {
            $response = Http::get("{$this->apiUrl}/getFile", [
                'file_id' => $fileId
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['result']['file_path'])) {
                    return $result['result']['file_path'];
                }
            }

            Log::error("Telegram getFile API error: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("Telegram getFile request exception: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Download a file from Telegram's file server to local storage.
     */
    public function downloadFile(string $telegramFilePath, string $localPath): bool
    {
        if (empty($this->token) || $this->token === 'YOUR_BOT_TOKEN_HERE') {
            Log::error("Telegram Bot Token is not configured in .env");
            return false;
        }

        $fileUrl = "https://api.telegram.org/file/bot{$this->token}/{$telegramFilePath}";
        
        try {
            Log::info("Downloading Telegram file from URL: {$fileUrl}");
            
            $response = Http::get($fileUrl);
            
            if ($response->successful()) {
                $dir = dirname($localPath);
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }

                file_put_contents($localPath, $response->body());
                Log::info("Telegram file downloaded successfully and saved to: {$localPath}");
                return true;
            }

            Log::error("Telegram downloadFile server error: " . $response->status());
            return false;
        } catch (\Exception $e) {
            Log::error("Telegram downloadFile exception: " . $e->getMessage());
            return false;
        }
    }
}
