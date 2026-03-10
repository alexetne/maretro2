<?php
declare(strict_types=1);

// Database connection manager using PDO (PostgreSQL by default)

final class Database
{
    private static ?PDO $pdo = null;
    private static array $config = [];

    /**
     * Initialize configuration once during bootstrap.
     */
    public static function init(array $config): void
    {
        self::$config = $config;
    }

    /**
     * Get a singleton PDO instance.
     */
    public static function getPDO(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        if (empty(self::$config)) {
            throw new RuntimeException('Database config not loaded. Call Database::init() first.');
        }

        $driver = self::$config['driver'] ?? 'pgsql';
        $host = self::$config['host'] ?? '127.0.0.1';
        $port = self::$config['port'] ?? ($driver === 'pgsql' ? '5432' : '3306');
        $db = self::$config['database'] ?? '';
        $charset = self::$config['charset'] ?? 'utf8';
        $sslmode = self::$config['sslmode'] ?? 'prefer';

        if ($driver === 'pgsql') {
            $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s;options=--client_encoding=%s;sslmode=%s', $host, $port, $db, $charset, $sslmode);
        } else {
            // Fallback for MySQL deployments
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $db, $charset);
        }

        $options = self::$config['options'] ?? [];
        $username = self::$config['username'] ?? '';
        $password = self::$config['password'] ?? '';

        self::$pdo = new PDO($dsn, $username, $password, $options);
        return self::$pdo;
    }
}

// Convenience wrapper
function db(): PDO
{
    return Database::getPDO();
}
