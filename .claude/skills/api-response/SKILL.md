---
name: api-response
description: Formato único de respuesta de la API (éxito y error) y cómo crear un endpoint nuevo con ApiResponse, DomainException y códigos de error. Usar al crear o modificar cualquier controller/endpoint de la API.
---

# API Response

Toda respuesta bajo `/api/*` usa el mismo envoltorio. Nunca usar `response()->json()` ni devolver modelos directo.

## Formato

Éxito (`ApiResponse::ok`, `created`):

```json
{ "timestamp": "2026-11-14T18:00:00Z", "code": 200, "status": "success", "request_id": "uuid", "data": {}, "meta": {} }
```

- `meta` solo existe si se pasa (paginación).
- `noContent()` devuelve 204 sin cuerpo.

Error (`ApiResponse::error`, normalmente vía excepción):

```json
{ "timestamp": "...", "code": 422, "status": "error", "request_id": "uuid",
  "error": { "code": "VALIDATION_FAILED", "fields": { "correo": ["INVALID_FORMAT"] } } }
```

- El backend **nunca** envía textos para el usuario: solo códigos (`error.code`, `fields`). El cliente los traduce.
- `request_id` también viaja en el header `X-Request-Id` y en los logs.

## Piezas (`app/Core`)

| Archivo | Rol |
|---|---|
| `Responses/ApiResponse.php` | Único constructor de respuestas |
| `Errors/DomainException.php` | Error esperado de negocio; lleva un código, no un texto |
| `Errors/ErrorCode.php` | Códigos generales (`NOT_FOUND`, `VALIDATION_FAILED`...) |
| `Errors/DomainErrorCode.php` | Contrato de los enums de códigos |
| `Errors/ErrorCategory.php` | Categoría → status HTTP (solo `ExceptionRenderer` la traduce) |
| `Errors/FieldErrorCode.php` | Reglas de validación de Laravel → `REQUIRED`, `TOO_LONG`, etc. |
| `Errors/ExceptionRenderer.php` | Convierte cualquier `Throwable` en respuesta estándar |
| `Http/Middleware/AssignRequestId.php` | Genera `request_id` |
| `Http/Middleware/ForceJsonResponse.php` | Fuerza `Accept: application/json` en `/api/*` |

El registro está en `bootstrap/app.php`.

## Crear un endpoint

1. Ruta en `routes/api.php` (prefijo `/api` automático).
2. Validación con un Form Request; los fallos salen solos como `VALIDATION_FAILED` con `fields`.
3. Controller delgado: llama a la lógica y retorna `ApiResponse::ok(...)` / `created(...)` / `noContent()`. Tipo de retorno `JsonResponse`.
4. Errores de negocio: `throw new DomainException(ErrorCode::NotFound)`. Nunca `abort()` con texto ni `response()->json(['error' => ...])`.
5. Mapear modelo → array con un API Resource antes de pasarlo a `ApiResponse`, para no exponer columnas internas.

```php
public function mostrar(int $id): JsonResponse
{
    $cliente = Cliente::find($id) ?? throw new DomainException(ErrorCode::NotFound);

    return ApiResponse::ok($cliente);
}
```

## Códigos de error por módulo

Si un feature necesita errores propios, crear un enum que implemente `DomainErrorCode`:

```php
enum ClienteErrorCode: string implements DomainErrorCode
{
    case DocumentoDuplicado = 'DOCUMENTO_DUPLICADO';

    public function code(): string { return $this->value; }

    public function category(): ErrorCategory { return ErrorCategory::BusinessRule; }
}
```

Categorías: `BadRequest` 400, `Unauthenticated` 401, `Forbidden` 403, `NotFound` 404, `MethodNotAllowed` 405, `Conflict` 409, `PayloadTooLarge` 413, `Validation`/`BusinessRule` 422, `RateLimited` 429, `Internal` 500.

## Reglas

- Códigos de error en `UPPER_SNAKE_CASE`, estables (son contrato con el cliente).
- El status HTTP se decide solo por la categoría, en `ExceptionRenderer`.
- Errores inesperados salen como `SERVER_ERROR` 500 sin detalles; el detalle va al log.
