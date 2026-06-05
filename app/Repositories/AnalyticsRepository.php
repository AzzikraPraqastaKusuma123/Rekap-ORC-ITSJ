<?php

namespace App\Repositories;

use App\Models\Receipt;
use App\Models\Analytics;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsRepository implements AnalyticsRepositoryInterface
{
    /**
     * Calculate summary statistics from receipts including period comparisons.
     */
    public function getSummaryStats()
    {
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek()->toDateString();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek()->toDateString();
        
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth()->toDateString();
        
        $startOfYear = Carbon::now()->startOfYear()->toDateString();
        $startOfLastYear = Carbon::now()->subYear()->startOfYear()->toDateString();
        $endOfLastYear = Carbon::now()->subYear()->endOfYear()->toDateString();

        // 1. Today vs Yesterday
        $spentToday = Receipt::where('receipt_date', $today)->sum('total_price') ?: 0;
        $spentYesterday = Receipt::where('receipt_date', $yesterday)->sum('total_price') ?: 0;
        
        // 2. This Week vs Last Week
        $spentThisWeek = Receipt::where('receipt_date', '>=', $startOfWeek)->sum('total_price') ?: 0;
        $spentLastWeek = Receipt::whereBetween('receipt_date', [$startOfLastWeek, $endOfLastWeek])->sum('total_price') ?: 0;

        // 3. This Month vs Last Month
        $spentThisMonth = Receipt::where('receipt_date', '>=', $startOfMonth)->sum('total_price') ?: 0;
        $spentLastMonth = Receipt::whereBetween('receipt_date', [$startOfLastMonth, $endOfLastMonth])->sum('total_price') ?: 0;

        // 4. This Year vs Last Year
        $spentThisYear = Receipt::where('receipt_date', '>=', $startOfYear)->sum('total_price') ?: 0;
        $spentLastYear = Receipt::whereBetween('receipt_date', [$startOfLastYear, $endOfLastYear])->sum('total_price') ?: 0;

        return [
            'today' => [
                'total' => $spentToday,
                'previous' => $spentYesterday,
                'change_percentage' => $this->calculatePercentageChange($spentToday, $spentYesterday)
            ],
            'week' => [
                'total' => $spentThisWeek,
                'previous' => $spentLastWeek,
                'change_percentage' => $this->calculatePercentageChange($spentThisWeek, $spentLastWeek)
            ],
            'month' => [
                'total' => $spentThisMonth,
                'previous' => $spentLastMonth,
                'change_percentage' => $this->calculatePercentageChange($spentThisMonth, $spentLastMonth)
            ],
            'year' => [
                'total' => $spentThisYear,
                'previous' => $spentLastYear,
                'change_percentage' => $this->calculatePercentageChange($spentThisYear, $spentLastYear)
            ]
        ];
    }

    /**
     * Get daily spending trend for a given number of days.
     */
    public function getDailySpendingTrend(int $days = 30)
    {
        $startDate = Carbon::now()->subDays($days - 1)->toDateString();

        return Receipt::select('receipt_date', DB::raw('SUM(total_price) as total_spent'))
            ->where('receipt_date', '>=', $startDate)
            ->groupBy('receipt_date')
            ->orderBy('receipt_date', 'asc')
            ->get();
    }

    /**
     * Get pre-compiled analytics by date.
     */
    public function getCachedAnalytics(string $date)
    {
        return Analytics::where('date', $date)->first();
    }

    /**
     * Save pre-compiled analytics record.
     */
    public function saveAnalytics(string $date, array $totals)
    {
        return Analytics::updateOrCreate(
            ['date' => $date],
            [
                'daily_total' => $totals['today'] ?? 0,
                'weekly_total' => $totals['week'] ?? 0,
                'monthly_total' => $totals['month'] ?? 0,
                'yearly_total' => $totals['year'] ?? 0,
            ]
        );
    }

    /**
     * Helper to compute percentage change securely (handling division by zero).
     */
    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }
}
