<?php $pageTitle='Encaissement'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <p>Date : <?= htmlspecialchars($receipt['receipt_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Acte : <?= htmlspecialchars($receipt['act_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Montant : <?= htmlspecialchars($receipt['amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?> €</p>
    <p>Commentaire : <?= htmlspecialchars($receipt['comment'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
</div>
<?php if (!empty($retrocession)): ?>
<div class="card">
    <h4>Rétrocession liée</h4>
    <p>Montant rétro : <?= htmlspecialchars($retrocession['retrocession_amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?> €</p>
    <p>Statut : <span class="badge"><?= htmlspecialchars($retrocession['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></p>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
