<?php
declare(strict_types=1);

require __DIR__ . '/../../config.php';
function require_login(): void { if (empty($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; } }
require_login();

$pageTitle = 'Cabinet • Setup • Ma Rétro Podo';
require __DIR__ . '/../../header.php';

$pdo = pdo_conn();
$userId = (int)$_SESSION['user_id'];
$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $csrf = (string)($_POST['csrf_token'] ?? '');
  if (!csrf_check($csrf)) $errors[] = "Session expirée (CSRF).";

  $action = (string)($_POST['action'] ?? '');

  if (!$errors && $action === 'create') {
    $name = trim((string)($_POST['name'] ?? ''));
    if ($name === '' || mb_strlen($name) > 150) $errors[] = "Nom de cabinet invalide.";
    if (!$errors) {
      $stmt = $pdo->prepare("INSERT INTO cabinets (owner_user_id, name, is_active) VALUES (:uid, :n, 1)");
      $stmt->execute([':uid'=>$userId, ':n'=>$name]);
      $cabId = (int)$pdo->lastInsertId();
      $_SESSION['active_cabinet_id'] = $cabId;

      // log
      $stmt = $pdo->prepare("
        INSERT INTO cabinet_logs (cabinet_id, user_id, action, details)
        VALUES (:cid, :uid, 'create', JSON_OBJECT('source','setup/cabinet.php'))
      ");
      $stmt->execute([':cid'=>$cabId, ':uid'=>$userId]);

      $success = "Cabinet créé ✅ (défini comme actif).";
    }
  }

  if (!$errors && $action === 'select') {
    $cabinetId = (int)($_POST['cabinet_id'] ?? 0);
    $stmt = $pdo->prepare("SELECT id FROM cabinets WHERE id=:id AND owner_user_id=:uid LIMIT 1");
    $stmt->execute([':id'=>$cabinetId, ':uid'=>$userId]);
    if (!$stmt->fetch()) {
      $errors[] = "Cabinet introuvable.";
    } else {
      $_SESSION['active_cabinet_id'] = $cabinetId;
      $success = "Cabinet actif mis à jour ✅";
    }
  }
}

$stmt = $pdo->prepare("SELECT id, name, is_active, created_at FROM cabinets WHERE owner_user_id=:uid ORDER BY id DESC");
$stmt->execute([':uid'=>$userId]);
$cabinets = $stmt->fetchAll();

$active = (int)($_SESSION['active_cabinet_id'] ?? 0);
?>

<section class="card">
  <h2 style="margin-top:0;">Cabinet</h2>
  <p class="muted">Crée un cabinet (ou sélectionne celui à configurer).</p>

  <?php if ($errors): ?>
    <div class="error"><ul style="margin:0 0 0 18px;"><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div>
    <div class="spacer"></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success"><strong><?= e($success) ?></strong></div>
    <div class="spacer"></div>
  <?php endif; ?>

  <div class="grid grid-2">
    <div class="card">
      <h3 style="margin-top:0;">Créer un cabinet</h3>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="create">
        <label for="name">Nom</label>
        <input id="name" name="name" required maxlength="150" placeholder="Cabinet principal">
        <div class="spacer"></div>
        <button class="btn" type="submit">Créer</button>
      </form>
    </div>

    <div class="card">
      <h3 style="margin-top:0;">Sélectionner le cabinet actif</h3>
      <?php if (!$cabinets): ?>
        <div class="muted">Aucun cabinet pour le moment.</div>
      <?php else: ?>
        <form method="post">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="action" value="select">
          <label for="cabinet_id">Cabinet</label>
          <select id="cabinet_id" name="cabinet_id">
            <?php foreach ($cabinets as $c): ?>
              <option value="<?= (int)$c['id'] ?>" <?= ((int)$c['id'] === $active) ? 'selected' : '' ?>>
                #<?= (int)$c['id'] ?> — <?= e((string)$c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="spacer"></div>
          <button class="btn" type="submit">Définir actif</button>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <div class="spacer"></div>
  <a class="btn" href="payment_methods.php">Continuer → Moyens de paiement</a>
</section>

<?php require __DIR__ . '/../../footer.php'; ?>
