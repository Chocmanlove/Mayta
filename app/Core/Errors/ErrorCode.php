<?php

declare(strict_types=1);

namespace App\Core\Errors;

/**
 * Códigos de error generales de la API (ver .claude/skills/api-response/SKILL.md).
 */
enum ErrorCode: string implements DomainErrorCode
{
    case ValidationFailed = 'VALIDATION_FAILED';
    case BadRequest = 'BAD_REQUEST';
    case Unauthenticated = 'UNAUTHENTICATED';
    case TokenExpired = 'TOKEN_EXPIRED';
    case Forbidden = 'FORBIDDEN';
    case NotFound = 'NOT_FOUND';
    case MethodNotAllowed = 'METHOD_NOT_ALLOWED';
    case PayloadTooLarge = 'PAYLOAD_TOO_LARGE';
    case RateLimited = 'RATE_LIMITED';
    case ServerError = 'SERVER_ERROR';

    public function code(): string
    {
        return $this->value;
    }

    public function category(): ErrorCategory
    {
        return match ($this) {
            self::ValidationFailed => ErrorCategory::Validation,
            self::BadRequest => ErrorCategory::BadRequest,
            self::Unauthenticated, self::TokenExpired => ErrorCategory::Unauthenticated,
            self::Forbidden => ErrorCategory::Forbidden,
            self::NotFound => ErrorCategory::NotFound,
            self::MethodNotAllowed => ErrorCategory::MethodNotAllowed,
            self::PayloadTooLarge => ErrorCategory::PayloadTooLarge,
            self::RateLimited => ErrorCategory::RateLimited,
            self::ServerError => ErrorCategory::Internal,
        };
    }
}
