<?php

namespace App\Repositories;

interface AnalyticsRepositoryInterface
{
    /**
     * Calculate summary statistics from receipts.
     * Returns an array with today, week, month, year totals.
     */
    public function getSummaryStats();

    /**
     * Get daily spending trend for a given number of days.
     */
    public function getDailySpendingTrend(int $days = 30);

    /**
     * Get pre-compiled analytics by date.
     */
    public function getCachedAnalytics(string $date);

    /**
     * Save pre-compiled analytics record.
     */
    public function saveAnalytics(string $date, array $totals);
}
