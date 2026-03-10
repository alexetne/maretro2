<?php
declare(strict_types=1);

/**
 * Check that a value is not null/empty.
 *
 * @param mixed $value
 * @return bool
 */
function validateRequired(mixed $value): bool
{
    if (is_array($value)) {
        return !empty($value);
    }
    return isset($value) && trim((string)$value) !== '';
}

/**
 * Validate an email address.
 *
 * @param string $email
 * @return bool
 */
function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate numeric value.
 *
 * @param mixed $value
 * @return bool
 */
function validateNumeric(mixed $value): bool
{
    return is_numeric($value);
}

/**
 * Validate minimum string length.
 *
 * @param string $value
 * @param int $length Minimum characters.
 * @return bool
 */
function validateMinLength(string $value, int $length): bool
{
    return mb_strlen($value) >= $length;
}
