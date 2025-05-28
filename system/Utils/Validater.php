<?php

namespace System\Utils;


class Validator
{
    public static function isWord($value): bool
    {
        return preg_match(VALID_WORD, $value) === 1;
    }

    public static function isNumber($value): bool
    {
        return is_string($value);
    }


    public static function isInteger($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public static function isFloat($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    public static function isDecimal($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    public static function isBoolean($value): bool
    {
        return is_bool($value) || in_array(strtolower($value), ['true', 'false', '1', '0'], true);
    }

    public static function isDate($value, $format = 'Y-m-d'): bool
    {
        $dateTime = \DateTime::createFromFormat($format, $value);
        return $dateTime && $dateTime->format($format) === $value;
    }

    public static function isEmail($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isUrl($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public static function isIpAddress($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    public static function isAlpha($value): bool
    {
        return ctype_alpha($value);
    }

    public static function isAlphanumeric($value): bool
    {
        return ctype_alnum($value);
    }

    public static function minLength($value, $min): bool
    {
        return strlen($value) >= $min;
    }

    public static function maxLength($value, $max): bool
    {
        return strlen($value) <= $max;
    }

    public static function between($value, $min, $max): bool
    {
        return $value >= $min && $value <= $max;
    }

    public static function matches($value, $pattern): bool
    {
        return preg_match($pattern, $value) === 1;
    }
}
?>