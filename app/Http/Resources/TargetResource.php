<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="TargetResource",
 *     title="Target Resource",
 *     description="Target resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Target unique identifier",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Target name",
 *         example="My Website"
 *     ),
 *     @OA\Property(
 *         property="url",
 *         type="string",
 *         format="url",
 *         description="Target URL to monitor",
 *         example="https://example.com"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="datetime",
 *         description="Target creation date and time",
 *         example="Mon 04 Jun, 2025 20:30"
 *     )
 * )
 */
class TargetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner' => User::where('id', $this->user_id)->firstOrFail()->name,
            'name' => $this->name,
            'url' => $this->url,
            'created_at' => $this->created_at->format('D d M, Y H:s'),
        ];
    }

    public function jsonOptions(): int
    {
        return JSON_UNESCAPED_SLASHES;
    }
}
