<?php

declare(strict_types=1);

namespace App\Core\Responses;

use App\Core\Errors\DomainErrorCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Formato único de toda respuesta de la API (ver .claude/skills/api-response/SKILL.md).
 */
final class ApiResponse
{
    private const int JSON_OPTIONS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    /**
     * @param  array<string, mixed>|null  $meta  Datos de paginación; se omite si es null.
     */
    public static function ok(mixed $data = null, int $status = Response::HTTP_OK, ?array $meta = null): JsonResponse
    {
        $body = self::envelope($status, 'success') + ['data' => $data];

        if ($meta !== null) {
            $body['meta'] = $meta;
        }

        return new JsonResponse($body, $status, [], self::JSON_OPTIONS);
    }

    public static function created(mixed $data): JsonResponse
    {
        return self::ok($data, Response::HTTP_CREATED);
    }

    public static function noContent(): Response
    {
        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    /**
     * @param  array<string, list<string>>  $fields  Códigos de error por campo; se omite si está vacío.
     * @param  array<string, mixed>  $headers
     */
    public static function error(DomainErrorCode $errorCode, int $status, array $fields = [], array $headers = []): JsonResponse
    {
        $error = ['code' => $errorCode->code()];

        if ($fields !== []) {
            $error['fields'] = $fields;
        }

        return new JsonResponse(self::envelope($status, 'error') + ['error' => $error], $status, $headers, self::JSON_OPTIONS);
    }

    /**
     * @return array{timestamp: string, code: int, status: string, request_id: string|null}
     */
    private static function envelope(int $status, string $outcome): array
    {
        $requestId = request()->attributes->get('request_id');

        return [
            'timestamp' => now('UTC')->format('Y-m-d\TH:i:s\Z'),
            'code' => $status,
            'status' => $outcome,
            'request_id' => is_string($requestId) ? $requestId : null,
        ];
    }
}
