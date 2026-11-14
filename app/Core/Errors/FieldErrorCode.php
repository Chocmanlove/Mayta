<?php

declare(strict_types=1);

namespace App\Core\Errors;

/**
 * Códigos de error por campo en las respuestas de validación.
 */
enum FieldErrorCode: string
{
    case Required = 'REQUIRED';
    case TooShort = 'TOO_SHORT';
    case TooLong = 'TOO_LONG';
    case InvalidFormat = 'INVALID_FORMAT';
    case InvalidValue = 'INVALID_VALUE';
    case NotUnique = 'NOT_UNIQUE';

    private const array FORMAT_RULES = [
        'Email', 'Regex', 'NotRegex', 'Date', 'DateFormat', 'Uuid', 'Ulid', 'Url', 'Ip', 'Json', 'Timezone',
        'Alpha', 'AlphaNum', 'AlphaDash', 'Lowercase', 'Uppercase', 'Digits', 'DigitsBetween', 'Integer',
        'Numeric', 'Decimal', 'String', 'Boolean', 'Array', 'File', 'Image', 'Mimes', 'Mimetypes',
    ];

    /**
     * Traduce el nombre de una regla de validación de Laravel (ej. "Required", "Max") a un código.
     * La regla Password solo se usa con longitud mínima, por eso equivale a TOO_SHORT.
     */
    public static function fromRule(string $rule): self
    {
        $rule = class_basename($rule);

        return match (true) {
            str_starts_with($rule, 'Required'), in_array($rule, ['Present', 'Filled'], true) => self::Required,
            $rule === 'Min', $rule === 'Password' => self::TooShort,
            $rule === 'Max' => self::TooLong,
            $rule === 'Unique' => self::NotUnique,
            in_array($rule, self::FORMAT_RULES, true) => self::InvalidFormat,
            default => self::InvalidValue,
        };
    }
}
