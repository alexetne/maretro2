<?php
declare(strict_types=1);

require __DIR__ . '/../../config.php';

function require_login(): void {
  if (empty($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; }
}
require_login();

$pageTitle = 'Setup • Ma Rétro Podo';
require __DIR__ . '/../../header.php';

$userId = (int)$_SESSION['user_id'];
$pdo = pdo_conn();

// Si un cabinet actif existe et appartient bien au user, on l'utilise
$activeId = $_SESSION['active_cabinet_id'] ?? null;

if ($activeId) {
  $stmt = $pdo->prepare("SELECT id FROM cabinets WHERE id=:id AND owner_user_id=:uid LIMIT 1");
  $stmt->execute([':id' => (int)$activeId, ':uid' => $userId]);
  if (!$stmt->fetch()) unset($_SESSION['active_cabinet_id']);
}

// S'il n'y a pas de cabinet actif, on prend le premier cabinet du user (si existe)
if (empty($_SESSION['active_cabinet_id'])) {
  $stmt = $pdo->prepare("SELECT id FROM cabinets WHERE owner_user_id=:uid ORDER BY id ASC LIMIT 1");
  $stmt->execute([':uid' => $userId]);
  $row = $stmt->fetch();
  if ($row) $_SESSION['active_cabinet_id'] = (int)$row['id'];
}

?>
<section class="card">
  <h2 style="margin-top:0;">Setup du cabinet</h2>
  <p class="muted">Configure ton cabinet : moyens de paiement, acteurs, encaissements, parts.</p>

  <div class="spacer"></div>

  <div class="grid grid-2">
    <a class="card" href="cabinet.php" style="display:block;">
      <strong>1) Cabinet</strong>
      <div class="muted">Créer / sélectionner ton cabinet actif</div>
    </a>

    <a class="card" href="payment_methods.php" style="display:block;">
      <strong>2) Moyens de paiement</strong>
      <div class="muted">CB, espèces, carte vitale, tiers payant…</div>
    </a>

    <a class="card" href="actors.php" style="display:block;">
      <strong>3) Acteurs</strong>
      <div class="muted">Titulaire / contractuels</div>
    </a>

    <a class="card" href="collectors.php" style="display:block;">
      <strong>4) Qui encaisse ?</strong>
      <div class="muted">Par moyen de paiement : encaisseur par défaut + autorisés</div>
    </a>

    <a class="card" href="shares.php" style="display:block;">
      <strong>5) Parts</strong>
      <div class="muted">Combien garde chaque acteur</div>
    </a>

    <a class="card" href="finish.php" style="display:block;">
      <strong>6) Terminer</strong>
      <div class="muted">Vérifier et finir le setup</div>
    </a>
  </div>
</section>

<?php require __DIR__ . '/../../footer.php'; ?>
