<?php
declare(strict_types=1);

require __DIR__ . '/../../config.php';
function require_login(): void { if (empty($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; } }
require_login();

$pageTitle = 'Parts • Setup • Ma Rétro Podo';
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

  if(!$errors && $action==='save'){
    $actorId=(int)($_POST['actor_id'] ?? 0);
    $shareType=(string)($_POST['share_type'] ?? 'percent');

    $percent = trim((string)($_POST['percent_share'] ?? ''));
    $fixed = trim((string)($_POST['fixed_amount_cents'] ?? ''));

    if(!in_array($shareType, ['percent','fixed_per_payment','mixed'], true)) $errors[]="Type invalide.";

    // validate actor belongs
    $st=$pdo->prepare("SELECT id FROM cabinet_actors WHERE id=:aid AND cabinet_id=:cid LIMIT 1");
    $st->execute([':aid'=>$actorId, ':cid'=>$cabId]);
    if(!$st->fetch()) $errors[]="Acteur invalide.";

    $percentVal = null;
    $fixedVal = null;

    if(!$errors){
      if($shareType === 'percent' || $shareType === 'mixed'){
        if($percent === '' || !is_numeric($percent)) $errors[]="Pourcentage requis.";
        else {
          $percentVal = (float)$percent;
          if($percentVal < 0 || $percentVal > 100) $errors[]="Pourcentage doit être entre 0 et 100.";
        }
      }
      if($shareType === 'fixed_per_payment' || $shareType === 'mixed'){
        if($fixed === '' || !ctype_digit($fixed)) $errors[]="Montant fixe (centimes) requis et doit être un entier >= 0.";
        else $fixedVal = (int)$fixed;
      }
    }

    if(!$errors){
      // désactive l'ancien barème actif (option simple)
      $pdo->prepare("UPDATE actor_shares SET is_active=0 WHERE cabinet_id=:cid AND actor_id=:aid")
          ->execute([':cid'=>$cabId, ':aid'=>$actorId]);

      $pdo->prepare("
        INSERT INTO actor_shares
          (cabinet_id, actor_id, share_type, percent_share, fixed_amount_cents, valid_from, valid_to, is_active)
        VALUES
          (:cid, :aid, :st, :p, :f, CURDATE(), NULL, 1)
      ")->execute([
        ':cid'=>$cabId, ':aid'=>$actorId, ':st'=>$shareType,
        ':p'=>$percentVal, ':f'=>$fixedVal
      ]);

      $success="Part enregistrée ✅";
    }
  }
}

$actors=$pdo->prepare("SELECT id, display_name, type, is_active FROM cabinet_actors WHERE cabinet_id=:cid ORDER BY type ASC, display_name ASC");
$actors->execute([':cid'=>$cabId]);
$actors=$actors->fetchAll();

$shares=$pdo->prepare("
  SELECT s.*
  FROM actor_shares s
  WHERE s.cabinet_id=:cid AND s.is_active=1
");
$shares->execute([':cid'=>$cabId]);
$shareRows=$shares->fetchAll();
$shareMap=[];
foreach($shareRows as $r){ $shareMap[(int)$r['actor_id']] = $r; }
?>

<section class="card">
  <h2 style="margin-top:0;">Parts par acteur</h2>
  <p class="muted">Définis combien garde chaque acteur (ex: 70%).</p>

  <?php if($errors): ?><div class="error"><ul style="margin:0 0 0 18px;"><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div><div class="spacer"></div><?php endif; ?>
  <?php if($success): ?><div class="success"><strong><?= e($success) ?></strong></div><div class="spacer"></div><?php endif; ?>

  <?php if(!$actors): ?>
    <div class="muted">Ajoute des acteurs d’abord.</div>
    <div class="spacer"></div>
    <a class="btn" href="actors.php">Aller aux acteurs</a>
  <?php else: ?>
    <?php foreach($actors as $a): ?>
      <?php $cur = $shareMap[(int)$a['id']] ?? null; ?>
      <div class="card" style="margin-bottom:14px;">
        <strong><?= e((string)$a['display_name']) ?></strong>
        <div class="muted" style="font-size:13px;">
          <?= e((string)$a['type']) ?>
          <?php if($cur): ?>
            — Actuel: <?= e((string)$cur['share_type']) ?>
            <?php if($cur['percent_share'] !== null): ?> • <?= e((string)$cur['percent_share']) ?>%<?php endif; ?>
            <?php if($cur['fixed_amount_cents'] !== null): ?> • +<?= e((string)$cur['fixed_amount_cents']) ?>c<?php endif; ?>
          <?php else: ?>
            — Aucun barème
          <?php endif; ?>
        </div>

        <div class="spacer"></div>

        <form method="post" style="display:flex; gap:10px; flex-wrap:wrap; align-items:end;">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="action" value="save">
          <input type="hidden" name="actor_id" value="<?= (int)$a['id'] ?>">

          <div style="min-width:220px;">
            <label>Type</label>
            <select name="share_type">
              <option value="percent">Pourcentage</option>
              <option value="fixed_per_payment">Fixe par paiement</option>
              <option value="mixed">Mix</option>
            </select>
          </div>

          <div style="min-width:160px;">
            <label>% (si applicable)</label>
            <input name="percent_share" placeholder="ex: 70.00">
          </div>

          <div style="min-width:200px;">
            <label>Fixe (centimes, si applicable)</label>
            <input name="fixed_amount_cents" placeholder="ex: 500 (5€)">
          </div>

          <button class="btn" type="submit">Enregistrer</button>
        </form>
      </div>
    <?php endforeach; ?>

    <a class="btn" href="finish.php">Terminer</a>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/../../footer.php'; ?>
