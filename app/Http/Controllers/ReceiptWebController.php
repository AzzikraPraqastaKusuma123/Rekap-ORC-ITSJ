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
        $stats = $this->analyticsService->getDashboardStats();
        $recentTransactions = $this->receiptRepository->getRecent(5);
        $activityLogs = ActivityLog::with('user')->orderBy('created_at', 'desc')->limit(5)->get();

        // ApexCharts compiled data
        $spendingTrend = $this->analyticsService->getDailySpendingChartData(15); // last 15 days
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
        $filters = $request->only(['search', 'store', 'start_date', 'end_date', 'sort_by', 'sort_order']);
        $receipts = $this->receiptRepository->getFiltered($filters, 9); // Paginate 9 per page
        
        $stores = $this->receiptRepository->getUniqueStores();

        $editReceipt = null;
        if ($request->has('edit')) {
            try {
                $editReceipt = $this->receiptRepository->find((int)$request->query('edit'))->load('items');
            } catch (\Exception $e) {
                // Ignore if not found
            }
        }

        return view('receipts.index', compact('receipts', 'stores', 'filters', 'editReceipt'));
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
            'items' => 'required|array',
            'items.*.item_name' => 'required|string|max:255',
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
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal
                ];
            }

            // In case the user enters manual totals, else use computed
            $totalPrice = $request->input('total_price') ?: $computedTotal;

            $data = [
                'store_name' => $request->input('store_name'),
                'receipt_date' => $request->input('receipt_date'),
                'total_price' => $totalPrice,
            ];

            $this->receiptRepository->update((int)$id, $data, $itemsData);

            // Rebuild analytics cache
            $this->analyticsService->getDashboardStats();

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Manual Verification',
                'description' => "Receipt ID {$id} has been manually updated/verified by " . Auth::user()->name
            ]);

            return redirect()->back()->with('success', 'Receipt successfully verified and updated.');
        } catch (\Exception $e) {
            Log::error("Failed to manually verify receipt: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update receipt: ' . $e->getMessage());
        }
    }

    /**
     * Delete receipt by ID.
     */
    public function deleteReceipt($id)
    {
        try {
            $receipt = $this->receiptRepository->find((int)$id);
            
            // Delete image file from local storage
            if ($receipt->receipt_image) {
                Storage::disk('public')->delete($receipt->receipt_image);
            }

            $code = $receipt->receipt_code;
            $this->receiptRepository->delete((int)$id);

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
            'telegram_bot_token' => 'nullable|string|max:200'
        ]);

        $user = Auth::user();
        
        // Save Telegram Chat ID to Current User
        if ($request->has('telegram_chat_id')) {
            $user->telegram_chat_id = $request->input('telegram_chat_id');
            $user->save();
        }

        // Save environmental variables
        if ($request->has('tesseract_path') || $request->has('telegram_bot_token')) {
            $this->updateEnvironmentFile([
                'TESSERACT_PATH' => $request->input('tesseract_path') ?? 'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
                'TELEGRAM_BOT_TOKEN' => $request->input('telegram_bot_token') ?? 'YOUR_BOT_TOKEN_HERE'
            ]);
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
            $receipt = $this->receiptRepository->find((int)$id)->load('items');
            
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
            $receipt = $this->receiptRepository->find((int)$id);
            
            // Verify hash
            $expectedHash = hash_hmac('sha256', $receipt->id . $receipt->receipt_code, env('APP_KEY'));
            if (!hash_equals($expectedHash, $hash)) {
                abort(403, 'Unauthorized edit signature');
            }

            $request->validate([
                'store_name' => 'required|string|max:255',
                'receipt_date' => 'required|date',
                'total_price' => 'required|numeric',
                'items' => 'required|array',
                'items.*.item_name' => 'required|string|max:255',
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
            ];

            $this->receiptRepository->update((int)$id, $data, $itemsData);

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
}
