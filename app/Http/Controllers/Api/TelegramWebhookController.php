<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Telegram\TelegramService;
use App\Jobs\ProcessReceiptOCRJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Handle incoming webhook requests from Telegram Bot API.
     */
    public function handle(Request $request)
    {
        Log::info("Telegram Webhook Request Received", $request->all());

        $message = $request->input('message');
        if (!$message) {
            return response()->json(['status' => 'no_message']);
        }

        $chatId = $message['chat']['id'] ?? null;
        $messageId = $message['message_id'] ?? null;
        
        if (!$chatId) {
            return response()->json(['status' => 'no_chat_id']);
        }

        // Check if there is an associated user
        $user = User::where('telegram_chat_id', (string)$chatId)->first();

        // 1. Handle incoming text commands
        if (isset($message['text'])) {
            $text = trim($message['text']);
            
            if (str_starts_with($text, '/start')) {
                return $this->handleStartCommand($chatId, $user);
            }

            if (str_starts_with($text, '/help')) {
                return $this->handleHelpCommand($chatId);
            }
        }

        // 2. Handle incoming photos (Receipt uploads)
        if (isset($message['photo']) && is_array($message['photo'])) {
            if (!$user) {
                // Return clear linking instructions with their Chat ID
                $unlinkedMessage = "❌ <b>Akun Telegram Anda Belum Terhubung!</b>\n\n";
                $unlinkedMessage .= "Silakan hubungkan akun Telegram Anda ke dashboard admin terlebih dahulu agar kami dapat merekap struk belanja Anda secara otomatis.\n\n";
                $unlinkedMessage .= "📋 <b>Langkah-langkah menghubungkan:</b>\n";
                $unlinkedMessage .= "1. Login ke web dashboard admin\n";
                $unlinkedMessage .= "2. Buka menu <b>Settings</b> / Profil Anda\n";
                $unlinkedMessage .= "3. Hubungkan Telegram dengan memasukkan Chat ID Anda berikut:\n\n";
                $unlinkedMessage .= "🔑 <b>Chat ID Anda:</b> <code>{$chatId}</code>\n\n";
                $unlinkedMessage .= "Setelah Anda menyimpannya di dashboard, kirim ulang foto struk belanja Anda!";
                
                $this->telegramService->sendMessage($chatId, $unlinkedMessage);
                return response()->json(['status' => 'unlinked_user']);
            }

            // Get the highest resolution photo (which is the last item in the array)
            $photoArray = $message['photo'];
            $highestResPhoto = end($photoArray);
            $fileId = $highestResPhoto['file_id'];

            // Fast acknowledgment response
            $this->telegramService->sendReply($chatId, $messageId, "⏳ <b>Struk diterima!</b> Kami sedang memproses gambar Anda menggunakan OCR. Laporan lengkap akan dikirimkan dalam beberapa saat...");

            // Dispatch background queue job for async processing
            ProcessReceiptOCRJob::dispatch($chatId, $messageId, $fileId, $user->id);

            return response()->json(['status' => 'job_dispatched']);
        }

        // 3. Fallback for unsupported message types (audio, video, documents, etc.)
        $fallbackMessage = "👋 Halo! Silakan kirimkan foto struk belanja Anda untuk merekap pengeluaran secara otomatis.\n\nKetik /help untuk panduan lebih lanjut.";
        $this->telegramService->sendMessage($chatId, $fallbackMessage);

        return response()->json(['status' => 'unsupported_type']);
    }

    /**
     * Handle the /start command.
     */
    private function handleStartCommand(string|int $chatId, ?User $user)
    {
        $statusStr = $user 
            ? "✅ <b>Terhubung</b> (User: <code>" . htmlspecialchars($user->name) . "</code>)" 
            : "❌ <b>Belum Terhubung</b>";

        $message = "👋 <b>Selamat Datang di Sistem Monitoring Receipt OCR!</b>\n\n";
        $message .= "Cukup kirimkan foto struk belanja Anda ke bot ini, dan sistem kami secara otomatis akan melakukan:\n";
        $message .= "🔹 Grayscaling & Noise reduction pada struk\n";
        $message .= "🔹 OCR scanning lokal menggunakan Tesseract\n";
        $message .= "🔹 Ekstraksi nama toko, tanggal, item produk, & total harga\n";
        $message .= "🔹 Sync data & realtime analytics di dashboard admin\n\n";
        $message .= "⚙️ <b>Status Bot:</b> {$statusStr}\n";
        $message .= "🔑 <b>Telegram Chat ID Anda:</b> <code>{$chatId}</code>\n\n";

        if (!$user) {
            $message .= "⚠️ <i>Silakan masukkan Chat ID di atas ke menu Settings pada dashboard web Anda untuk mulai merekap!</i>";
        } else {
            $message .= "📸 <i>Kirim foto struk belanja pertama Anda sekarang untuk mencoba!</i>";
        }

        $this->telegramService->sendMessage($chatId, $message);
        return response()->json(['status' => 'start_handled']);
    }

    /**
     * Handle the /help command.
     */
    private function handleHelpCommand(string|int $chatId)
    {
        $message = "💡 <b>Panduan Penggunaan Receipt OCR Bot:</b>\n\n";
        $message .= "1️⃣ Pastikan akun Telegram Anda sudah terhubung ke dashboard admin di menu Settings.\n";
        $message .= "2️⃣ Kirim foto struk belanja Anda (bisa dari Indomaret, Alfamart, dll).\n";
        $message .= "3️⃣ Pastikan foto struk menghadap tegak lurus, pencahayaan cukup, tulisan tidak buram, dan teks struk terlihat jelas.\n";
        $message .= "4️⃣ Tunggu 5-10 detik, bot akan mengirimkan laporan struk belanja dan otomatis terekam pada analytics dashboard Anda.\n\n";
        $message .= "Silakan hubungi Super Admin jika mengalami masalah teknis.";

        $this->telegramService->sendMessage($chatId, $message);
        return response()->json(['status' => 'help_handled']);
    }
}
