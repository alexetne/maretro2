<?php
declare(strict_types=1);

require dirname(__DIR__, 2) . '/config.php';
$pageTitle = 'Vérification email • Ma Rétro Podo';
require dirname(__DIR__, 2) . '/header.php';

$token = (string)($_GET['token'] ?? '');
$token = trim($token);

$errors = [];
$success = null;

if ($token === '' || !preg_match('/^[a-f0-9]{40,128}$/i', $token)) {
  $errors[] = "Lien invalide.";
} else {
  $pdo = pdo_conn();
  $tokenHash = hash('sha256', $token);

  try {
    $pdo->beginTransaction();

    // Cherche un token valide (non utilisé + non expiré)
    $stmt = $pdo->prepare("
      SELECT evt.id AS token_id, evt.user_id, u.email_normalized
      FROM email_verification_tokens evt
      JOIN users u ON u.id = evt.user_id
      WHERE evt.token_hash = :th
        AND evt.used_at IS NULL
        AND evt.expires_at > NOW()
      LIMIT 1
      FOR UPDATE
    ");
    $stmt->execute([':th' => $tokenHash]);
    $row = $stmt->fetch();

    if (!$row) {
      $pdo->rollBack();
      $errors[] = "Ce lien est expiré ou a déjà été utilisé.";
      // log anonyme
      log_auth_event(null, 'email_verified', null, false, ['reason' => 'invalid_or_expired_token']);
    } else {
      $userId = (int)$row['user_id'];
      $emailNorm = (string)$row['email_normalized'];
      $tokenId = (int)$row['token_id'];

      // Marque token utilisé
      $stmt = $pdo->prepare("UPDATE email_verification_tokens SET used_at = NOW() WHERE id = :id");
      $stmt->execute([':id' => $tokenId]);

      // Marque email vérifié
      $stmt = $pdo->prepare("UPDATE users SET email_verified = 1 WHERE id = :uid");
      $stmt->execute([':uid' => $userId]);

      $pdo->commit();

      log_auth_event($userId, 'email_verified', $emailNorm, true, ['source' => 'verify_email.php']);

      $success = "Email vérifié ✅ Tu peux maintenant te connecter.";
    }
  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $errors[] = "Erreur serveur: " . $e->getMessage();
  }
}
?>

<section class="card">
  <h2 style="margin-top:0;">Vérification de ton email</h2>

  <?php if ($errors): ?>
    <div class="error">
      <strong>Impossible de valider :</strong>
      <ul style="margin:8px 0 0 18px;">
        <?php foreach ($errors as $err): ?>
          <li><?= e($err) ?></li>
        <?php endforeach; ?>
      </ul>
      <div class="spacer"></div>
      <a class="btn" href="verify_email_resend.php">Renvoyer un email de vérification</a>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success">
      <strong><?= e($success) ?></strong>
      <div class="spacer"></div>
      <!-- plus tard: login.php -->
      <a class="btn" href="register.php">Retour</a>
    </div>
  <?php endif; ?>
</section>

<?php require dirname(__DIR__, 2) . '/footer.php'; ?>
