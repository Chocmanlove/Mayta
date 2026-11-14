<?php

declare(strict_types=1);

namespace App\Core\Errors;

use RuntimeException;
use Throwable;

/**
 * Error esperado del negocio. Lleva un código, nunca un texto para el usuario.
 */
final class DomainException extends RuntimeException
{
    /**
     * @param  array<string, list<string>>  $fields  Códigos de error por campo, si aplica.
     * @param  array<string, string>  $headers  Cabeceras HTTP extra (ej. Retry-After).
     */
    public function __construct(
        public readonly DomainErrorCode $errorCode,
        public readonly array $fields = [],
        public readonly array $headers = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($errorCode->code(), 0, $previous);
    }
}
