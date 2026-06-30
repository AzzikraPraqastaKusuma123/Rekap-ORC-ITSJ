<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'receipt_code',
    'store_name',
    'total_price',
    'tax',
    'discount',
    'category',
    'confidence_score',
    'receipt_image',
    'receipt_date',
    'raw_text',
    'created_by'
])]
class Receipt extends Model
{
    /**
     * Get the items for the receipt.
     */
    public function items()
    {
        return $this->hasMany(ReceiptItem::class);
    }

    /**
     * Get the user who created/owns this receipt.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
