<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TargetResource;
use App\Models\Target;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Annotations as OA;

class TargetController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/targets",
     *     tags={"Targets"},
     *     summary="List all targets",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TargetResource")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedError")
     * )
     */
    public function index()
    {
        try {
            return TargetResource::collection(Target::all());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Server error occurred'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/targets",
     *     tags={"Targets"},
     *     summary="Create a new target",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TargetResource")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Target created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TargetResource")
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError"),
     *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedError")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
        ]);

        try {
            return new TargetResource(Target::create($data));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Server error occurred'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/targets/{id}",
     *     tags={"Targets"},
     *     summary="Get target by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/TargetResource")),
     *     @OA\Response(response=404, description="Target not found"),
     *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedError")
     * )
     */
    public function show(Target $target)
    {
        return new TargetResource($target);
    }

    /**
     * @OA\Put(
     *     path="/api/targets/{id}",
     *     tags={"Targets"},
     *     summary="Update target",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/TargetResource")),
     *     @OA\Response(response=200, description="Target updated successfully", @OA\JsonContent(ref="#/components/schemas/TargetResource")),
     *     @OA\Response(response=404, description="Target not found"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError"),
     *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedError")
     * )
     */
    public function update(Request $request, Target $target)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'url' => 'sometimes|url|max:255',
        ]);

        $target->update($data);
        return new TargetResource($target);
    }

    /**
     * @OA\Delete(
     *     path="/api/targets/{id}",
     *     tags={"Targets"},
     *     summary="Delete target",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="No content"),
     *     @OA\Response(response=404, description="Target not found"),
     *     @OA\Response(response=401, ref="#/components/responses/UnauthorizedError")
     * )
     */
    public function destroy(Target $target)
    {
        $target->delete();
        return response()->json(null, 204);
    }
}

/**
 * @OA\Schema(
 *     schema="TargetResource",
 *     type="object",
 *     required={"id", "name", "url"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Example Website"),
 *     @OA\Property(property="url", type="string", example="https://example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-06-04T12:00:00Z")
 * )
 */
