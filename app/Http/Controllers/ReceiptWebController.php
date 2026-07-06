<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Receipt;
use App\Models\User;
use App\Repositories\ReceiptRepositoryInterface;
use App\Services\AnalyticsService;
use App\Services\ExportService;
use App\Telegram\TelegramService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReceiptWebController extends Controller
{
    protected $receiptRepository;
    protected $analyticsService;
    protected $exportService;

    public function __construct(
        ReceiptRepositoryInterface $receiptRepository,
        AnalyticsService $analyticsService,
        ExportService $exportService
    ) {
        $this->receiptRepository = $receiptRepository;
        $this->analyticsService = $analyticsService;
        $this->exportService = $exportService;
    }

    /**
     * Dashboard Overview (Real-time spending charts & stats).
     */
    public function dashboard()
    {
        $recentTransactions = $this->receiptRepository->getRecent(5);
        $activityLogs = ActivityLog::with('user')->orderBy('created_at', 'desc')->limit(5)->get();

        $stats = $this->analyticsService->getDashboardStats();
        $spendingTrend = $this->analyticsService->getDailySpendingChartData(15);
        $storeSpending = $this->analyticsService->getStoreSpendingChartData();
        $topProducts = $this->analyticsService->getTopProductsChartData(5);

        return view('dashboard', compact(
            'stats',
            'recentTransactions',
            'activityLogs',
            'spendingTrend',
            'storeSpending',
            'topProducts'
        ));
    }

    /**
     * Receipts Gallery / Management table.
     */
    public function receiptsIndex(Request $request)
    {
        $filters = $request->only(['search', 'store', 'start_date', 'end_date', 'sort_by', 'sort_order', 'status']);
        $receipts = $this->receiptRepository->getFiltered($filters, 9); // Paginate 9 per page

        $stores = $this->receiptRepository->getUniqueStores();

        $editReceipt = null;
        if ($request->has('edit')) {
            try {
                $editReceipt = $this->receiptRepository->find((int) $request->query('edit'))->load('items');
            } catch (\Exception $e) {
                // Ignore if not found
            }
        }

        return view('receipts.index', compact('receipts', 'stores', 'filters', 'editReceipt'));
    }

    /**
     * Print Filtered Receipts natively via browser optimized CSS layout
     */
    public function printReport(Request $request)
    {
        $filters = $request->only(['search', 'store', 'start_date', 'end_date', 'sort_by', 'sort_order', 'status']);
        // Override pagination to fetch a large subset suitable for printing
        $receipts = $this->receiptRepository->getFiltered($filters, 1000);

        return view('receipts.print', compact('receipts', 'filters'));
    }

    /**
     * Update receipt manual verification edits.
     */
    public function updateReceipt(Request $request, $id)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'receipt_date' => 'required|date',
            'total_price' => 'required|numeric',
            'category' => 'nullable|string|max:255',
            'tax' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'items' => 'required|array',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.category' => 'nullable|string|max:255',
            'items.*.price' => 'required|numeric',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            $itemsData = [];
            $computedTotal = 0;

            foreach ($request->items as $item) {
                $subtotal = $item['price'] * $item['qty'];
                $computedTotal += $subtotal;
                $itemsData[] = [
                    'item_name' => $item['item_name'],
                    'category' => $item['category'] ?? 'Others',
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal
                ];
            }

            // In case the user enters manual totals, else use computed
            $totalPrice = $request->input('total_price') ?: $computedTotal;

            // RBAC Enforcement: Staff edits default to pending_approval. Admins remain verified.
            $status = (Auth::user()->role === 'staff') ? 'pending_approval' : 'verified';

            $data = [
                'store_name' => $request->input('store_name'),
                'receipt_date' => $request->input('receipt_date'),
                'total_price' => $totalPrice,
                'category' => $request->input('category') ?: 'Others',
                'tax' => $request->input('tax') ?: 0,
                'discount' => $request->input('discount') ?: 0,
                'status' => $status
            ];

            $this->receiptRepository->update((int) $id, $data, $itemsData);

            // Rebuild analytics cache
            $this->analyticsService->getDashboardStats();

            // Log activity
            $logMsg = ($status === 'pending_approval')
                ? "Receipt ID {$id} edited by Staff " . Auth::user()->name . " (Pending Approval)"
                : "Receipt ID {$id} manually verified by " . Auth::user()->name;

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Manual Edit',
                'description' => $logMsg
            ]);

            return redirect()->back()->with(
                'success',
                $status === 'pending_approval'
                ? 'Struk berhasil dirubah. Menunggu ACC Direktur Keuangan.'
                : 'Struk berhasil diperbarui.'
            );
        } catch (\Exception $e) {
            Log::error("Failed to manually verify receipt: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update receipt: ' . $e->getMessage());
        }
    }

    /**
     * Approve Receipt (Finance / Admin)
     */
    public function approveReceipt($id)
    {
        try {
            $this->receiptRepository->update((int) $id, ['status' => 'verified'], []);
            $this->analyticsService->getDashboardStats();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Receipt Approved',
                'description' => "Receipt ID {$id} was approved by " . Auth::user()->name
            ]);

            return redirect()->back()->with('success', 'Struk berhasil disahkan (ACC).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal ACC struk: ' . $e->getMessage());
        }
    }

    /**
     * Reject Receipt (Finance / Admin)
     */
    public function rejectReceipt($id)
    {
        try {
            $this->receiptRepository->update((int) $id, ['status' => 'pending_correction'], []);
            $this->analyticsService->getDashboardStats();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Receipt Rejected',
                'description' => "Receipt ID {$id} was rejected by " . Auth::user()->name
            ]);

            return redirect()->back()->with('success', 'Struk Ditolak dan dikembalikan ke staf.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menolak struk: ' . $e->getMessage());
        }
    }

    /**
     * Delete receipt by ID.
     */
    public function deleteReceipt($id)
    {
        try {
            $receipt = $this->receiptRepository->find((int) $id);

            // Delete image file from local storage
            if ($receipt->receipt_image) {
                Storage::disk('public')->delete($receipt->receipt_image);
            }

            $code = $receipt->receipt_code;
            $this->receiptRepository->delete((int) $id);

            // Rebuild analytics cache
            $this->analyticsService->getDashboardStats();

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Receipt Deleted',
                'description' => "Receipt {$code} deleted by " . Auth::user()->name
            ]);

            return redirect()->route('receipts.index')->with('success', 'Receipt successfully deleted.');
        } catch (\Exception $e) {
            Log::error("Failed to delete receipt: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete receipt: ' . $e->getMessage());
        }
    }

    /**
     * Export to CSV format.
     */
    public function exportCsv(Request $request)
    {
        $filters = $request->only(['search', 'store', 'start_date', 'end_date']);
        $receipts = $this->receiptRepository->getFiltered($filters, 1000)->items(); // Get items from paginator limit 1000

        $csv = $this->exportService->exportToCsv($receipts);

        $filename = 'Receipts_Export_' . Carbon::now()->format('Ymd_His') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export to Excel.
     */
    public function exportExcel(Request $request)
    {
        $filters = $request->only(['search', 'store', 'start_date', 'end_date']);
        $receipts = $this->receiptRepository->getFiltered($filters, 1000)->items();

        $excelHtml = $this->exportService->exportToExcel($receipts);

        $filename = 'Receipts_Export_' . Carbon::now()->format('Ymd_His') . '.xls';

        return response($excelHtml, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Settings Page.
     */
    public function settings()
    {
        $user = Auth::user();
        return view('settings', compact('user'));
    }

    /**
     * Update settings (bot token, tesseract path, link telegram chat id).
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'telegram_chat_id' => 'nullable|string|max:100',
            'tesseract_path' => 'nullable|string|max:500',
            'telegram_bot_token' => 'nullable|string|max:200',
            'gemini_api_key' => 'nullable|string|max:200',

        ]);

        $user = Auth::user();

        // Save Telegram Chat ID to Current User
        if ($request->has('telegram_chat_id')) {
            $user->telegram_chat_id = $request->input('telegram_chat_id');
            $user->save();
        }

        $envUpdates = [];

        // Settings for external tools
        if ($request->has('tesseract_path'))
            $envUpdates['TESSERACT_PATH'] = $request->input('tesseract_path') ?? 'C:\\Program Files\\Tesseract-OCR\\tesseract.exe';
        if ($request->has('telegram_bot_token'))
            $envUpdates['TELEGRAM_BOT_TOKEN'] = $request->input('telegram_bot_token') ?? 'YOUR_BOT_TOKEN_HERE';

        // Settings for AI API Keys
        $apiKeysUpdated = false;
        if ($request->has('gemini_api_key')) {
            $envUpdates['GEMINI_API_KEY'] = $request->input('gemini_api_key') ?? 'YOUR_GEMINI_KEY_HERE';
            \Illuminate\Support\Facades\Cache::forget('gemini_quota_exhausted');
            $apiKeysUpdated = true;
        }

        // Save environmental variables
        if (!empty($envUpdates)) {
            $this->updateEnvironmentFile($envUpdates);
        }

        if ($apiKeysUpdated) {
            app(\App\Telegram\TelegramService::class)->notifyAdmins("✅ <b>API Key AI Diperbarui!</b>\nKonfigurasi API Key untuk AI (Gemini) baru saja disimpan di Dashboard.\n\nSistem Quota Limit telah di-reset, AI kembali berjalan sebagai prioritas utama (Priority 1) untuk ekstraksi struk!");
        }

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'Settings Updated',
            'description' => "Dashboard and bot settings updated by " . Auth::user()->name
        ]);

        return redirect()->back()->with('success', 'Settings successfully updated.');
    }

    /**
     * Handle web receipt upload.
     */
    public function upload(Request $request, \App\OCR\OCRService $ocrService, \App\Services\AnalyticsService $analyticsService)
    {
        $request->validate([
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        try {
            $file = $request->file('receipt_image');
            $filename = 'RCP_' . time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $file->getClientOriginalExtension();
            $relativeStoragePath = 'receipts/' . $filename;

            // Simpan gambar secara lokal di disk public (storage/app/public)
            $path = $file->storeAs('receipts', $filename, 'public');
            $absolutePath = storage_path('app/public/' . $path);

            // Proses OCR
            $parsedData = $ocrService->process($absolutePath);

            if (!$parsedData['success']) {
                // Hapus file jika gagal OCR
                if (file_exists($absolutePath)) {
                    @unlink($absolutePath);
                }
                return redirect()->back()->with('error', 'Gagal memproses struk. AI gagal membaca data dari gambar Anda. Silakan coba gambar yang lebih terang.');
            }

            // Simpan ke DB
            try {
                $date = \Carbon\Carbon::parse($parsedData['receipt_date'])->toDateString();
            } catch (\Exception $e) {
                $date = \Carbon\Carbon::today()->toDateString();
            }

            $code = 'RCP-' . \Carbon\Carbon::today()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));

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
                'created_by' => auth()->id()
            ];

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

            $receipt = $this->receiptRepository->create($receiptData, $items);

            // Log activity
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'activity' => 'Unggah Struk via Website',
                'description' => "Struk {$code} dari " . $parsedData['store_name'] . " dengan total Rp " . number_format($parsedData['total_price'], 0, ',', '.') . " diunggah via Website dan diproses OCR."
            ]);

            // Clear cache
            $analyticsService->getDashboardStats();

            return redirect()->route('receipts.index')->with('success', 'Struk berhasil diunggah dan diproses oleh AI OCR!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to process web receipt upload: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses gambar.');
        }
    }

    /**
     * Register app webhook with Telegram Bot API automatically based on APP_URL!
     */
    public function setTelegramWebhook()
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');

        if (empty($botToken) || $botToken === 'YOUR_BOT_TOKEN_HERE') {
            return redirect()->back()->with('error', 'Gagal: Telegram Bot Token belum dikonfigurasi di file .env!');
        }

        // Check if application is running on localhost (which Telegram cannot reach)
        $appUrl = env('APP_URL', 'http://localhost');
        if (str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            return redirect()->back()->with('warning', "Catatan: Webhook tidak dapat didaftarkan menggunakan alamat lokal '{$appUrl}'. Gunakan tunnel publik seperti ngrok atau deploy ke server online, lalu perbarui APP_URL di .env!");
        }

        $webhookUrl = rtrim($appUrl, '/') . '/api/telegram/webhook';
        $apiUrl = "https://api.telegram.org/bot{$botToken}/setWebhook?url={$webhookUrl}";

        try {
            $response = Http::get($apiUrl);

            if ($response->successful() && $response->json('ok') === true) {
                // Log activity
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'activity' => 'Webhook Registered',
                    'description' => "Telegram webhook successfully registered to URL: {$webhookUrl}"
                ]);

                return redirect()->back()->with('success', "Sukses! Telegram Webhook berhasil didaftarkan ke: {$webhookUrl}");
            }

            return redirect()->back()->with('error', 'Gagal mendaftarkan Webhook ke Telegram: ' . $response->json('description', 'Unknown Error'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Koneksi ke API Telegram gagal: ' . $e->getMessage());
        }
    }

    /**
     * Helper to write values back to the active .env file securely.
     */
    private function updateEnvironmentFile(array $data)
    {
        $envFile = base_path('.env');
        if (file_exists($envFile)) {
            $content = file_get_contents($envFile);

            foreach ($data as $key => $value) {
                // Keep windows backslashes correctly escaped
                $escapedValue = str_replace('\\', '\\\\', $value);

                // Regex to find and replace existing key, or append if not exists
                if (preg_match("/^{$key}=.*/m", $content)) {
                    $replacement = "{$key}=\"{$escapedValue}\"";
                    // Escape backslashes for preg_replace replacement string syntax
                    $replacement = str_replace('\\', '\\\\', $replacement);
                    $content = preg_replace("/^{$key}=.*/m", $replacement, $content);
                } else {
                    $content .= "\n{$key}=\"{$escapedValue}\"\n";
                }
            }

            file_put_contents($envFile, $content);
        }
    }

    /**
     * Show the receipt edit form specifically for Telegram WebApp (no login required).
     */
    public function telegramEdit($id, $hash)
    {
        try {
            $receipt = $this->receiptRepository->find((int) $id)->load('items');

            // Verify hash
            $expectedHash = hash_hmac('sha256', $receipt->id . $receipt->receipt_code, env('APP_KEY'));
            if (!hash_equals($expectedHash, $hash)) {
                abort(403, 'Unauthorized edit signature');
            }

            return view('receipts.telegram-edit', compact('receipt', 'hash'));
        } catch (\Exception $e) {
            abort(404, 'Receipt not found');
        }
    }

    /**
     * Save the receipt edit from Telegram WebApp.
     */
    public function telegramUpdate(Request $request, $id, $hash)
    {
        try {
            $receipt = $this->receiptRepository->find((int) $id);

            // Verify hash
            $expectedHash = hash_hmac('sha256', $receipt->id . $receipt->receipt_code, env('APP_KEY'));
            if (!hash_equals($expectedHash, $hash)) {
                abort(403, 'Unauthorized edit signature');
            }

            $request->validate([
                'store_name' => 'required|string|max:255',
                'receipt_date' => 'required|date',
                'total_price' => 'required|numeric',
                'category' => 'nullable|string|max:255',
                'tax' => 'nullable|numeric',
                'discount' => 'nullable|numeric',
                'items' => 'required|array',
                'items.*.item_name' => 'required|string|max:255',
                'items.*.category' => 'nullable|string|max:255',
                'items.*.price' => 'required|numeric',
                'items.*.qty' => 'required|integer|min:1',
            ]);

            $itemsData = [];
            $computedTotal = 0;

            foreach ($request->items as $item) {
                $subtotal = $item['price'] * $item['qty'];
                $computedTotal += $subtotal;
                $itemsData[] = [
                    'item_name' => $item['item_name'],
                    'category' => $item['category'] ?? 'Others',
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal
                ];
            }

            $totalPrice = $request->input('total_price') ?: $computedTotal;

            $data = [
                'store_name' => $request->input('store_name'),
                'receipt_date' => $request->input('receipt_date'),
                'total_price' => $totalPrice,
                'category' => $request->input('category') ?: 'Others',
                'tax' => $request->input('tax') ?: 0,
                'discount' => $request->input('discount') ?: 0,
            ];

            $this->receiptRepository->update((int) $id, $data, $itemsData);

            // Rebuild analytics cache
            $this->analyticsService->getDashboardStats();

            // Log activity
            \App\Models\ActivityLog::create([
                'user_id' => $receipt->created_by ?: 1,
                'activity' => 'Telegram WebApp Verification',
                'description' => "Receipt {$receipt->receipt_code} updated/verified directly via Telegram WebApp."
            ]);

            return view('receipts.telegram-success');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update receipt: ' . $e->getMessage());
        }
    }

    /**
     * API for Real-Time Dashboard updates via AJAX Polling.
     */
    public function apiDashboardStats()
    {
        $recentTransactions = $this->receiptRepository->getRecent(5);
        $activityLogs = ActivityLog::with('user')->orderBy('created_at', 'desc')->limit(5)->get();

        $stats = [];
        $spendingTrend = [];
        $storeSpending = [];
        $topProducts = [];

        if (Auth::user()->role !== 'staff') {
            $stats = $this->analyticsService->getDashboardStats();
            $spendingTrend = $this->analyticsService->getDailySpendingChartData(15);
            $storeSpending = $this->analyticsService->getStoreSpendingChartData();
            $topProducts = $this->analyticsService->getTopProductsChartData(5);
        }

        // Format dates and prepare custom HTML structures if needed to make the frontend replacement easy
        $recentTransactionsData = $recentTransactions->map(function ($tx) {
            return [
                'id' => $tx->id,
                'receipt_code' => $tx->receipt_code,
                'store_name' => $tx->store_name,
                'receipt_date' => \Carbon\Carbon::parse($tx->receipt_date)->translatedFormat('d M Y'),
                'total_price' => number_format($tx->total_price, 0, ',', '.'),
                'total_num' => $tx->total_price,
                'receipt_image' => $tx->receipt_image ? asset('storage/' . $tx->receipt_image) : null,
                'initial' => strtoupper(substr($tx->store_name ?? 'S', 0, 1))
            ];
        });

        $activityLogsData = $activityLogs->map(function ($log) {
            return [
                'activity' => $log->activity,
                'time' => $log->created_at->diffForHumans()
            ];
        });

        $htmlStats = [
            'today' => number_format($stats['today']['total'], 0, ',', '.'),
            'week' => number_format($stats['week']['total'], 0, ',', '.'),
            'month' => number_format($stats['month']['total'], 0, ',', '.'),
            'year' => number_format($stats['year']['total'], 0, ',', '.')
        ];

        return response()->json([
            'stats' => $htmlStats,
            'recentTransactions' => $recentTransactionsData,
            'activityLogs' => $activityLogsData,
            'spendingTrend' => $spendingTrend,
            'storeSpending' => $storeSpending,
            'topProducts' => $topProducts
        ]);
    }
}
