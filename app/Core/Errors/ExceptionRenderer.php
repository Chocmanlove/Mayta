<?php

declare(strict_types=1);

namespace App\Core\Errors;

use App\Core\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Único lugar donde un error se convierte en código HTTP y respuesta estándar.
 */
final class ExceptionRenderer
{
    public function render(Throwable $exception): JsonResponse
    {
        return match (true) {
            $exception instanceof DomainException => ApiResponse::error(
                $exception->errorCode,
                $this->statusFor($exception->errorCode->category()),
                $exception->fields,
                $exception->headers,
            ),
            $exception instanceof ValidationException => ApiResponse::error(
                ErrorCode::ValidationFailed,
                $this->statusFor(ErrorCategory::Validation),
                $this->fieldErrors($exception),
            ),
            $exception instanceof AuthenticationException => ApiResponse::error(
                ErrorCode::Unauthenticated,
                $this->statusFor(ErrorCategory::Unauthenticated),
            ),
            $exception instanceof HttpExceptionInterface => $this->renderHttpException($exception),
            default => ApiResponse::error(ErrorCode::ServerError, $this->statusFor(ErrorCategory::Internal)),
        };
    }

    private function statusFor(ErrorCategory $category): int
    {
        return match ($category) {
            ErrorCategory::BadRequest => 400,
            ErrorCategory::Unauthenticated => 401,
            ErrorCategory::Forbidden => 403,
            ErrorCategory::NotFound => 404,
            ErrorCategory::MethodNotAllowed => 405,
            ErrorCategory::Conflict => 409,
            ErrorCategory::PayloadTooLarge => 413,
            ErrorCategory::Validation, ErrorCategory::BusinessRule => 422,
            ErrorCategory::RateLimited => 429,
            ErrorCategory::Internal => 500,
        };
    }

    private function renderHttpException(HttpExceptionInterface $exception): JsonResponse
    {
        $status = $exception->getStatusCode();

        $code = match (true) {
            $status === 401 => ErrorCode::Unauthenticated,
            $status === 403 => ErrorCode::Forbidden,
            $status === 404 => ErrorCode::NotFound,
            $status === 405 => ErrorCode::MethodNotAllowed,
            $status === 413 => ErrorCode::PayloadTooLarge,
            $status === 429 => ErrorCode::RateLimited,
            $status >= 500 => ErrorCode::ServerError,
            default => ErrorCode::BadRequest,
        };

        return ApiResponse::error($code, $status, headers: $exception->getHeaders());
    }

    /**
     * @return array<string, list<string>>
     */
    private function fieldErrors(ValidationException $exception): array
    {
        $failedRules = $exception->validator->failed();

        if ($failedRules === []) {
            return array_map(
                static fn (): array => [FieldErrorCode::InvalidValue->value],
                $exception->errors(),
            );
        }

        $fields = [];

        foreach ($failedRules as $field => $rules) {
            $fields[$field] = array_values(array_unique(array_map(
                static fn (int|string $rule): string => is_string($rule)
                    ? FieldErrorCode::fromRule($rule)->value
                    : FieldErrorCode::InvalidValue->value,
                array_keys($rules),
            )));
        }

        return $fields;
    }
}
