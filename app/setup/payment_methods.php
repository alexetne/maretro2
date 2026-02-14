<?php
declare(strict_types=1);

require __DIR__ . '/../../config.php';
function require_login(): void { if (empty($_SESSION['user_id'])) { header('Location: ../../login.php'); exit; } }
require_login();

$pageTitle = 'Moyens de paiement • Setup • Ma Rétro Podo';
require __DIR__ . '/../../header.php';

$pdo = pdo_conn();
$userId = (int)$_SESSION['user_id'];
$cabId = (int)($_SESSION['active_cabinet_id'] ?? 0);

$errors=[]; $success=null;

if ($cabId <= 0) { header('Location: cabinet.php'); exit; }

// ownership check
$stmt = $pdo->prepare("SELECT id FROM cabinets WHERE id=:cid AND owner_user_id=:uid LIMIT 1");
$stmt->execute([':cid'=>$cabId, ':uid'=>$userId]);
if (!$stmt->fetch()) { unset($_SESSION['active_cabinet_id']); header('Location: cabinet.php'); exit; }

$types = [
  'carte_bancaire'=>'Carte bancaire',
  'especes'=>'Espèces',
  'cheque'=>'Chèque',
  'virement'=>'Virement',
  'carte_vitale'=>'Carte Vitale',
  'tiers_payant'=>'Tiers payant',
  'mutuelle'=>'Mutuelle',
  'autre'=>'Autre',
];

if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!csrf_check((string)($_POST['csrf_token'] ?? ''))) $errors[]="Session expirée (CSRF).";
  $action = (string)($_POST['action'] ?? '');

  if (!$errors && $action === 'add') {
    $type = (string)($_POST['type'] ?? '');
    $label = trim((string)($_POST['label'] ?? ''));
    $desc = trim((string)($_POST['description'] ?? ''));
    $secu = isset($_POST['remboursement_secu']) ? 1 : 0;
    $mut = isset($_POST['remboursement_mutuelle']) ? 1 : 0;
    $tp  = isset($_POST['tiers_payant_integral']) ? 1 : 0;

    if (!isset($types[$type])) $errors[]="Type invalide.";
    if (!$errors) {
      $stmt=$pdo->prepare("
        INSERT INTO payment_methods
          (cabinet_id, type, label, description, is_active, remboursement_secu, remboursement_mutuelle, tiers_payant_integral)
        VALUES
          (:cid, :t, :l, :d, 1, :s, :m, :tp)
      ");
      $stmt->execute([
        ':cid'=>$cabId, ':t'=>$type,
        ':l'=>($label!==''?$label:null),
        ':d'=>($desc!==''?$desc:null),
        ':s'=>$secu, ':m'=>$mut, ':tp'=>$tp
      ]);
      $success="Moyen de paiement ajouté ✅";
    }
  }

  if (!$errors && $action === 'toggle') {
    $id=(int)($_POST['id'] ?? 0);
    $stmt=$pdo->prepare("UPDATE payment_methods SET is_active = 1 - is_active WHERE id=:id AND cabinet_id=:cid");
    $stmt->execute([':id'=>$id, ':cid'=>$cabId]);
    $success="Mise à jour ✅";
  }

  if (!$errors && $action === 'delete') {
    $id=(int)($_POST['id'] ?? 0);
    $stmt=$pdo->prepare("DELETE FROM payment_methods WHERE id=:id AND cabinet_id=:cid");
    $stmt->execute([':id'=>$id, ':cid'=>$cabId]);
    $success="Supprimé ✅";
  }
}

$stmt=$pdo->prepare("SELECT * FROM payment_methods WHERE cabinet_id=:cid ORDER BY id DESC");
$stmt->execute([':cid'=>$cabId]);
$methods=$stmt->fetchAll();
?>

<section class="card">
  <h2 style="margin-top:0;">Moyens de paiement</h2>
  <p class="muted">Cabinet actif #<?= (int)$cabId ?> — configure ce que tu acceptes.</p>

  <?php if ($errors): ?><div class="error"><ul style="margin:0 0 0 18px;"><?php foreach($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div><div class="spacer"></div><?php endif; ?>
  <?php if ($success): ?><div class="success"><strong><?= e($success) ?></strong></div><div class="spacer"></div><?php endif; ?>

  <div class="grid grid-2">
    <div class="card">
      <h3 style="margin-top:0;">Ajouter</h3>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="add">

        <label for="type">Type</label>
        <select id="type" name="type" required>
          <?php foreach($types as $k=>$v): ?>
            <option value="<?= e($k) ?>"><?= e($v) ?></option>
          <?php endforeach; ?>
        </select>

        <div class="spacer"></div>
        <label for="label">Libellé (optionnel)</label>
        <input id="label" name="label" maxlength="120" placeholder="Ex: Carte Vitale + Mutuelle X">

        <div class="spacer"></div>
        <label for="description">Description (optionnel)</label>
        <textarea id="description" name="description" rows="3"></textarea>

        <div class="spacer"></div>
        <label><input type="checkbox" name="remboursement_secu"> Remboursement Sécu</label>
        <label><input type="checkbox" name="remboursement_mutuelle"> Remboursement Mutuelle</label>
        <label><input type="checkbox" name="tiers_payant_integral"> Tiers payant intégral</label>

        <div class="spacer"></div>
        <button class="btn" type="submit">Ajouter</button>
      </form>
    </div>

    <div class="card">
      <h3 style="margin-top:0;">Liste</h3>
      <?php if(!$methods): ?>
        <div class="muted">Aucun moyen de paiement.</div>
      <?php else: ?>
        <?php foreach($methods as $m): ?>
          <div style="border:1px solid var(--line); border-radius:12px; padding:12px; margin-bottom:10px;">
            <div style="display:flex; justify-content:space-between; gap:10px;">
              <div>
                <strong><?= e($types[$m['type']] ?? (string)$m['type']) ?></strong>
                <?php if(!empty($m['label'])): ?> — <?= e((string)$m['label']) ?><?php endif; ?>
                <div class="muted" style="font-size:13px;">
                  <?= (int)$m['is_active'] ? 'Actif' : 'Inactif' ?>
                  <?php if((int)$m['remboursement_secu']) echo ' • Sécu'; ?>
                  <?php if((int)$m['remboursement_mutuelle']) echo ' • Mutuelle'; ?>
                  <?php if((int)$m['tiers_payant_integral']) echo ' • TP intégral'; ?>
                </div>
              </div>
              <div style="display:flex; gap:8px;">
                <form method="post">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="action" value="toggle">
                  <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                  <button class="btn" type="submit"><?= (int)$m['is_active'] ? 'Désactiver' : 'Activer' ?></button>
                </form>
                <form method="post" onsubmit="return confirm('Supprimer ce moyen de paiement ?');">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                  <button class="btn" type="submit">Supprimer</button>
                </form>
              </div>
            </div>
            <?php if(!empty($m['description'])): ?>
              <div class="muted" style="margin-top:8px;"><?= e((string)$m['description']) ?></div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="spacer"></div>
  <a class="btn" href="actors.php">Continuer → Acteurs</a>
</section>

<?php require __DIR__ . '/../../footer.php'; ?>
