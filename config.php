<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

function e(string $v): string {
  return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Charge un .env simple (sans composer) */
function load_env(string $path): array {
  if (!is_file($path)) return [];
  $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  $env = [];
  foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) continue;
    if (!str_contains($line, '=')) continue;

    [$k, $v] = explode('=', $line, 2);
    $k = trim($k);
    $v = trim($v);

    if ((str_starts_with($v, '"') && str_ends_with($v, '"')) || (str_starts_with($v, "'") && str_ends_with($v, "'"))) {
      $v = substr($v, 1, -1);
    }
    $env[$k] = $v;
  }
  return $env;
}

$ENV = load_env(__DIR__ . '/.env');

function envv(string $key, ?string $default = null): ?string {
  global $ENV;
  return $ENV[$key] ?? $_ENV[$key] ?? (getenv($key) !== false ? (string)getenv($key) : $default);
}

function pdo_conn(): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $host = envv('DB_HOST', '127.0.0.1');
  $port = envv('DB_PORT', '3306');
  $db   = envv('DB_DATABASE', 'retro_podo');
  $user = envv('DB_USERNAME', 'retro_user');
  $pass = envv('DB_PASSWORD', 'retro_password');

  $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  return $pdo;
}

function normalize_email(string $email): string {
  return mb_strtolower(trim($email), 'UTF-8');
}

/** CSRF */
function csrf_token(): string {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

function csrf_check(string $token): bool {
  return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/** IP helper (stockage VARBINARY(16) en DB) */
function client_ip_bin(): ?string {
  $ip = $_SERVER['REMOTE_ADDR'] ?? null;
  if (!$ip) return null;
  $bin = @inet_pton($ip);     // IPv4/IPv6
  return $bin === false ? null : $bin;
}

/** Log auth_event */
function log_auth_event(?int $userId, string $type, ?string $emailNorm, bool $success, array $details = []): void {
  $pdo = pdo_conn();
  $stmt = $pdo->prepare("
    INSERT INTO auth_events (user_id, event_type, email_normalized, success, request_ip, user_agent, details)
    VALUES (:uid, :etype, :email, :success, :ip, :ua, :details)
  ");
  $stmt->execute([
    ':uid' => $userId,
    ':etype' => $type,
    ':email' => $emailNorm,
    ':success' => $success ? 1 : 0,
    ':ip' => client_ip_bin(),
    ':ua' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
    ':details' => $details ? json_encode($details, JSON_UNESCAPED_UNICODE) : null,
  ]);
}

/** Génère token brut + hash */
function make_token_pair(int $bytes = 32): array {
  $raw = bin2hex(random_bytes($bytes)); // brut à envoyer
  $hash = hash('sha256', $raw);
  return [$raw, $hash];
}
