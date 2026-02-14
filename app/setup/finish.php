<?php
declare(strict_types=1);

require __DIR__ . '/../../config.php';
function require_login(): void { if (empty($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; } }
require_login();

$pageTitle = 'Fin • Setup • Ma Rétro Podo';
require __DIR__ . '/../../header.php';

$pdo=pdo_conn();
$userId=(int)$_SESSION['user_id'];
$cabId=(int)($_SESSION['active_cabinet_id'] ?? 0);

if($cabId<=0){ header('Location: cabinet.php'); exit; }

$stmt=$pdo->prepare("SELECT id, name FROM cabinets WHERE id=:cid AND owner_user_id=:uid LIMIT 1");
$stmt->execute([':cid'=>$cabId, ':uid'=>$userId]);
$cab = $stmt->fetch();
if(!$cab){ unset($_SESSION['active_cabinet_id']); header('Location: cabinet.php'); exit; }

$counts = [];

$counts['payment_methods'] = (int)$pdo->prepare("SELECT COUNT(*) c FROM payment_methods WHERE cabinet_id=:cid")
  ->execute([':cid'=>$cabId]) ?: 0; // (avoid)
$stmt=$pdo->prepare("SELECT COUNT(*) c FROM payment_methods WHERE cabinet_id=:cid");
$stmt->execute([':cid'=>$cabId]); $counts['payment_methods']=(int)$stmt->fetch()['c'];

$stmt=$pdo->prepare("SELECT COUNT(*) c FROM cabinet_actors WHERE cabinet_id=:cid");
$stmt->execute([':cid'=>$cabId]); $counts['actors']=(int)$stmt->fetch()['c'];

$stmt=$pdo->prepare("SELECT COUNT(*) c FROM actor_shares WHERE cabinet_id=:cid AND is_active=1");
$stmt->execute([':cid'=>$cabId]); $counts['shares']=(int)$stmt->fetch()['c'];

$stmt=$pdo->prepare("SELECT COUNT(*) c FROM payment_methods WHERE cabinet_id=:cid AND default_collector_actor_id IS NOT NULL");
$stmt->execute([':cid'=>$cabId]); $counts['defaults']=(int)$stmt->fetch()['c'];

$stmt=$pdo->prepare("SELECT COUNT(*) c FROM payment_methods WHERE cabinet_id=:cid");
$stmt->execute([':cid'=>$cabId]); $pmTotal=(int)$stmt->fetch()['c'];
?>

<section class="card">
  <h2 style="margin-top:0;">Setup terminé ?</h2>
  <p class="muted">Cabinet actif : <strong><?= e((string)$cab['name']) ?></strong></p>

  <div class="spacer"></div>

  <div class="grid grid-2">
    <div class="card">
      <strong>Résumé</strong>
      <ul class="muted" style="margin:10px 0 0 18px;">
        <li>Moyens de paiement : <strong><?= (int)$counts['payment_methods'] ?></strong></li>
        <li>Acteurs : <strong><?= (int)$counts['actors'] ?></strong></li>
        <li>Parts actives : <strong><?= (int)$counts['shares'] ?></strong></li>
        <li>Encaisseurs par défaut : <strong><?= (int)$counts['defaults'] ?></strong> / <?= (int)$pmTotal ?></li>
      </ul>
    </div>

    <div class="card">
      <strong>Actions</strong>
      <div class="spacer"></div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn" href="payment_methods.php">Moyens de paiement</a>
        <a class="btn" href="actors.php">Acteurs</a>
        <a class="btn" href="collectors.php">Encaissements</a>
        <a class="btn" href="shares.php">Parts</a>
      </div>
      <div class="spacer"></div>
      <a class="btn" href="../../profile.php">Aller au profil</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../../footer.php'; ?>
