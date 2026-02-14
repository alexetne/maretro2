<?php
declare(strict_types=1);

require dirname(__DIR__, 2) . '/config.php';
$pageTitle = 'Renvoyer la vérification • Ma Rétro Podo';
require dirname(__DIR__, 2) . '/header.php';

$errors = [];
$success = null;
$previewLink = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $csrf = (string)($_POST['csrf_token'] ?? '');
  if (!csrf_check($csrf)) {
    $errors[] = "Session expirée (CSRF). Recharge la page.";
  }

  $email = trim((string)($_POST['email'] ?? ''));
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email invalide.";
  }

  if (!$errors) {
    $pdo = pdo_conn();
    $emailNorm = normalize_email($email);

    try {
      // On ne révèle pas si l'email existe: même message dans tous les cas
      $stmt = $pdo->prepare("SELECT id, email_verified FROM users WHERE email_normalized = :e LIMIT 1");
      $stmt->execute([':e' => $emailNorm]);
      $u = $stmt->fetch();

      if ($u && (int)$u['email_verified'] === 0) {
        [$rawToken, $tokenHash] = make_token_pair(32);
        $ttlMin = (int)(envv('EMAIL_VERIFY_TOKEN_TTL_MIN', '60') ?? '60');
        $expiresAt = (new DateTimeImmutable())->modify("+{$ttlMin} minutes")->format('Y-m-d H:i:s');

        $stmt = $pdo->prepare("
          INSERT INTO email_verification_tokens (user_id, token_hash, expires_at, request_ip, user_agent)
          VALUES (:uid, :th, :exp, :ip, :ua)
        ");
        $stmt->execute([
          ':uid' => (int)$u['id'],
          ':th'  => $tokenHash,
          ':exp' => $expiresAt,
          ':ip'  => client_ip_bin(),
          ':ua'  => substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
        ]);

        log_auth_event((int)$u['id'], 'register_verify_sent', $emailNorm, true, ['source' => 'verify_email_resend.php']);

        // Mode dev: lien visible
        $appUrl = rtrim(envv('APP_URL', 'http://localhost:8000') ?? 'http://localhost:8000', '/');
        $previewLink = $appUrl . "/auth/verify_email.php?token=" . urlencode($rawToken);
      } else {
        // soit inexistant, soit déjà vérifié => on répond pareil (anti-enum)
        log_auth_event($u ? (int)$u['id'] : null, 'register_verify_sent', $emailNorm, true, ['note' => 'no_action']);
      }

      $success = "Si un compte existe avec cet email, un lien de vérification vient d’être envoyé.";
    } catch (Throwable $e) {
      $errors[] = "Erreur serveur: " . $e->getMessage();
    }
  }
}
?>

<section class="card">
  <h2 style="margin-top:0;">Renvoyer l’email de vérification</h2>
  <p class="muted">Entre ton email. On te renvoie un lien si ton compte existe et n’est pas encore vérifié.</p>

  <?php if ($errors): ?>
    <div class="error">
      <ul style="margin:0 0 0 18px;">
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

      <?php if ($previewLink): ?>
        <div class="spacer"></div>
        <div class="muted">Mode dev : lien</div>
        <div><a href="<?= e($previewLink) ?>"><?= e($previewLink) ?></a></div>
      <?php endif; ?>
    </div>
    <div class="spacer"></div>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label for="email">Email</label>
    <input id="email" name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>">
    <div class="spacer"></div>
    <button class="btn" type="submit">Renvoyer</button>
  </form>
</section>

<?php require dirname(__DIR__, 2) . '/footer.php'; ?>
