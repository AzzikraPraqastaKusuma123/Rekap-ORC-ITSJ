<?php

namespace App\Services;

use App\Repositories\AnalyticsRepositoryInterface;
use App\Repositories\ReceiptRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AnalyticsService
{
    protected $analyticsRepository;
    protected $receiptRepository;

    public function __construct(
        AnalyticsRepositoryInterface $analyticsRepository,
        ReceiptRepositoryInterface $receiptRepository
    ) {
        $this->analyticsRepository = $analyticsRepository;
        $this->receiptRepository = $receiptRepository;
    }

    /**
     * Get real-time stats. If aggregates aren't cached for today, recalculate and cache them.
     */
    public function getDashboardStats(): array
    {
        $today = Carbon::today()->toDateString();
        
        // Calculate in real-time to ensure absolute accuracy on dashboard load
        $stats = $this->analyticsRepository->getSummaryStats();
        
        // Cache the aggregates for this date in database
        try {
            $this->analyticsRepository->saveAnalytics($today, [
                'today' => $stats['today']['total'],
                'week' => $stats['week']['total'],
                'month' => $stats['month']['total'],
                'year' => $stats['year']['total']
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to cache analytics in database: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get data formatted specifically for ApexCharts line graphs (daily spending).
     */
    public function getDailySpendingChartData(int $days = 30): array
    {
        $trend = $this->analyticsRepository->getDailySpendingTrend($days);
        
        $categories = [];
        $data = [];
        
        // Create full list of days to ensure no missing dates on chart
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $label = Carbon::now()->subDays($i)->translatedFormat('d M');
            $categories[] = $label;
            
            $spent = 0;
            foreach ($trend as $record) {
                if ($record->receipt_date === $date) {
                    $spent = (float) $record->total_spent;
                    break;
                }
            }
            $data[] = $spent;
        }

        return [
            'labels' => $categories,
            'series' => $data
        ];
    }

    /**
     * Get spending share by store.
     */
    public function getStoreSpendingChartData(): array
    {
        $breakdown = $this->receiptRepository->getSpendingByStore();
        
        $labels = [];
        $series = [];
        
        foreach ($breakdown as $item) {
            $labels[] = $item->store_name;
            $series[] = (float) $item->total_spent;
        }

        return [
            'labels' => $labels,
            'series' => $series
        ];
    }

    /**
     * Get top purchased items list.
     */
    public function getTopProductsChartData(int $limit = 5): array
    {
        $top = $this->receiptRepository->getTopProducts($limit);
        
        $labels = [];
        $series = [];
        
        foreach ($top as $item) {
            $labels[] = $item->item_name;
            $series[] = (int) $item->total_qty;
        }

        return [
            'labels' => $labels,
            'series' => $series,
            'raw_records' => $top
        ];
    }

    /**
     * Trigger aggregate rebuild (used by scheduler).
     */
    public function generateDailyReport(): array
    {
        $today = Carbon::today()->toDateString();
        Log::info("Generating daily analytics cache for: {$today}");
        
        $stats = $this->analyticsRepository->getSummaryStats();
        
        $this->analyticsRepository->saveAnalytics($today, [
            'today' => $stats['today']['total'],
            'week' => $stats['week']['total'],
            'month' => $stats['month']['total'],
            'year' => $stats['year']['total']
        ]);
        
        return $stats;
    }
}
