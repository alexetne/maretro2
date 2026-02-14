<?php
declare(strict_types=1);

require dirname(__DIR__, 2) . '/config.php';

$pageTitle = 'Connexion • Ma Rétro Podo';
require dirname(__DIR__, 2) . '/header.php';

$errors = [];
$success = null;

if (!empty($_SESSION['user_id'])) {
  $success = "Tu es déjà connecté.";
}

function redirect(string $to): never {
  header("Location: {$to}");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_SESSION['user_id'])) {
  $csrf = (string)($_POST['csrf_token'] ?? '');
  if (!csrf_check($csrf)) {
    $errors[] = "Session expirée (CSRF). Recharge la page.";
  }

  $email = trim((string)($_POST['email'] ?? ''));
  $password = (string)($_POST['password'] ?? '');

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
  if ($password === '') $errors[] = "Mot de passe requis.";

  if (!$errors) {
    $pdo = pdo_conn();
    $emailNorm = normalize_email($email);

    $stmt = $pdo->prepare("
      SELECT id, email, email_normalized, email_verified, password_hash,
             status, failed_login_count, locked_until
      FROM users
      WHERE email_normalized = :e
      LIMIT 1
    ");
    $stmt->execute([':e' => $emailNorm]);
    $u = $stmt->fetch();

    // Ne pas révéler si le compte existe
    $genericFail = "Identifiants incorrects.";

    if (!$u) {
      log_auth_event(null, 'login_failed', $emailNorm, false, ['reason' => 'unknown_email']);
      $errors[] = $genericFail;
    } else {
      $userId = (int)$u['id'];

      // status
      if (($u['status'] ?? 'active') !== 'active') {
        log_auth_event($userId, 'login_failed', $emailNorm, false, ['reason' => 'status_' . $u['status']]);
        $errors[] = "Compte désactivé.";
      } else {
        // lockout
        if (!empty($u['locked_until']) && strtotime((string)$u['locked_until']) > time()) {
          log_auth_event($userId, 'login_failed', $emailNorm, false, ['reason' => 'locked']);
          $errors[] = "Compte temporairement bloqué. Réessaie plus tard.";
        } else {
          // Vérif password
          if (!password_verify($password, (string)$u['password_hash'])) {
            $maxAttempts = (int)(envv('LOGIN_MAX_ATTEMPTS', '5') ?? '5');
            $lockMinutes = (int)(envv('LOGIN_LOCK_MINUTES', '15') ?? '15');

            $newFail = (int)$u['failed_login_count'] + 1;
            $lockedUntil = null;

            if ($newFail >= $maxAttempts) {
              $lockedUntil = (new DateTimeImmutable())->modify("+{$lockMinutes} minutes")->format('Y-m-d H:i:s');
            }

            $stmt = $pdo->prepare("
              UPDATE users
              SET failed_login_count = :fc,
                  locked_until = :lu
              WHERE id = :uid
            ");
            $stmt->execute([
              ':fc' => $newFail,
              ':lu' => $lockedUntil,
              ':uid' => $userId,
            ]);

            log_auth_event($userId, 'login_failed', $emailNorm, false, [
              'reason' => 'bad_password',
              'failed_count' => $newFail,
              'locked_until' => $lockedUntil
            ]);

            if ($lockedUntil) {
              log_auth_event($userId, 'account_locked', $emailNorm, true, ['locked_until' => $lockedUntil]);
              $errors[] = "Trop d’essais. Compte bloqué temporairement.";
            } else {
              $errors[] = $genericFail;
            }
          } else {
            // email verified ?
            if ((int)$u['email_verified'] !== 1) {
              log_auth_event($userId, 'login_failed', $emailNorm, false, ['reason' => 'email_not_verified']);
              $errors[] = "Email non vérifié. Vérifie ta boîte mail ou renvoie un lien.";
            } else {
              // OK -> reset fails + last_login
              $stmt = $pdo->prepare("
                UPDATE users
                SET failed_login_count = 0,
                    locked_until = NULL,
                    last_login_at = NOW()
                WHERE id = :uid
              ");
              $stmt->execute([':uid' => $userId]);

              // Session
              session_regenerate_id(true);
              $_SESSION['user_id'] = $userId;
              $_SESSION['email'] = (string)$u['email'];
              $_SESSION['role'] = (string)$u['role'] ?? 'user';

              log_auth_event($userId, 'login_success', $emailNorm, true, ['source' => 'login.php']);

              redirect('../profile.php');
            }
          }
        }
      }
    }
  }
}
?>

<section class="card">
  <h2 style="margin-top:0;">Connexion</h2>
  <p class="muted">Accède à ton espace Ma Rétro Podo.</p>

  <?php if ($errors): ?>
    <div class="error">
      <ul style="margin:0 0 0 18px;">
        <?php foreach ($errors as $err): ?>
          <li><?= e($err) ?></li>
        <?php endforeach; ?>
      </ul>
      <div class="spacer"></div>
      <a class="btn" href="verify_email_resend.php">Renvoyer l’email de vérification</a>
    </div>
    <div class="spacer"></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success"><strong><?= e($success) ?></strong></div>
    <div class="spacer"></div>
  <?php endif; ?>

  <form method="post" autocomplete="on">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <label for="email">Email</label>
    <input id="email" name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>">

    <div class="spacer"></div>

    <label for="password">Mot de passe</label>
    <input id="password" name="password" type="password" required>

    <div class="spacer"></div>

    <button class="btn" type="submit">Se connecter</button>
    <span class="muted" style="margin-left:10px;">
      Pas de compte ? <a href="register.php">Créer un compte</a>
    </span>
  </form>
</section>

<?php require dirname(__DIR__, 2) . '/footer.php'; ?>
