<?php
declare(strict_types=1);

/**
 * Sanitize a general string to prevent XSS.
 *
 * @param string $value
 * @return string
 */
function sanitizeString(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Sanitize an email string to a safe format.
 *
 * @param string $value
 * @return string
 */
function sanitizeEmail(string $value): string
{
    $value = filter_var($value, FILTER_SANITIZE_EMAIL) ?: '';
    return strtolower($value);
}

/**
 * Sanitize an integer value.
 *
 * @param mixed $value
 * @return int
 */
function sanitizeInt(mixed $value): int
{
    return (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
}
