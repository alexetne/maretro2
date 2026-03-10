<?php $pageTitle='Cabinets (Admin)'; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <table class="table">
        <thead><tr><th>Nom</th><th>Adresse</th><th>Responsable</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach (($cabinets ?? []) as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($c['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($c['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><a class="btn" href="/cabinets/<?= (int)$c['id']; ?>">Voir</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../layouts/app_end.php'; ?>
