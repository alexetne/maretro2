<?php $pageTitle='Historique'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <form method="GET" class="filter-form grid">
        <div class="form-group">
            <label>Du</label><input type="date" name="start" value="<?= htmlspecialchars($_GET['start'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group">
            <label>Au</label><input type="date" name="end" value="<?= htmlspecialchars($_GET['end'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group">
            <label>Type d'entité</label><input type="text" name="entity" value="<?= htmlspecialchars($_GET['entity'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group">
            <label>Praticien</label><input type="text" name="practitioner" value="<?= htmlspecialchars($_GET['practitioner'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group">
            <label>Cabinet</label><input type="text" name="cabinet" value="<?= htmlspecialchars($_GET['cabinet'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <button class="btn btn-secondary" type="submit">Filtrer</button>
    </form>
</div>
<div class="card">
    <table class="table">
        <thead><tr><th>Date</th><th>Action</th><th>Entité</th><th>ID</th><th>Utilisateur</th><th>Détails</th></tr></thead>
        <tbody>
        <?php foreach (($history ?? []) as $h): ?>
            <tr>
                <td><?= htmlspecialchars($h['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($h['action'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($h['entity_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($h['entity_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($h['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><pre><?= htmlspecialchars(json_encode($h['new_values'] ?? [], JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8'); ?></pre></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
