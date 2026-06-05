<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'receipt_id',
    'item_name',
    'price',
    'qty',
    'subtotal'
])]
class ReceiptItem extends Model
{
    /**
     * Get the receipt that owns this item.
     */
    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}
