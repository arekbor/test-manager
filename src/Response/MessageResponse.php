<?php 

declare(strict_types=1);

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MessageResponse
{
    public static function createResponse(string $message, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['message' => $message], $status);
    }
}