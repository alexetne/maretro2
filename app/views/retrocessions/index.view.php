<?php $pageTitle='Rétrocessions'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <form method="GET" class="filter-form inline">
        <label>Statut</label><input type="text" name="status" value="<?= htmlspecialchars($_GET['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <label>Du</label><input type="date" name="start" value="<?= htmlspecialchars($_GET['start'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <label>Au</label><input type="date" name="end" value="<?= htmlspecialchars($_GET['end'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <button class="btn btn-secondary" type="submit">Filtrer</button>
    </form>
</div>
<div class="card">
    <table class="table">
        <thead><tr><th>Encaissement</th><th>Base</th><th>Rétro</th><th>Conservé</th><th>Statut</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach (($retrocessions ?? []) as $retro): ?>
            <tr>
                <td>#<?= htmlspecialchars($retro['receipt_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($retro['base_amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?> €</td>
                <td><?= htmlspecialchars($retro['retrocession_amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?> €</td>
                <td><?= htmlspecialchars($retro['practitioner_kept_amount'] ?? '', ENT_QUOTES, 'UTF-8'); ?> €</td>
                <td><span class="badge"><?= htmlspecialchars($retro['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></td>
                <td>
                    <a class="btn" href="/retrocessions/<?= (int)$retro['id']; ?>">Voir</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
