<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlertResource extends JsonResource
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
            'target_id' => $this->target_id,
            'type' => $this->type,
            'message' => $this->message,
            'created_at' => $this->created_at->format('D d M, Y H:s'),
        ];
    }

    public function jsonOptions(): int
    {
        return JSON_UNESCAPED_SLASHES;
    }
}
