<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    protected $fillable = ['target_id', 'type', 'message'];

    public function target(): BelongsTo
    {
        return $this->belongsTo(Target::class);
    }
}
