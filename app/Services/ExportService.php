<?php

namespace App\Services;

class ExportService
{
    /**
     * Export receipts collection to CSV format string.
     */
    public function exportToCsv($receipts): string
    {
        $handle = fopen('php://temp', 'r+');
        
        // UTF-8 BOM for Indonesian characters & symbols in Excel
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // CSV Headers
        fputcsv($handle, [
            'Receipt Code', 
            'Store Name', 
            'Receipt Date', 
            'Total Items', 
            'Total Price (IDR)',
            'Date Created'
        ]);

        foreach ($receipts as $receipt) {
            fputcsv($handle, [
                $receipt->receipt_code,
                $receipt->store_name,
                $receipt->receipt_date,
                $receipt->items->count(),
                $receipt->total_price,
                $receipt->created_at->toDateTimeString()
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    /**
     * Export receipts collection to Excel-compatible HTML/XML format.
     * Excel opens this directly with beautiful native formatting.
     */
    public function exportToExcel($receipts): string
    {
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-type" content="text/html;charset=utf-8" />';
        $html .= '<style>';
        $html .= 'table { border-collapse: collapse; font-family: sans-serif; }';
        $html .= 'th { background-color: #0F172A; color: #FFFFFF; text-align: left; padding: 10px; }';
        $html .= 'td { padding: 8px; border: 1px solid #E2E8F0; }';
        $html .= 'tr:nth-child(even) { background-color: #F8FAFC; }';
        $html .= '</style>';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<h2>Purchase Receipts Export</h2>';
        $html .= '<p>Generated Date: ' . now()->toDateTimeString() . '</p>';
        $html .= '<table>';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Receipt Code</th>';
        $html .= '<th>Store Name</th>';
        $html .= '<th>Receipt Date</th>';
        $html .= '<th>Total Items</th>';
        $html .= '<th>Total Price (IDR)</th>';
        $html .= '<th>Date Created</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($receipts as $receipt) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($receipt->receipt_code) . '</td>';
            $html .= '<td>' . htmlspecialchars($receipt->store_name) . '</td>';
            $html .= '<td>' . htmlspecialchars($receipt->receipt_date) . '</td>';
            $html .= '<td>' . $receipt->items->count() . '</td>';
            $html .= '<td>' . (float) $receipt->total_price . '</td>';
            $html .= '<td>' . htmlspecialchars($receipt->created_at->toDateTimeString()) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';

        return $html;
    }
}
