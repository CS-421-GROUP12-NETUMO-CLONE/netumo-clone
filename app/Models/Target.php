<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Target extends Model
{
    protected $fillable = ['name', 'url', 'user_id'];

    public function statuses(): HasMany
    {
        return $this->hasMany(Status::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
