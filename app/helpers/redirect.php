<?php
declare(strict_types=1);

/**
 * Issue an HTTP redirect to a target URL and stop execution.
 *
 * @param string $url Absolute or relative URL.
 */
function redirect(string $url): void
{
    header('Location: ' . $url, true, 302);
    exit;
}

/**
 * Redirect back to the referrer if available, otherwise fallback to root.
 */
function redirectBack(): void
{
    $target = $_SERVER['HTTP_REFERER'] ?? '/';
    redirect($target);
}

/**
 * Redirect with a flash-style message stored in session.
 *
 * @param string $url Target URL.
 * @param string $type Message type (e.g. success, error).
 * @param string $message Message text.
 */
function redirectWithMessage(string $url, string $type, string $message): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION['flash'][$type][] = $message;
    }
    redirect($url);
}
