<?php
declare(strict_types=1);

/**
 * Format a number as Euro currency.
 *
 * @param float $amount
 * @return string
 */
function formatCurrency(float $amount): string
{
    return number_format($amount, 2, ',', ' ') . ' €';
}

/**
 * Format a date (Y-m-d) into a human-readable form.
 *
 * @param string $date
 * @return string
 */
function formatDate(string $date): string
{
    $ts = strtotime($date);
    return $ts ? date('d/m/Y', $ts) : '';
}

/**
 * Format a datetime into a human-readable form.
 *
 * @param string $date
 * @return string
 */
function formatDateTime(string $date): string
{
    $ts = strtotime($date);
    return $ts ? date('d/m/Y H:i', $ts) : '';
}

/**
 * Normalize a status string for display.
 *
 * @param string $status
 * @return string
 */
function formatStatus(string $status): string
{
    $clean = str_replace('_', ' ', strtolower($status));
    return ucfirst($clean);
}
