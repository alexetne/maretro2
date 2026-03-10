<?php
declare(strict_types=1);

/**
 * Get first day of current month (YYYY-MM-DD).
 *
 * @return string
 */
function startOfMonth(): string
{
    return date('Y-m-01');
}

/**
 * Get last day of current month (YYYY-MM-DD).
 *
 * @return string
 */
function endOfMonth(): string
{
    return date('Y-m-t');
}

/**
 * Get first day of current year (YYYY-MM-DD).
 *
 * @return string
 */
function startOfYear(): string
{
    return date('Y-01-01');
}

/**
 * Get last day of current year (YYYY-MM-DD).
 *
 * @return string
 */
function endOfYear(): string
{
    return date('Y-12-31');
}
