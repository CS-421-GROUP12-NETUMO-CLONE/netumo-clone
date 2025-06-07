<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['target_id', 'status_code', 'latency', 'checked_at'];

    protected function casts()
    {
        return [
          'checked_at' => 'datetime'
        ];
    }

    public function target()
    {
        return $this->belongsTo(Target::class);
    }

}
