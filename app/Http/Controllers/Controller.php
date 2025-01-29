<?php

namespace App\Http\Controllers;

/**
 * @OA\OpenApi(
 *   @OA\Info(
 *     title="Fire Prevention API",
 *     version="1.0.0",
 *     description="API documentation with JWT authentication"
 *   ),
 *   @OA\Components(
 *     @OA\SecurityScheme(
 *       securityScheme="bearerAuth",
 *       type="http",
 *       scheme="bearer",
 *       bearerFormat="JWT"
 *     )
 *   )
 * )
 */

abstract class Controller
{
    //
}
