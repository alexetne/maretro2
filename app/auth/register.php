<?php
// register.php
declare(strict_types=1);

$pageTitle = 'Inscription • RetroPodo';
require dirname(__DIR__, 2) . '/header.php';

/**
 * Charge un .env simple (sans composer).
 * Supporte: KEY=value, KEY="value", ignore les lignes vides/#.
 */
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

$ENV = load_env(dirname(__DIR__, 2) . '/.env');

function envv(array $ENV, string $key, ?string $default = null): ?string {
  return $ENV[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
}

function pdo_conn(array $ENV): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $host = envv($ENV, 'DB_HOST', '127.0.0.1');
  $port = envv($ENV, 'DB_PORT', '3306');
  $db   = envv($ENV, 'DB_DATABASE', 'retro_podo');
  $user = envv($ENV, 'DB_USERNAME', 'retro_user');
  $pass = envv($ENV, 'DB_PASSWORD', 'retro_password');

  $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  return $pdo;
}

function normalize_email(string $email): string {
  $email = trim($email);
  $email = mb_strtolower($email, 'UTF-8');
  return $email;
}

function password_algo(array $ENV): int|string {
  $pref = strtolower(envv($ENV, 'PASSWORD_HASH_ALGO', 'argon2id') ?? 'argon2id');
  if ($pref === 'argon2id' && defined('PASSWORD_ARGON2ID')) return PASSWORD_ARGON2ID;
  if ($pref === 'argon2i' && defined('PASSWORD_ARGON2I')) return PASSWORD_ARGON2I;
  return PASSWORD_BCRYPT;
}

$errors = [];
$success = null;
$verifyPreviewLink = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // CSRF
  $csrf = $_POST['csrf_token'] ?? '';
  if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
    $errors[] = "Session expirée (CSRF). Recharge la page et réessaie.";
  }

  $email = trim((string)($_POST['email'] ?? ''));
  $first = trim((string)($_POST['first_name'] ?? ''));
  $last  = trim((string)($_POST['last_name'] ?? ''));
  $pass1 = (string)($_POST['password'] ?? '');
  $pass2 = (string)($_POST['password_confirm'] ?? '');

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
  if (mb_strlen($pass1) < 10) $errors[] = "Mot de passe trop court (10 caractères minimum).";
  if ($pass1 !== $pass2) $errors[] = "Les mots de passe ne correspondent pas.";

  if (!$errors) {
    $pdo = pdo_conn($ENV);
    $emailNorm = normalize_email($email);

    // Token vérif email (brut envoyé par mail, hash stocké)
    $rawToken  = bin2hex(random_bytes(32)); // 64 chars
    $tokenHash = hash('sha256', $rawToken);
    $ttlMin = (int)(envv($ENV, 'EMAIL_VERIFY_TOKEN_TTL_MIN', '60') ?? '60');
    $expiresAt = (new DateTimeImmutable())->modify("+{$ttlMin} minutes")->format('Y-m-d H:i:s');

    $algo = password_algo($ENV);
    $hash = password_hash($pass1, $algo);

    try {
      $pdo->beginTransaction();

      // Insert user
      $stmt = $pdo->prepare("
        INSERT INTO users (email, email_normalized, email_verified, password_hash, first_name, last_name, role, status)
        VALUES (:email, :email_norm, 0, :phash, :first, :last, 'user', 'active')
      ");
      $stmt->execute([
        ':email' => $email,
        ':email_norm' => $emailNorm,
        ':phash' => $hash,
        ':first' => ($first !== '' ? $first : null),
        ':last'  => ($last !== '' ? $last : null),
      ]);
      $userId = (int)$pdo->lastInsertId();

      // Log event register
      $stmt = $pdo->prepare("
        INSERT INTO auth_events (user_id, event_type, email_normalized, success, request_ip, user_agent, details)
        VALUES (:uid, 'register', :email_norm, 1, :ip, :ua, JSON_OBJECT('source','register.php'))
      ");
      $stmt->execute([
        ':uid' => $userId,
        ':email_norm' => $emailNorm,
        ':ip' => null, // tu peux mettre INET6_ATON($_SERVER['REMOTE_ADDR']) côté SQL si tu veux
        ':ua' => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
      ]);

      // Create email verification token
      $stmt = $pdo->prepare("
        INSERT INTO email_verification_tokens (user_id, token_hash, expires_at, request_ip, user_agent)
        VALUES (:uid, :th, :exp, :ip, :ua)
      ");
      $stmt->execute([
        ':uid' => $userId,
        ':th'  => $tokenHash,
        ':exp' => $expiresAt,
        ':ip'  => null,
        ':ua'  => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
      ]);

      // Setup: créer un cabinet + un acteur titulaire (première configuration)
      $cabName = "Cabinet principal";
      $stmt = $pdo->prepare("
        INSERT INTO cabinets (owner_user_id, name, is_active)
        VALUES (:uid, :name, 1)
      ");
      $stmt->execute([':uid' => $userId, ':name' => $cabName]);
      $cabinetId = (int)$pdo->lastInsertId();

      $display = trim(($first ?: '') . ' ' . ($last ?: ''));
      if ($display === '') $display = 'Titulaire';

      $stmt = $pdo->prepare("
        INSERT INTO cabinet_actors (cabinet_id, type, display_name, first_name, last_name, email, is_active)
        VALUES (:cid, 'titulaire', :disp, :first, :last, :email, 1)
      ");
      $stmt->execute([
        ':cid' => $cabinetId,
        ':disp' => $display,
        ':first' => ($first !== '' ? $first : null),
        ':last'  => ($last !== '' ? $last : null),
        ':email' => $email,
      ]);

      $pdo->commit();

      // Preview lien (en local), plus tard tu enverras par SMTP
      $appUrl = rtrim(envv($ENV, 'APP_URL', 'http://localhost:8000') ?? 'http://localhost:8000', '/');
      $verifyPreviewLink = $appUrl . "/auth/verify_email.php?token=" . urlencode($rawToken);

      $success = "Compte créé ✅. Cabinet + titulaire initialisés. Il reste à vérifier l’email.";
    } catch (PDOException $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();

      // 1062 = duplicate entry (email_normalized unique)
      if ((int)($e->errorInfo[1] ?? 0) === 1062) {
        $errors[] = "Cet email est déjà utilisé.";
      } else {
        $errors[] = "Erreur DB: " . $e->getMessage();
      }
    }
  }
}
?>

<div class="grid grid-2">
  <section class="card">
    <h2 class="no-top-margin">Créer un compte</h2>
    <p class="muted">Inscription + création automatique de ton cabinet et de ton acteur “titulaire”.</p>

    <?php if ($errors): ?>
      <div class="error">
        <strong>Oups :</strong>
        <ul class="list-compact">
          <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="spacer"></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success">
        <strong><?= e($success) ?></strong>
        <div class="muted">Un email de vérification vient d’être envoyé. Clique sur le lien reçu pour valider ton compte.</div>
        <?php if ($verifyPreviewLink): ?>
          <div class="spacer"></div>
          <div class="muted">Dev : lien direct de vérification</div>
          <div><a href="<?= e($verifyPreviewLink) ?>"><?= e($verifyPreviewLink) ?></a></div>
        <?php endif; ?>
      </div>
      <div class="spacer"></div>
    <?php endif; ?>

    <form method="post" autocomplete="on">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

      <label for="email">Email</label>
      <input id="email" name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>">

      <div class="spacer"></div>

      <div class="grid grid-2">
        <div>
          <label for="first_name">Prénom</label>
          <input id="first_name" name="first_name" type="text" value="<?= e($_POST['first_name'] ?? '') ?>">
        </div>
        <div>
          <label for="last_name">Nom</label>
          <input id="last_name" name="last_name" type="text" value="<?= e($_POST['last_name'] ?? '') ?>">
        </div>
      </div>

      <div class="spacer"></div>

      <label for="password">Mot de passe</label>
      <input id="password" name="password" type="password" required minlength="10">

      <div class="spacer"></div>

      <label for="password_confirm">Confirmer le mot de passe</label>
      <input id="password_confirm" name="password_confirm" type="password" required minlength="10">

      <div class="spacer"></div>

      <button class="btn" type="submit">Créer le compte</button>
    </form>
  </section>

  <aside class="card">
    <h3 class="no-top-margin">Prochaines étapes</h3>
    <ol class="muted">
      <li>Créer <code>verify_email.php</code> (valider le token et passer <code>email_verified=1</code>)</li>
      <li>Créer <code>login.php</code> (connexion + logs + lockout + MFA)</li>
      <li>Écran “setup” cabinet (infos, moyens de paiement, acteurs…)</li>
    </ol>

    <div class="spacer"></div>

    <div class="muted">
      Astuce : en prod, le lien de vérif partira via SMTP (config `.env`).  
      Là on l’affiche juste pour avancer vite.
    </div>
  </aside>
</div>

<?php require dirname(__DIR__, 2) . '/footer.php'; ?>
