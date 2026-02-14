<?php
declare(strict_types=1);

require __DIR__ . '/../../config.php';
function require_login(): void { if (empty($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; } }
require_login();

$pageTitle = 'Acteurs • Setup • Ma Rétro Podo';
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

  if(!$errors && $action==='add'){
    $type=(string)($_POST['type'] ?? 'contractuel');
    $display=trim((string)($_POST['display_name'] ?? ''));
    $first=trim((string)($_POST['first_name'] ?? ''));
    $last=trim((string)($_POST['last_name'] ?? ''));
    $email=trim((string)($_POST['email'] ?? ''));
    if(!in_array($type, ['titulaire','contractuel'], true)) $errors[]="Type invalide.";
    if($display==='' || mb_strlen($display)>120) $errors[]="Nom affiché invalide.";
    if($email!=='' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[]="Email invalide.";

    if(!$errors){
      $stmt=$pdo->prepare("
        INSERT INTO cabinet_actors (cabinet_id, type, display_name, first_name, last_name, email, is_active)
        VALUES (:cid, :t, :d, :f, :l, :e, 1)
      ");
      $stmt->execute([
        ':cid'=>$cabId, ':t'=>$type, ':d'=>$display,
        ':f'=>($first!==''?$first:null),
        ':l'=>($last!==''?$last:null),
        ':e'=>($email!==''?$email:null),
      ]);
      $success="Acteur ajouté ✅";
    }
  }

  if(!$errors && $action==='toggle'){
    $id=(int)($_POST['id'] ?? 0);
    $stmt=$pdo->prepare("UPDATE cabinet_actors SET is_active = 1 - is_active WHERE id=:id AND cabinet_id=:cid");
    $stmt->execute([':id'=>$id, ':cid'=>$cabId]);
    $success="Mise à jour ✅";
  }

  if(!$errors && $action==='delete'){
    $id=(int)($_POST['id'] ?? 0);
    $stmt=$pdo->prepare("DELETE FROM cabinet_actors WHERE id=:id AND cabinet_id=:cid");
    $stmt->execute([':id'=>$id, ':cid'=>$cabId]);
    $success="Supprimé ✅";
  }
}

$stmt=$pdo->prepare("SELECT * FROM cabinet_actors WHERE cabinet_id=:cid ORDER BY id DESC");
$stmt->execute([':cid'=>$cabId]);
$actors=$stmt->fetchAll();
?>

<section class="card">
  <h2 style="margin-top:0;">Acteurs du cabinet</h2>
  <p class="muted">Cabinet actif #<?= (int)$cabId ?> — ajoute titulaire / contractuels.</p>

  <?php if($errors): ?><div class="error"><ul style="margin:0 0 0 18px;"><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div><div class="spacer"></div><?php endif; ?>
  <?php if($success): ?><div class="success"><strong><?= e($success) ?></strong></div><div class="spacer"></div><?php endif; ?>

  <div class="grid grid-2">
    <div class="card">
      <h3 style="margin-top:0;">Ajouter un acteur</h3>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="add">

        <label for="type">Type</label>
        <select id="type" name="type">
          <option value="titulaire">Titulaire</option>
          <option value="contractuel" selected>Contractuel</option>
        </select>

        <div class="spacer"></div>
        <label for="display_name">Nom affiché</label>
        <input id="display_name" name="display_name" required maxlength="120" placeholder="Ex: Dr Martin">

        <div class="spacer"></div>
        <div class="grid grid-2">
          <div>
            <label for="first_name">Prénom (opt.)</label>
            <input id="first_name" name="first_name" maxlength="100">
          </div>
          <div>
            <label for="last_name">Nom (opt.)</label>
            <input id="last_name" name="last_name" maxlength="100">
          </div>
        </div>

        <div class="spacer"></div>
        <label for="email">Email (opt.)</label>
        <input id="email" name="email" type="email">

        <div class="spacer"></div>
        <button class="btn" type="submit">Ajouter</button>
      </form>
    </div>

    <div class="card">
      <h3 style="margin-top:0;">Liste</h3>
      <?php if(!$actors): ?>
        <div class="muted">Aucun acteur.</div>
      <?php else: ?>
        <?php foreach($actors as $a): ?>
          <div style="border:1px solid var(--line); border-radius:12px; padding:12px; margin-bottom:10px;">
            <div style="display:flex; justify-content:space-between; gap:10px;">
              <div>
                <strong><?= e((string)$a['display_name']) ?></strong>
                <div class="muted" style="font-size:13px;">
                  <?= e((string)$a['type']) ?> • <?= (int)$a['is_active'] ? 'Actif' : 'Inactif' ?>
                </div>
              </div>
              <div style="display:flex; gap:8px;">
                <form method="post">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="action" value="toggle">
                  <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                  <button class="btn" type="submit"><?= (int)$a['is_active'] ? 'Désactiver' : 'Activer' ?></button>
                </form>
                <form method="post" onsubmit="return confirm('Supprimer cet acteur ?');">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                  <button class="btn" type="submit">Supprimer</button>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="spacer"></div>
  <a class="btn" href="collectors.php">Continuer → Qui encaisse ?</a>
</section>

<?php require __DIR__ . '/../../footer.php'; ?>
