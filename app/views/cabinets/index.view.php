<?php $pageTitle = 'Cabinets'; $actions=[['href'=>'/cabinets/create','label'=>'Nouveau']]; include __DIR__ . '/../layouts/app.php'; ?>
<div class="card">
    <table class="table">
        <thead><tr><th>Nom</th><th>Adresse</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach (($cabinets ?? []) as $cab): ?>
            <tr>
                <td><?= htmlspecialchars($cab['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?= htmlspecialchars($cab['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a class="btn btn-secondary" href="/cabinets/<?= (int)$cab['id']; ?>">Voir</a>
                    <a class="btn" href="/cabinets/<?= (int)$cab['id']; ?>/edit">Éditer</a>
                    <form method="POST" action="/cabinets/<?= (int)$cab['id']; ?>/delete" class="inline">
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
