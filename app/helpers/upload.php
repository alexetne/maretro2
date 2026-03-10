<?php
declare(strict_types=1);

/**
 * Validate an uploaded file array.
 *
 * @param array $file Single file from $_FILES.
 * @return bool
 */
function validateUpload(array $file): bool
{
    if (!isset($file['error'], $file['size'], $file['name'])) {
        return false;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Limit size to 5MB
    $maxSize = 5 * 1024 * 1024;
    if ((int)$file['size'] > $maxSize) {
        return false;
    }

    // Allow common safe extensions; adjust as needed
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    return in_array($ext, $allowed, true);
}

/**
 * Move an uploaded file to destination directory.
 *
 * @param array $file Single file from $_FILES.
 * @param string $destination Target directory path (must be writable).
 * @return string|null Final stored filepath or null on failure.
 */
function uploadFile(array $file, string $destination): ?string
{
    if (!validateUpload($file)) {
        return null;
    }

    if (!is_dir($destination) || !is_writable($destination)) {
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $safeName = bin2hex(random_bytes(16)) . '.' . $ext;
    $targetPath = rtrim($destination, '/\\') . DIRECTORY_SEPARATOR . $safeName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $targetPath;
    }

    return null;
}
