<?php

namespace App\Traits;

/**
 * @OA\Schema(
 *     schema="OptionResource",
 *     description="A trait that provides JSON formatting options for API resources"
 * )
 */
trait OptionResource
{
    /**
     * Get JSON encoding options for the resource.
     *
     * @OA\Property(
     *     description="JSON encoding options value",
     *     type="integer"
     * )
     * @return int The JSON encoding options
     */
    public function jsonOptions(): int
    {
        return JSON_UNESCAPED_SLASHES;
    }
}
