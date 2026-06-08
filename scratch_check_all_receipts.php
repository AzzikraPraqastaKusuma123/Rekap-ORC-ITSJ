<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$receipts = App\Models\Receipt::with('items')->orderBy('id', 'desc')->get();
foreach ($receipts as $r) {
    echo "ID: {$r->id} | Code: {$r->receipt_code} | Store: {$r->store_name} | Total: Rp {$r->total_price} | Date: {$r->receipt_date} | Created At: {$r->created_at}\n";
    echo "Items count: " . count($r->items) . "\n";
    foreach ($r->items as $item) {
        echo "  - {$item->item_name} x{$item->qty} = Rp {$item->subtotal}\n";
    }
    echo "--------------------------------------------------------\n";
}
