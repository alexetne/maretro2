<?php $pageTitle='Paiements'; $actions=[['href'=>'/payments/create','label'=>'Nouveau paiement']]; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <form method="GET" class="filter-form inline">
        <label>Du</label><input type="date" name="start" value="<?= htmlspecialchars($_GET['start'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <label>Au</label><input type="date" name="end" value="<?= htmlspecialchars($_GET['end'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <label>Méthode</label><input type="text" name="method" value="<?= htmlspecialchars($_GET['method'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <button class="btn btn-secondary" type="submit">Filtrer</button>
    </form>
</div>
<div class="card">
    <table class="table">
        <thead><tr><th>Date</th><th>Montant</th><th>Méthode</th><th>Référence</th><th>Rétrocession</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach (($payments ?? []) as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['payment_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($p['amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?> €</td>
                <td><?= htmlspecialchars($p['payment_method'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($p['reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td>#<?= htmlspecialchars($p['retrocession_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><a class="btn" href="/payments/<?= (int)$p['id']; ?>">Voir</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
