<?php

declare(strict_types=1);

namespace App\Core\Errors;

/**
 * Tipo de error desde el punto de vista del negocio. Solo ExceptionRenderer lo traduce a HTTP.
 */
enum ErrorCategory
{
    case BadRequest;
    case Validation;
    case Unauthenticated;
    case Forbidden;
    case NotFound;
    case MethodNotAllowed;
    case Conflict;
    case PayloadTooLarge;
    case BusinessRule;
    case RateLimited;
    case Internal;
}
