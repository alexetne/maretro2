<?php $pageTitle='Paiement'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <p>Date : <?= htmlspecialchars($payment['payment_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Montant : <?= htmlspecialchars($payment['amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?> €</p>
    <p>Méthode : <?= htmlspecialchars($payment['payment_method'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Référence : <?= htmlspecialchars($payment['reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Commentaire : <?= htmlspecialchars($payment['comment'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    <?php if (!empty($payment['proof_path'])): ?>
        <p><a href="<?= htmlspecialchars($payment['proof_path'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">Voir justificatif</a></p>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
