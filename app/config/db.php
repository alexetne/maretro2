<?php

require_once __DIR__ . '/config.php';

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $host   = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port   = $_ENV['DB_PORT'] ?? '3306';
        $dbname = $_ENV['DB_DATABASE'];
        $user   = $_ENV['DB_USERNAME'];
        $pass   = $_ENV['DB_PASSWORD'];

        // Avoid missing socket errors when host is "localhost" by forcing TCP;
        // allow explicit socket usage via DB_SOCKET if provided.
        if (!empty($_ENV['DB_SOCKET'])) {
            $dsn = "mysql:unix_socket={$_ENV['DB_SOCKET']};dbname=$dbname;charset=utf8mb4";
        } else {
            $safeHost = ($host === 'localhost') ? '127.0.0.1' : $host;
            $dsn = "mysql:host=$safeHost;port=$port;dbname=$dbname;charset=utf8mb4";
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => false
        ];

        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public static function getConnection()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance->pdo;
    }
}
