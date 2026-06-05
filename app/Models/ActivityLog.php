<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'user_id',
    'activity',
    'description'
])]
class ActivityLog extends Model
{
    /**
     * Get the user associated with this activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
