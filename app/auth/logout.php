<?php
declare(strict_types=1);

require dirname(__DIR__, 2) . '/config.php';

$userId = $_SESSION['user_id'] ?? null;
$emailNorm = isset($_SESSION['email']) ? normalize_email((string)$_SESSION['email']) : null;

if ($userId) {
  log_auth_event((int)$userId, 'logout', $emailNorm, true, ['source' => 'logout.php']);
}

// Destroy session
$_SESSION = [];

if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], (bool)$params["secure"], (bool)$params["httponly"]);
}

session_destroy();

header('Location: login.php');
exit;
