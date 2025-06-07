<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StatusResource;
use App\Models\Target;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function latest(Target $target)
    {
        $latest = $target->statuses()->latest('checked_at')->first();
        return new StatusResource($latest);
    }

    public function history(Target $target)
    {
        $since = now()->subDay(); // past 24 hours
        $history = $target->statuses()
            ->where('checked_at', '>=', $since)
            ->orderBy('checked_at')
            ->get();
        return StatusResource::collection($history);
    }
}
