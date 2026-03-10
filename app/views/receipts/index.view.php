<?php $pageTitle='Encaissements'; $actions=[['href'=>'/receipts/create','label'=>'Nouvel encaissement']]; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <form method="GET" class="filter-form">
        <div class="form-group inline">
            <label>Du</label><input type="date" name="start" value="<?= htmlspecialchars($_GET['start'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <label>Au</label><input type="date" name="end" value="<?= htmlspecialchars($_GET['end'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <label>Praticien</label><input type="text" name="practitioner" value="<?= htmlspecialchars($_GET['practitioner'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <label>Cabinet</label><input type="text" name="cabinet" value="<?= htmlspecialchars($_GET['cabinet'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <button class="btn btn-secondary" type="submit">Filtrer</button>
        </div>
    </form>
</div>
<div class="card">
    <table class="table">
        <thead><tr><th>Date</th><th>Acte</th><th>Montant</th><th>Praticien</th><th>Rétrocession</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach (($receipts ?? []) as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['receipt_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($r['act_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($r['amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?> €</td>
                <td><?= htmlspecialchars($r['practitioner_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($r['retro_status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a class="btn" href="/receipts/<?= (int)$r['id']; ?>">Voir</a>
                    <a class="btn" href="/receipts/<?= (int)$r['id']; ?>/edit">Éditer</a>
                    <form method="POST" action="/receipts/<?= (int)$r['id']; ?>/delete" class="inline">
                        <?= csrfInputField(); ?>
                        <button class="btn btn-danger" type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
