<?php
declare(strict_types=1);

/**
 * Send a JSON response with given data and status code.
 *
 * @param array $data Response payload.
 * @param int $statusCode HTTP status code.
 */
function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Shortcut for an error JSON response.
 *
 * @param string $message Error message.
 * @param int $statusCode HTTP status code.
 */
function errorResponse(string $message, int $statusCode = 400): void
{
    jsonResponse(['error' => $message], $statusCode);
}
