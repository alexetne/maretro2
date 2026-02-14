<?php
declare(strict_types=1);

require __DIR__ . '/../../config.php';
function require_login(): void { if (empty($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; } }
require_login();

$pageTitle = 'Encaissements • Setup • Ma Rétro Podo';
require __DIR__ . '/../../header.php';

$pdo=pdo_conn();
$userId=(int)$_SESSION['user_id'];
$cabId=(int)($_SESSION['active_cabinet_id'] ?? 0);
if($cabId<=0){ header('Location: cabinet.php'); exit; }

$stmt=$pdo->prepare("SELECT id FROM cabinets WHERE id=:cid AND owner_user_id=:uid LIMIT 1");
$stmt->execute([':cid'=>$cabId, ':uid'=>$userId]);
if(!$stmt->fetch()){ unset($_SESSION['active_cabinet_id']); header('Location: cabinet.php'); exit; }

$errors=[]; $success=null;

if($_SERVER['REQUEST_METHOD']==='POST'){
  if(!csrf_check((string)($_POST['csrf_token'] ?? ''))) $errors[]="Session expirée (CSRF).";
  $action=(string)($_POST['action'] ?? '');

  if(!$errors && $action==='set_default'){
    $pmId=(int)($_POST['payment_method_id'] ?? 0);
    $actorId=(int)($_POST['default_actor_id'] ?? 0);

    // vérif que les 2 appartiennent au cabinet
    $stmt=$pdo->prepare("
      SELECT 1
      FROM payment_methods pm
      JOIN cabinet_actors ca ON ca.cabinet_id = pm.cabinet_id
      WHERE pm.id=:pm AND pm.cabinet_id=:cid AND ca.id=:aid
      LIMIT 1
    ");
    $stmt->execute([':pm'=>$pmId, ':cid'=>$cabId, ':aid'=>$actorId]);
    if(!$stmt->fetch()){
      $errors[]="Sélection invalide (cabinet).";
    } else {
      $stmt=$pdo->prepare("UPDATE payment_methods SET default_collector_actor_id=:aid WHERE id=:pm AND cabinet_id=:cid");
      $stmt->execute([':aid'=>$actorId, ':pm'=>$pmId, ':cid'=>$cabId]);

      // s'assurer que l'acteur est autorisé aussi
      $stmt=$pdo->prepare("
        INSERT INTO payment_method_collectors (payment_method_id, actor_id, is_default)
        VALUES (:pm, :aid, 1)
        ON DUPLICATE KEY UPDATE is_default = 1
      ");
      $stmt->execute([':pm'=>$pmId, ':aid'=>$actorId]);

      $success="Encaisseur par défaut mis à jour ✅";
    }
  }

  if(!$errors && $action==='toggle_allowed'){
    $pmId=(int)($_POST['payment_method_id'] ?? 0);
    $actorId=(int)($_POST['actor_id'] ?? 0);

    // appartient au cabinet ?
    $stmt=$pdo->prepare("
      SELECT 1
      FROM payment_methods pm
      JOIN cabinet_actors ca ON ca.cabinet_id = pm.cabinet_id
      WHERE pm.id=:pm AND pm.cabinet_id=:cid AND ca.id=:aid
      LIMIT 1
    ");
    $stmt->execute([':pm'=>$pmId, ':cid'=>$cabId, ':aid'=>$actorId]);
    if(!$stmt->fetch()){
      $errors[]="Sélection invalide (cabinet).";
    } else {
      // si existe => delete, sinon insert
      $stmt=$pdo->prepare("SELECT 1 FROM payment_method_collectors WHERE payment_method_id=:pm AND actor_id=:aid");
      $stmt->execute([':pm'=>$pmId, ':aid'=>$actorId]);

      if($stmt->fetch()){
        // si l'acteur est default du moyen => refuser suppression
        $stmt2=$pdo->prepare("SELECT default_collector_actor_id FROM payment_methods WHERE id=:pm AND cabinet_id=:cid");
        $stmt2->execute([':pm'=>$pmId, ':cid'=>$cabId]);
        $row=$stmt2->fetch();
        if($row && (int)$row['default_collector_actor_id'] === $actorId){
          $errors[]="Impossible : cet acteur est l’encaisseur par défaut.";
        } else {
          $pdo->prepare("DELETE FROM payment_method_collectors WHERE payment_method_id=:pm AND actor_id=:aid")
              ->execute([':pm'=>$pmId, ':aid'=>$actorId]);
          $success="Autorisation retirée ✅";
        }
      } else {
        $pdo->prepare("INSERT INTO payment_method_collectors (payment_method_id, actor_id, is_default) VALUES (:pm,:aid,0)")
            ->execute([':pm'=>$pmId, ':aid'=>$actorId]);
        $success="Autorisation ajoutée ✅";
      }
    }
  }
}

$pm=$pdo->prepare("
  SELECT id, type, label, is_active, default_collector_actor_id
  FROM payment_methods
  WHERE cabinet_id=:cid
  ORDER BY id DESC
");
$pm->execute([':cid'=>$cabId]);
$methods=$pm->fetchAll();

$ac=$pdo->prepare("SELECT id, display_name, type, is_active FROM cabinet_actors WHERE cabinet_id=:cid AND is_active=1 ORDER BY type ASC, display_name ASC");
$ac->execute([':cid'=>$cabId]);
$actors=$ac->fetchAll();

// allowed map
$allowed = [];
$stmt=$pdo->prepare("
  SELECT payment_method_id, actor_id
  FROM payment_method_collectors pmc
  JOIN payment_methods pm ON pm.id = pmc.payment_method_id
  WHERE pm.cabinet_id = :cid
");
$stmt->execute([':cid'=>$cabId]);
foreach($stmt->fetchAll() as $r){
  $allowed[(int)$r['payment_method_id']][(int)$r['actor_id']] = true;
}

$labels = [
  'carte_bancaire'=>'Carte bancaire','especes'=>'Espèces','cheque'=>'Chèque','virement'=>'Virement',
  'carte_vitale'=>'Carte Vitale','tiers_payant'=>'Tiers payant','mutuelle'=>'Mutuelle','autre'=>'Autre'
];
?>

<section class="card">
  <h2 style="margin-top:0;">Qui encaisse ?</h2>
  <p class="muted">Définis l’encaisseur par défaut et qui est autorisé, par moyen de paiement.</p>

  <?php if($errors): ?><div class="error"><ul style="margin:0 0 0 18px;"><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div><div class="spacer"></div><?php endif; ?>
  <?php if($success): ?><div class="success"><strong><?= e($success) ?></strong></div><div class="spacer"></div><?php endif; ?>

  <?php if(!$methods): ?>
    <div class="muted">Ajoute d’abord des moyens de paiement.</div>
    <div class="spacer"></div>
    <a class="btn" href="payment_methods.php">Aller aux moyens de paiement</a>
  <?php elseif(!$actors): ?>
    <div class="muted">Ajoute d’abord des acteurs.</div>
    <div class="spacer"></div>
    <a class="btn" href="actors.php">Aller aux acteurs</a>
  <?php else: ?>
    <?php foreach($methods as $m): ?>
      <div class="card" style="margin-bottom:14px;">
        <strong><?= e($labels[$m['type']] ?? (string)$m['type']) ?></strong>
        <?php if(!empty($m['label'])): ?> — <?= e((string)$m['label']) ?><?php endif; ?>
        <div class="muted" style="font-size:13px;">
          <?= (int)$m['is_active'] ? 'Actif' : 'Inactif' ?>
        </div>

        <div class="spacer"></div>

        <form method="post" style="display:flex; gap:10px; align-items:end; flex-wrap:wrap;">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="action" value="set_default">
          <input type="hidden" name="payment_method_id" value="<?= (int)$m['id'] ?>">

          <div style="min-width:260px;">
            <label>Encaisseur par défaut</label>
            <select name="default_actor_id" required>
              <?php foreach($actors as $a): ?>
                <option value="<?= (int)$a['id'] ?>" <?= ((int)$m['default_collector_actor_id']===(int)$a['id'])?'selected':'' ?>>
                  <?= e((string)$a['display_name']) ?> (<?= e((string)$a['type']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <button class="btn" type="submit">Enregistrer</button>
        </form>

        <div class="spacer"></div>
        <div class="muted">Acteurs autorisés :</div>

        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:10px;">
          <?php foreach($actors as $a): ?>
            <?php $isOn = !empty($allowed[(int)$m['id']][(int)$a['id']]); ?>
            <form method="post">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action" value="toggle_allowed">
              <input type="hidden" name="payment_method_id" value="<?= (int)$m['id'] ?>">
              <input type="hidden" name="actor_id" value="<?= (int)$a['id'] ?>">
              <button class="btn" type="submit" style="<?= $isOn ? '' : 'opacity:.55;' ?>">
                <?= $isOn ? '✓ ' : '+ ' ?><?= e((string)$a['display_name']) ?>
              </button>
            </form>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <a class="btn" href="shares.php">Continuer → Parts</a>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/../../footer.php'; ?>
