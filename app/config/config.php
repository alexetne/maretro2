<?php
declare(strict_types=1);

// Global configuration loader with env() helper

if (!function_exists('env')) {
    /**
     * Read an environment variable with optional default.
     */
    function env(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }

        // Normalize booleans and numeric strings when default hints type
        if (is_bool($default)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
        }
        if (is_int($default)) {
            return (int)$value;
        }
        if (is_float($default)) {
            return (float)$value;
        }

        return $value === '' ? $default : $value;
    }
}

return [
    'app' => [
        'name' => env('APP_NAME', 'MyApp'),
        'env' => env('APP_ENV', 'production'),
        'debug' => env('APP_DEBUG', false),
        'url' => env('APP_URL', 'http://localhost'),
        'timezone' => env('APP_TIMEZONE', 'UTC'),
    ],

    'database' => [
        // Default to pgsql per requirements; override to mysql via DB_DRIVER env
        'driver' => env('DB_DRIVER', 'pgsql'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', env('DB_DRIVER', 'pgsql') === 'pgsql' ? '5432' : '3306'),
        'database' => env('DB_DATABASE', 'app'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => env('DB_CHARSET', 'utf8'),
        'sslmode' => env('DB_SSLMODE', 'prefer'),
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ],

    'mail' => [
        'smtp_host' => env('SMTP_HOST', 'localhost'),
        'smtp_port' => (int)env('SMTP_PORT', 587),
        'smtp_user' => env('SMTP_USER', ''),
        'smtp_password' => env('SMTP_PASSWORD', ''),
        'smtp_secure' => env('SMTP_SECURE', 'tls'),
        'from_address' => env('MAIL_FROM_ADDRESS', 'no-reply@example.com'),
        'from_name' => env('MAIL_FROM_NAME', env('APP_NAME', 'MyApp')),
    ],

    'session' => [
        'name' => env('SESSION_NAME', 'app_session'),
        'lifetime' => (int)env('SESSION_LIFETIME', 1200), // seconds
        'secure' => env('SESSION_SECURE', false),
        'httponly' => true,
        'samesite' => env('SESSION_SAMESITE', 'Lax'),
        'path' => '/',
        'domain' => env('SESSION_DOMAIN', ''),
    ],
];
