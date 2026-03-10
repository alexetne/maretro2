<?php $pageTitle='Audit logs'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <form method="GET" class="filter-form inline">
        <label>User</label><input type="text" name="user" value="<?= htmlspecialchars($_GET['user'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <label>Action</label><input type="text" name="action" value="<?= htmlspecialchars($_GET['action'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <label>Entité</label><input type="text" name="entity" value="<?= htmlspecialchars($_GET['entity'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <label>Du</label><input type="date" name="start" value="<?= htmlspecialchars($_GET['start'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <label>Au</label><input type="date" name="end" value="<?= htmlspecialchars($_GET['end'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <button class="btn btn-secondary" type="submit">Filtrer</button>
    </form>
</div>
<div class="card">
    <table class="table">
        <thead><tr><th>Date</th><th>User</th><th>Action</th><th>Entité</th><th>ID</th><th>IP</th></tr></thead>
        <tbody>
        <?php foreach (($logs ?? []) as $log): ?>
            <tr>
                <td><?= htmlspecialchars($log['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($log['user_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($log['action'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($log['entity_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($log['entity_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($log['ip_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
