<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\ActivityLog;
use App\OCR\OCRService;
use App\Telegram\TelegramService;
use App\Repositories\ReceiptRepositoryInterface;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessReceiptOCRJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $telegramChatId;
    protected $telegramMessageId;
    protected $fileId;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string|int $telegramChatId, int $telegramMessageId, string $fileId, ?int $userId = null)
    {
        $this->telegramChatId = $telegramChatId;
        $this->telegramMessageId = $telegramMessageId;
        $this->fileId = $fileId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(
        TelegramService $telegramService,
        OCRService $ocrService,
        ReceiptRepositoryInterface $receiptRepository,
        AnalyticsService $analyticsService
    ): void {
        Log::info("Handling ProcessReceiptOCRJob for chat_id {$this->telegramChatId}");

        // 1. Get Telegram file path
        $telegramPath = $telegramService->getFilePath($this->fileId);
        if (!$telegramPath) {
            $telegramService->sendReply($this->telegramChatId, $this->telegramMessageId, "❌ Gagal memproses struk. File gambar tidak dapat ditemukan di server Telegram.");
            return;
        }

        // 2. Download receipt image to secure public storage
        $filename = 'RCP_' . time() . '_' . Str::random(8) . '.jpg';
        $relativeStoragePath = 'receipts/' . $filename;
        $absolutePath = storage_path('app/public/' . $relativeStoragePath);

        $downloadSuccess = $telegramService->downloadFile($telegramPath, $absolutePath);
        if (!$downloadSuccess) {
            $telegramService->sendReply($this->telegramChatId, $this->telegramMessageId, "❌ Gagal memproses struk. Gambar struk gagal diunduh ke server kami.");
            return;
        }

        // 3. Process image via OCR and Parser
        $parsedData = $ocrService->process($absolutePath);
        if (!$parsedData['success']) {
            $telegramService->sendReply(
                $this->telegramChatId, 
                $this->telegramMessageId, 
                "❌ Gagal memproses struk. Tesseract OCR gagal mengekstrak data dari gambar struk Anda. Silakan coba kirim ulang dengan gambar yang lebih terang dan jelas."
            );
            return;
        }

        // 4. Save to Database
        try {
            $date = Carbon::parse($parsedData['receipt_date'])->toDateString();
        } catch (\Exception $e) {
            $date = Carbon::today()->toDateString();
        }

        // Generate unique receipt code: RCP-YYYYMMDD-XXXX
        $code = 'RCP-' . Carbon::today()->format('Ymd') . '-' . strtoupper(Str::random(4));

        $receiptData = [
            'receipt_code' => $code,
            'store_name' => $parsedData['store_name'],
            'total_price' => $parsedData['total_price'],
            'tax' => $parsedData['tax'] ?? 0,
            'discount' => $parsedData['discount'] ?? 0,
            'category' => $parsedData['category'] ?? 'Others',
            'confidence_score' => $parsedData['confidence_score'] ?? 90,
            'receipt_image' => $relativeStoragePath,
            'receipt_date' => $date,
            'raw_text' => $parsedData['raw_text'],
            'created_by' => $this->userId
        ];

        // Format items list for repository insert
        $items = [];
        foreach ($parsedData['items'] as $item) {
            $items[] = [
                'item_name' => $item['item_name'],
                'category' => $item['category'] ?? 'Others',
                'price' => $item['price'],
                'qty' => $item['qty'],
                'subtotal' => $item['subtotal']
            ];
        }

        try {
            $receipt = $receiptRepository->create($receiptData, $items);

            // Log activity
            ActivityLog::create([
                'user_id' => $this->userId,
                'activity' => 'Unggah Struk via Telegram',
                'description' => "Struk {$code} dari " . $parsedData['store_name'] . " dengan total belanja Rp " . number_format($parsedData['total_price'], 0, ',', '.') . " berhasil diunggah via Telegram dan diproses otomatis oleh OCR."
            ]);

            // Rebuild cache
            $analyticsService->getDashboardStats();

            $hash = hash_hmac('sha256', $receipt->id . $code, env('APP_KEY'));
            $editUrl = rtrim(env('APP_URL', 'http://localhost'), '/') . "/receipts/{$receipt->id}/telegram-edit/{$hash}";

            // 5. Send rich reply message to Telegram Chat
            $message = "✅ <b>Receipt Berhasil Diproses!</b>\n\n";
            $message .= "🔑 Code: <code>{$code}</code>\n";
            $message .= "🏪 Toko: <b>" . htmlspecialchars($parsedData['store_name']) . "</b>\n";
            $message .= "📅 Tanggal: <b>" . Carbon::parse($date)->translatedFormat('d F Y') . "</b>\n";
            $message .= "💰 Total: <b>Rp " . number_format($parsedData['total_price'], 0, ',', '.') . "</b>\n";
            $message .= "🛒 Jumlah Item: <b>" . count($parsedData['items']) . " item</b>\n\n";

            if (count($parsedData['items']) > 0) {
                $message .= "<b>Detail Belanja:</b>\n";
                $i = 1;
                foreach ($parsedData['items'] as $item) {
                    $itemTotal = number_format($item['subtotal'], 0, ',', '.');
                    $qtyStr = $item['qty'] > 1 ? " ({$item['qty']}x)" : "";
                    $message .= "{$i}. " . htmlspecialchars($item['item_name']) . "{$qtyStr} - Rp {$itemTotal}\n";
                    $i++;
                }
                $message .= "\n";
            }

            $message .= "✏️ <b>Data Tidak Sesuai?</b>\n";
            $message .= "Silakan klik tombol di bawah ini untuk mengedit data secara langsung di Telegram.\n\n";
            $message .= "🌐 Buka dashboard admin Anda untuk melihat grafik analytics realtime!";

            $keyboard = [
                'inline_keyboard' => [
                    [
                        [
                            'text' => '✏️ Edit Langsung di Telegram',
                            'web_app' => [
                                'url' => $editUrl
                            ]
                        ]
                    ]
                ]
            ];

            $telegramService->sendReply($this->telegramChatId, $this->telegramMessageId, $message, [
                'reply_markup' => json_encode($keyboard)
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to save OCR receipt to database: " . $e->getMessage());
            
            // Clean up file if DB save failed
            if (file_exists($absolutePath)) {
                @unlink($absolutePath);
            }
            
            $telegramService->sendReply($this->telegramChatId, $this->telegramMessageId, "❌ Gagal memproses struk. Terjadi kesalahan internal saat menyimpan struk Anda.");
        }
    }
}
