<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

function require_login(): void {
  if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
  }
}
require_login();

$pageTitle = 'Profil • Ma Rétro Podo';
require __DIR__ . '/header.php';

$pdo = pdo_conn();
$userId = (int)$_SESSION['user_id'];

$errors = [];
$success = null;

function load_user(PDO $pdo, int $userId): array {
  $stmt = $pdo->prepare("
    SELECT id, email, email_verified, first_name, last_name, role, status, created_at, last_login_at
    FROM users
    WHERE id = :uid
    LIMIT 1
  ");
  $stmt->execute([':uid' => $userId]);
  $u = $stmt->fetch();
  if (!$u) {
    // session invalide
    header('Location: logout.php');
    exit;
  }
  return $u;
}

$user = load_user($pdo, $userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $csrf = (string)($_POST['csrf_token'] ?? '');
  if (!csrf_check($csrf)) {
    $errors[] = "Session expirée (CSRF). Recharge la page.";
  } else {
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'update_profile') {
      $first = trim((string)($_POST['first_name'] ?? ''));
      $last  = trim((string)($_POST['last_name'] ?? ''));

      if (mb_strlen($first) > 100) $errors[] = "Prénom trop long.";
      if (mb_strlen($last) > 100) $errors[] = "Nom trop long.";

      if (!$errors) {
        $stmt = $pdo->prepare("
          UPDATE users SET first_name = :first, last_name = :last
          WHERE id = :uid
        ");
        $stmt->execute([
          ':first' => ($first !== '' ? $first : null),
          ':last'  => ($last !== '' ? $last : null),
          ':uid'   => $userId,
        ]);

        // log business
        $stmt = $pdo->prepare("
          INSERT INTO business_logs (cabinet_id, actor_id, event_type, details)
          VALUES (0, NULL, 'profile_update', JSON_OBJECT('user_id', :uid))
        ");
        // cabinet_id=0 => on n'a pas encore de notion "cabinet actif" côté session.
        // Si tu préfères, on peut supprimer ce log ou le faire dans auth_events.
        $stmt->execute([':uid' => $userId]);

        $success = "Profil mis à jour ✅";
        $user = load_user($pdo, $userId);
      }
    }

    if ($action === 'change_password') {
      $current = (string)($_POST['current_password'] ?? '');
      $p1 = (string)($_POST['new_password'] ?? '');
      $p2 = (string)($_POST['new_password_confirm'] ?? '');

      if (mb_strlen($p1) < 10) $errors[] = "Nouveau mot de passe trop court (10 caractères minimum).";
      if ($p1 !== $p2) $errors[] = "La confirmation ne correspond pas.";

      if (!$errors) {
        // Récupérer hash actuel
        $stmt = $pdo->prepare("SELECT password_hash, email_normalized FROM users WHERE id = :uid LIMIT 1");
        $stmt->execute([':uid' => $userId]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($current, (string)$row['password_hash'])) {
          $errors[] = "Mot de passe actuel incorrect.";
          log_auth_event($userId, 'login_failed', (string)($row['email_normalized'] ?? null), false, ['reason' => 'wrong_current_password_on_change']);
        } else {
          $pref = strtolower(envv('PASSWORD_HASH_ALGO', 'argon2id') ?? 'argon2id');
          $algo = (defined('PASSWORD_ARGON2ID') && $pref === 'argon2id') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;

          $newHash = password_hash($p1, $algo);

          $stmt = $pdo->prepare("UPDATE users SET password_hash = :ph WHERE id = :uid");
          $stmt->execute([':ph' => $newHash, ':uid' => $userId]);

          log_auth_event($userId, 'password_reset_success', normalize_email((string)$user['email']), true, ['source' => 'profile.php']);
          $success = "Mot de passe changé ✅";
        }
      }
    }
  }
}
?>

<section class="card">
  <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px;">
    <div>
      <h2 style="margin-top:0;">Mon profil</h2>
      <div class="muted">Connecté en tant que <strong><?= e((string)$user['email']) ?></strong></div>
    </div>
    <div>
      <a class="btn" href="logout.php">Se déconnecter</a>
    </div>
  </div>

  <div class="spacer"></div>

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
    <div class="success"><strong><?= e($success) ?></strong></div>
    <div class="spacer"></div>
  <?php endif; ?>

  <div class="grid grid-2">
    <div class="card">
      <h3 style="margin-top:0;">Infos</h3>
      <div class="muted">Email vérifié : <strong><?= ((int)$user['email_verified'] === 1) ? 'oui' : 'non' ?></strong></div>
      <div class="muted">Rôle : <strong><?= e((string)$user['role']) ?></strong></div>
      <div class="muted">Statut : <strong><?= e((string)$user['status']) ?></strong></div>
      <div class="muted">Créé le : <strong><?= e((string)$user['created_at']) ?></strong></div>
      <div class="muted">Dernière connexion : <strong><?= e((string)($user['last_login_at'] ?? '—')) ?></strong></div>
    </div>

    <div class="card">
      <h3 style="margin-top:0;">Modifier mon profil</h3>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="update_profile">

        <label for="first_name">Prénom</label>
        <input id="first_name" name="first_name" type="text" value="<?= e((string)($user['first_name'] ?? '')) ?>">

        <div class="spacer"></div>

        <label for="last_name">Nom</label>
        <input id="last_name" name="last_name" type="text" value="<?= e((string)($user['last_name'] ?? '')) ?>">

        <div class="spacer"></div>

        <button class="btn" type="submit">Enregistrer</button>
      </form>
    </div>
  </div>

  <div class="spacer"></div>

  <div class="card">
    <h3 style="margin-top:0;">Changer mon mot de passe</h3>
    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action" value="change_password">

      <div class="grid grid-2">
        <div>
          <label for="current_password">Mot de passe actuel</label>
          <input id="current_password" name="current_password" type="password" required>
        </div>
        <div></div>
      </div>

      <div class="spacer"></div>

      <div class="grid grid-2">
        <div>
          <label for="new_password">Nouveau mot de passe</label>
          <input id="new_password" name="new_password" type="password" required minlength="10">
        </div>
        <div>
          <label for="new_password_confirm">Confirmation</label>
          <input id="new_password_confirm" name="new_password_confirm" type="password" required minlength="10">
        </div>
      </div>

      <div class="spacer"></div>

      <button class="btn" type="submit">Changer le mot de passe</button>
    </form>
  </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>
