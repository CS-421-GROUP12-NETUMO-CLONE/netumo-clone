<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'target_id',
        'ssl_expiry_date',
        'domain_expiry_date',
        'days_to_ssl_expiry',
        'days_to_domain_expiry',
        'checked_at',
    ];

    protected function casts()
    {
        return [
            'checked_at' => 'datetime',
            'ssl_expiry_date' => 'datetime',
            'domain_expiry_date' => 'datetime',
        ];
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(Target::class);
    }
}
