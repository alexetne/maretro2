<?php
declare(strict_types=1);

// CSRF protection utilities

function generateCsrfToken(): string
{
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

function csrfInputField(): string
{
    $token = $_SESSION['csrf_token'] ?? generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

function verifyCsrfToken(?string $token): bool
{
    if (!isset($_SESSION['csrf_token']) || $token === null) {
        return false;
    }

    $isValid = hash_equals($_SESSION['csrf_token'], $token);

    if ($isValid) {
        // Rotate token after successful validation
        unset($_SESSION['csrf_token']);
    }

    return $isValid;
}
