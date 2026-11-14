<?php

declare(strict_types=1);

namespace App\Core\Errors;

/**
 * Contrato de los enums de códigos de error (generales y de cada módulo).
 */
interface DomainErrorCode
{
    /**
     * Código que viaja en la respuesta y que el cliente traduce, ej. NOT_FOUND.
     */
    public function code(): string;

    public function category(): ErrorCategory;
}
