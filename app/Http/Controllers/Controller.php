<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Netumo Clone API",
 *     description="API documentation for uptime monitoring system",
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     in="header",
 *     name="Authorization"
 * )
 *
 * @OA\Response(
 *     response="UnauthorizedError",
 *     description="Unauthorized",
 *     @OA\JsonContent(
 *         @OA\Property(property="message", type="string", example="Unauthorized")
 *     )
 * )
 *
 * @OA\Response(
 *     response="ValidationError",
 *     description="Validation Error",
 *     @OA\JsonContent(
 *         @OA\Property(property="message", type="string", example="The given data was invalid."),
 *         @OA\Property(property="errors", type="object")
 *     )
 * )
 */
abstract class Controller
{
    // Base logic (if any)
}
