<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'date',
    'daily_total',
    'weekly_total',
    'monthly_total',
    'yearly_total'
])]
class Analytics extends Model
{
    protected $table = 'analytics';
}
