<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'status_code' => $this->status_code,
            'latency' => $this->latency,
            'checked_at' => $this->checked_at->format('D d M, Y H:s'),
        ];
    }

    public function jsonOptions(): int
    {
        return JSON_UNESCAPED_SLASHES;
    }
}
