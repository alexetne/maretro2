<?php
// Simple DB connectivity check script

// Bootstrap application (adjust relative path from /public)
require_once __DIR__ . '../app/config/bootstrap.php';

// Obtain PDO instance (singleton)
$pdo = Database::getPDO();

echo "Connexion DB OK";
