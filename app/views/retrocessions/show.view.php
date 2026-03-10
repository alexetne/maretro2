<?php $pageTitle='Rétrocession'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <p>Encaissement #: <?= htmlspecialchars($retrocession['receipt_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Base : <?= htmlspecialchars($retrocession['base_amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?> €</p>
    <p>Rétrocession : <?= htmlspecialchars($retrocession['retrocession_amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?> €</p>
    <p>Conservé praticien : <?= htmlspecialchars($retrocession['practitioner_kept_amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?> €</p>
    <p>Statut : <span class="badge"><?= htmlspecialchars($retrocession['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></p>
    <?php if (!empty($canRecalc)): ?>
        <form method="POST" action="/retrocessions/<?= (int)$retrocession['receipt_id']; ?>/recalculate">
            <?= csrfInputField(); ?>
            <button class="btn btn-secondary" type="submit">Recalculer</button>
        </form>
    <?php endif; ?>
</div>
<?php if (!empty($payments)): ?>
<div class="card">
    <h4>Paiements</h4>
    <table class="table">
        <thead><tr><th>Date</th><th>Montant</th><th>Méthode</th><th>Référence</th></tr></thead>
        <tbody>
        <?php foreach ($payments as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['payment_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($p['amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?> €</td>
                <td><?= htmlspecialchars($p['payment_method'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($p['reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
