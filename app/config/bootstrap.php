<?php
declare(strict_types=1);

// Application bootstrapper: loads environment, configuration, session, DB and helpers.

$rootPath = dirname(__DIR__, 2);

// Composer autoload (required for PHPMailer, Dotenv, etc.)
$autoloadPath = $rootPath . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    throw new RuntimeException('Composer autoload not found. Run `composer install`.');
}
require_once $autoloadPath;

// Load environment variables from .env if present
if (class_exists(\Dotenv\Dotenv::class)) {
    $dotenv = Dotenv\Dotenv::createImmutable($rootPath);
    $dotenv->safeLoad();
}

// Load configuration
$config = require __DIR__ . '/config.php';

// Set timezone early so logs/timestamps are consistent
if (!empty($config['app']['timezone'])) {
    date_default_timezone_set($config['app']['timezone']);
}

// Configure and start secure session
require_once __DIR__ . '/session.php';
configureSession($config['session']);
startSession();

// Prepare database access (singleton pattern inside db.php)
require_once __DIR__ . '/db.php';
Database::init($config['database']);

// Authentication & security helpers
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/roles.php';

// Mailer configuration
require_once __DIR__ . '/mail.php';

// Optional: load global helper functions if you have them
$helperPath = $rootPath . '/app/helpers/helpers.php';
if (file_exists($helperPath)) {
    require_once $helperPath;
}

return $config;
